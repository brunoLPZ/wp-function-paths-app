package wpfunctionpaths.plugin.service;

import lombok.extern.slf4j.Slf4j;
import net.lingala.zip4j.ZipFile;
import net.lingala.zip4j.exception.ZipException;
import org.apache.commons.io.IOUtils;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import org.springframework.util.FileSystemUtils;
import org.springframework.web.multipart.MultipartFile;
import wpfunctionpaths.db.node.Plugin;
import wpfunctionpaths.db.repository.PluginRepository;
import wpfunctionpaths.exception.NotFoundException;
import wpfunctionpaths.exception.UploadException;
import wpfunctionpaths.plugin.request.PluginRequest;
import wpfunctionpaths.plugin.util.DownloadRunner;
import wpfunctionpaths.plugin.request.DownloadRequest;

import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.util.*;

@Service
@Slf4j
public class PluginService {

  private static final String PLUGIN_BASE_DIR = "/usr/plugins";

  private final DownloadRunner downloadRunner;
  private final PluginRepository pluginRepository;

  @Autowired
  public PluginService(DownloadRunner downloadRunner, PluginRepository pluginRepository) {
    this.downloadRunner = downloadRunner;
    this.pluginRepository = pluginRepository;
  }

  public Plugin downloadPlugin(DownloadRequest downloadRequest) throws IOException, InterruptedException {
    log.info("Retrieving Plugin from database");
    Optional<Plugin> optionalPlugin;
    if (downloadRequest.getVersion() != null && !downloadRequest.getVersion().isBlank()) {
      optionalPlugin = pluginRepository
        .findByPluginSlugAndVersion(downloadRequest.getPlugin(), downloadRequest.getVersion());
    } else {
      optionalPlugin = pluginRepository.findByPluginSlug(downloadRequest.getPlugin());
    }
    if (optionalPlugin.isPresent()) {
      log.info("Starting download for Plugin: {}", downloadRequest.getPlugin());
      Boolean isDownloaded = downloadRunner.downloadPlugin(downloadRequest);
      optionalPlugin.get().setIsDownloaded(isDownloaded);
      if (isDownloaded) {
        log.info("Starting PHP file count for Plugin: {}", downloadRequest.getPlugin());
        optionalPlugin.get().setFiles(countPhpFiles(new File(optionalPlugin.get().getPath())));
      }
      log.info("Updating status of Plugin: {}", downloadRequest.getPlugin());
      return pluginRepository.save(optionalPlugin.get());
    }
    log.error("Plugin {} not found", downloadRequest.getPlugin());
    throw new NotFoundException("Plugin not found");
  }

  public List<Plugin> getAllPlugins() throws IOException {
    log.info("Retrieving all Plugins from database");
    List<Plugin> plugins = pluginRepository.findAll();
    return plugins;
  }

  private Long countPhpFiles(File pluginDir) {
    Long count = 0L;
    if (pluginDir.exists() && pluginDir.isDirectory()) {
      for (File f : pluginDir.listFiles()) {
        if (f.isDirectory()) {
          count += countPhpFiles(f);
        } else if (f.getName().endsWith(".php")) {
          count++;
        }
      }
    }
    return count;
  }

  public void createPlugin(PluginRequest pluginRequest) {
    if (!pluginRequest.getVersions().isEmpty()) {
      pluginRequest.getVersions().forEach(version -> {
        Plugin plugin = new Plugin(pluginRequest.getSlug(), version);
        plugin.setIsFromSvn(true);
        Optional<Plugin> optionalPlugin = pluginRepository.findByPluginSlugAndVersion(pluginRequest.getSlug(), version);
        if (optionalPlugin.isEmpty()) {
          pluginRepository.save(plugin);
        }
      });
    } else {
      Plugin plugin = new Plugin(pluginRequest.getSlug());
      plugin.setIsFromSvn(true);
      Optional<Plugin> optionalPlugin = pluginRepository.findByPluginSlug(pluginRequest.getSlug());
      if (optionalPlugin.isEmpty()) {
        pluginRepository.save(plugin);
      }
    }
  }

  public Plugin uploadPlugin(MultipartFile file, String pluginName) throws IOException {
    log.info("Creating temporary ZIP file");
    File zip = File.createTempFile(UUID.randomUUID().toString(), "temp");
    FileOutputStream o = new FileOutputStream(zip);
    IOUtils.copy(file.getInputStream(), o);
    o.close();

    Boolean isUploaded = true;

    try {
      String pluginDirectory = PLUGIN_BASE_DIR + "/" + pluginName;
      log.info("Creating Plugin directory: {}", pluginDirectory);
      File pluginDir = new File(pluginDirectory);
      if (pluginDir.exists()) {
        log.error("Plugin directory {} already exists", pluginDirectory);
        throw new UploadException("Plugin already exists");
      }
      pluginDir.mkdir();
      ZipFile zipFile = new ZipFile(zip);
      log.info("Extracting ZIP contents");
      zipFile.extractAll(pluginDir.getPath());
    } catch (ZipException e) {
      isUploaded = false;
    } finally {
      log.info("Deleting ZIP file");
      zip.delete();
    }

    if (isUploaded) {
      log.info("ZIP file upload success. Saving Plugin {} to database", pluginName);
      Plugin plugin = new Plugin(pluginName);
      plugin.setIsDownloaded(true);
      plugin.setIsFromSvn(false);
      plugin.setFiles(countPhpFiles(new File(plugin.getPath())));
      pluginRepository.save(plugin);
      return plugin;
    } else {
      log.error("Error uploading ZIP file");
      throw new UploadException("Plugin upload went wrong");
    }

  }

  public void deletePlugin(String uuid) {
    log.info("Retrieving from database Plugin with UUID: {}", uuid);

    Optional<Plugin> optionalPlugin = pluginRepository.findByUuid(uuid);

    if (optionalPlugin.isEmpty()) {
      log.error("Plugin with UUID {} not found", uuid);
      throw new NotFoundException("Plugin not found");
    }

    log.info("Recursive deletion of Plugin directory");
    FileSystemUtils.deleteRecursively(new File(optionalPlugin.get().getPath()));

    if (!optionalPlugin.get().getIsFromSvn()) {
      log.info("Plugin was uploaded by user. Deleting from database");
      pluginRepository.delete(optionalPlugin.get());
    } else {
      log.info("Plugin is from SVN. Updating Plugin status");
      optionalPlugin.get().setIsDownloaded(false);
      optionalPlugin.get().setIsScanned(false);
      pluginRepository.save(optionalPlugin.get());
    }

  }
}
