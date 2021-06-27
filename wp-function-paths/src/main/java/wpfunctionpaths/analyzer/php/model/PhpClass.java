package wpfunctionpaths.analyzer.php.model;

import java.util.List;
import lombok.Data;

@Data
public class PhpClass {

  private String uuid;
  private String name;
  private List<String> parentClasses;
  private Long startLine;
  private Long endLine;
  private Boolean isInterface;
  private List<PhpMethod> methods;

}
