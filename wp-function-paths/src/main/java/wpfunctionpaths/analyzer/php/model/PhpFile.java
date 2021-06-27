package wpfunctionpaths.analyzer.php.model;

import java.util.List;
import lombok.Data;

@Data
public class PhpFile extends PhpCaller {

  private String uuid;
  private String path;
  private List<PhpFunction> functions;
  private List<PhpClass> classes;
  private List<WpHook> hooks;
  private List<String> sanitizers;
  private Boolean isControlledByUser;

}
