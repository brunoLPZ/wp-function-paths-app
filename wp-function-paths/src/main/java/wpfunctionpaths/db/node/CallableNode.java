package wpfunctionpaths.db.node;

import lombok.Data;
import org.springframework.data.neo4j.core.schema.Relationship;
import wpfunctionpaths.analyzer.php.model.InsecureCall;
import wpfunctionpaths.analyzer.php.model.PhpCallable;
import wpfunctionpaths.analyzer.php.model.PhpFunction;

import java.util.List;
import java.util.stream.Collectors;

@Data
public abstract class CallableNode extends BaseNode implements Callable {

  private String name;
  @Relationship(type = "HAS_PARAM")
  private List<Parameter> params;
  private Long paramCount;
  private Long startLine;
  private Long endLine;
  private Boolean isControlledByUser;
  private List<String> sanitizers;
  @Relationship(type = "CALL")
  private List<Callable> calls;
  @Relationship(type = "TAINT_CALL")
  private List<Callable> taints;

  protected static void toNode(PhpCallable phpCallable, CallableNode callableNode) {
    callableNode.setUuid(phpCallable.getUuid());
    callableNode.setEndLine(phpCallable.getEndLine());
    callableNode.setParams(phpCallable.getVarParams().stream().map(Parameter::toNode).collect(Collectors.toList()));
    callableNode.setCalls(phpCallable.getInsecureCalls().stream().distinct().map(Sink::toNode).collect(Collectors.toList()));
    callableNode.setParamCount(phpCallable.getParams());
    callableNode.setName(phpCallable.getName());
    callableNode.setIsControlledByUser(phpCallable.getIsControlledByUser());
    callableNode.setStartLine(phpCallable.getStartLine());
    callableNode.setSanitizers(phpCallable.getSanitizers());
  }

}
