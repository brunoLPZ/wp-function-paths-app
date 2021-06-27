package wpfunctionpaths.plugin.util;

import org.springframework.stereotype.Component;
import wpfunctionpaths.plugin.request.DownloadRequest;
import wpfunctionpaths.runner.ScriptRunner;

import java.io.IOException;
import java.util.ArrayList;
import java.util.List;

@Component
public class DownloadRunner extends ScriptRunner {

  private static final String DOWNLOAD_PLUGIN_SCRIPT = "/opt/static-analyzer/download-plugin.sh";

  public Boolean downloadPlugin(DownloadRequest downloadRequest) throws IOException, InterruptedException {
    List<String> command = new ArrayList<>();
    command.add(DOWNLOAD_PLUGIN_SCRIPT);
    command.add(downloadRequest.getPlugin());
    if (downloadRequest.getVersion() != null && !downloadRequest.getVersion().isBlank()) {
      command.add(downloadRequest.getVersion());
    }
    return this.runSilentScript(command);
  }

}
