package wpfunctionpaths.analyzer.php.model;

import lombok.Data;

@Data
public class WpHook {

  private String uuid;
  private String name;
  private String type;
  private String triggeredFunction;

}
