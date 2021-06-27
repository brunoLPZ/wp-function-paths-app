package wpfunctionpaths.plugin.controller;

import org.springframework.beans.factory.annotation.Autowired;

import org.springframework.web.bind.annotation.*;
import org.springframework.web.multipart.MultipartFile;
import wpfunctionpaths.plugin.request.DownloadRequest;
import wpfunctionpaths.db.node.Plugin;
import wpfunctionpaths.plugin.request.PluginRequest;
import wpfunctionpaths.plugin.service.PluginService;

import java.io.IOException;
import java.util.List;

@RequestMapping("/plugin")
@RestController()
public class PluginController {

  private final PluginService pluginService;

  @Autowired
  public PluginController(PluginService pluginService) {
    this.pluginService = pluginService;
  }


  @PostMapping(path = "")
  public void createPlugin(@RequestBody PluginRequest pluginRequest) throws IOException, InterruptedException {
    pluginService.createPlugin(pluginRequest);
  }

  @DeleteMapping(path = "/{pluginId}")
  public void deletePlugin(@PathVariable String pluginId) throws IOException, InterruptedException {
    pluginService.deletePlugin(pluginId);
  }

  @PostMapping(path = "/upload")
  public Plugin uploadPlugin(@RequestParam("file") MultipartFile file, @RequestParam String pluginName) throws IOException {
    return pluginService.uploadPlugin(file, pluginName);
  }

  @PostMapping(path = "/download")
  public Plugin downloadPlugin(@RequestBody DownloadRequest downloadRequest) throws IOException, InterruptedException {
    return pluginService.downloadPlugin(downloadRequest);
  }

  @GetMapping()
  public List<Plugin> getAllPlugins() throws IOException {
    return pluginService.getAllPlugins();
  }

}
