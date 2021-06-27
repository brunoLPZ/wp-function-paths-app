package wpfunctionpaths.analyzer.controller;

import java.io.IOException;
import java.util.List;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.*;
import wpfunctionpaths.analyzer.php.model.PhpFile;
import wpfunctionpaths.analyzer.request.PluginRequest;
import wpfunctionpaths.analyzer.service.AnalyzerService;
import wpfunctionpaths.db.node.Plugin;
import wpfunctionpaths.exception.ScanException;

@RequestMapping("/scanner")
@RestController()
public class AnalyzerController {

  private final AnalyzerService analyzerService;

  @Autowired
  public AnalyzerController(AnalyzerService analyzerService) {
    this.analyzerService = analyzerService;
  }

  @PostMapping
  private Plugin scanPlugin(@RequestBody PluginRequest pluginRequest) throws IOException, InterruptedException {
    if (!analyzerService.deleteAllScannedFiles()) {
      throw new ScanException("Error cleaning database before starting scan");
    }
    return analyzerService.scanPlugin(pluginRequest);
  }

  @DeleteMapping
  private Boolean deleteAll() {
    return analyzerService.deleteAllScannedFiles();
  }

  @PostMapping(path = "/tests")
  private List<PhpFile> runScanTests() throws IOException, InterruptedException {
    analyzerService.deleteAllScannedFiles();
    return analyzerService.runScanTests();
  }


}
