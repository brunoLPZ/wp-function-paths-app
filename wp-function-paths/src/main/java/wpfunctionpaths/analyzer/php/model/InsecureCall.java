package wpfunctionpaths.analyzer.php.model;

import java.util.List;
import java.util.Objects;

import lombok.Data;

@Data
public class InsecureCall {

  private String uuid;
  private String name;
  private String vulnerability;
  private Long params;
  private List<Parameter> varParams;
  private Long line;
  private Boolean isTainted;

  @Override
  public boolean equals(Object o) {
    if (this == o) return true;
    if (o == null || getClass() != o.getClass()) return false;
    InsecureCall that = (InsecureCall) o;
    return name.equals(that.name) && vulnerability.equals(that.vulnerability) && params.equals(that.params) && line.equals(that.line);
  }

  @Override
  public int hashCode() {
    return Objects.hash(name, vulnerability, params, line);
  }

}
