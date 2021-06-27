package wpfunctionpaths.plugin.request;

import lombok.Data;

import java.util.List;

@Data
public class PluginRequest {

  private String slug;
  private List<String> versions;

}
