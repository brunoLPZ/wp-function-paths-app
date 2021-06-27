package wpfunctionpaths.plugin.request;

import lombok.Data;

@Data
public class DownloadRequest {
  private String plugin;
  private String version;
}
