package wpfunctionpaths.analyzer.php.model;

import lombok.Data;

import java.util.List;

@Data
public class PossibleCall {

  private String className;
  private String name;
  private Long line;
  private Long params;
  private List<Parameter> varParams;
  private Boolean isTainted;
  private List<Long> positions;
  private Long condition;

}
