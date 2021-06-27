package wpfunctionpaths.analyzer.php.model;

import java.util.List;
import lombok.Data;

@Data
public class PhpCallable extends PhpCaller {
  private String uuid;
  private String name;
  private Long params;
  private List<Parameter> varParams;
  private Long startLine;
  private Long endLine;
  private Boolean isControlledByUser;
  private List<String> sanitizers;
}
