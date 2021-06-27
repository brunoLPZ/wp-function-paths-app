package wpfunctionpaths.analyzer.php.model;

import lombok.Data;

import java.util.List;

@Data
public abstract class PhpCaller {
  private List<InsecureCall> insecureCalls;
  private List<PossibleCall> possibleCalls;
}
