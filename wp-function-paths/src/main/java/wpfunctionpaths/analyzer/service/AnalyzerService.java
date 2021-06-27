package wpfunctionpaths.analyzer.service;

import java.io.File;
import java.io.IOException;
import java.util.ArrayList;
import java.util.List;
import java.util.Optional;

import lombok.extern.slf4j.Slf4j;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import wpfunctionpaths.analyzer.php.model.PhpFile;
import wpfunctionpaths.analyzer.php.tool.PhpRunner;
import wpfunctionpaths.analyzer.php.tool.PhpTokenProcessor;
import wpfunctionpaths.analyzer.request.PluginRequest;
import wpfunctionpaths.db.node.Plugin;
import wpfunctionpaths.db.repository.FileRepository;
import wpfunctionpaths.db.repository.PluginRepository;
import wpfunctionpaths.exception.NotFoundException;

@Service
@Slf4j
public class AnalyzerService {

  private final PhpRunner phpTokenExtractor;
  private final PhpTokenProcessor phpTokenProcessor;
  private final FileRepository fileRepository;
  private final PluginRepository pluginRepository;

  private static final String TESTS_BASE_DIR = "/opt/static-analyzer/test";

  @Autowired
  public AnalyzerService(PhpRunner phpTokenExtractor, PhpTokenProcessor phpTokenProcessor,
                         FileRepository fileRepository, PluginRepository pluginRepository) {
    this.phpTokenExtractor = phpTokenExtractor;
    this.phpTokenProcessor = phpTokenProcessor;
    this.fileRepository = fileRepository;
    this.pluginRepository = pluginRepository;
  }

  /**
   * Runs a complete scan for the specified plugin
   *
   * @param pluginRequest @see wpfunctionpaths.analyzer.request.PluginRequest
   * @return List of @see wpfunctionpaths.analyzer.php.model.PhpFile
   * @throws IOException
   * @throws InterruptedException
   */
  public Plugin scanPlugin(PluginRequest pluginRequest) throws IOException, InterruptedException {
    log.info("Retrieving Plugin from database");
    Optional<Plugin> optionalPlugin;
    if (pluginRequest.getVersion() != null && !pluginRequest.getVersion().isBlank()) {
      optionalPlugin = pluginRepository
        .findByPluginSlugAndVersion(pluginRequest.getPlugin(), pluginRequest.getVersion());
    } else {
      optionalPlugin = pluginRepository.findByPluginSlug(pluginRequest.getPlugin());
    }

    if (optionalPlugin.isEmpty()) {
      log.error("Plugin {} not found", pluginRequest.getPlugin());
      throw new NotFoundException("Plugin not found");
    }

    List<PhpFile> phpFiles = new ArrayList<>();

    // Scan all php files from plugin
    scanPluginDirAndExtractTokens(new File(optionalPlugin.get().getPath()), phpFiles);

    // Process tokens and save them to neo4j
    phpTokenProcessor.processAndSaveTokens(phpFiles);

    optionalPlugin.get().setIsScanned(true);

    return pluginRepository.save(optionalPlugin.get());

  }


  public Boolean deleteAllScannedFiles() {
    try {
      log.info("Cleaning database except Plugins");
      fileRepository.deleteAll();
      log.info("Marking all Plugins as not scanned");
      pluginRepository.markAsNotScanned();
      return true;
    } catch (Exception e) {
      log.error("Error cleaning database");
      return false;
    }
  }

  public List<PhpFile> runScanTests() throws IOException, InterruptedException {
    File pluginDir = new File(TESTS_BASE_DIR);
    List<PhpFile> phpFiles = new ArrayList<>();

    // Scan all php files from plugin
    scanPluginDirAndExtractTokens(pluginDir, phpFiles);

    // Process tokens and save them to neo4j
    phpTokenProcessor.processAndSaveTokens(phpFiles);

    return phpFiles;
  }

  /**
   * Calls static analyzer tool in PHP to extract tokens
   *
   * @param pluginDir Plugin directory
   * @param phpFiles  List of scanned PHP files
   * @throws IOException          if something goes wrong reading PHP files
   * @throws InterruptedException if something goes wrong reading PHP files
   */
  private void scanPluginDirAndExtractTokens(File pluginDir, List phpFiles)
    throws IOException, InterruptedException {
    if (pluginDir.exists() && pluginDir.isDirectory()) {
      for (File f : pluginDir.listFiles()) {
        if (f.isDirectory()) {
          scanPluginDirAndExtractTokens(f, phpFiles);
        } else if (f.getName().endsWith(".php")) {
          log.info("Starting static code analysis for file: {}", f.getAbsolutePath());
          phpFiles.add(phpTokenExtractor.extract(f.getAbsolutePath()));
        }
      }
    }
  }
}
