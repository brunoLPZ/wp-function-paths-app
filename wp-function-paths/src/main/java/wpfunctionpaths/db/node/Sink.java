package wpfunctionpaths.db.node;

import java.util.List;
import java.util.stream.Collectors;
import lombok.Data;
import org.springframework.data.neo4j.core.schema.Node;
import org.springframework.data.neo4j.core.schema.Relationship;

@Node
@Data
public class Sink extends BaseNode implements Callable {

  private String name;
  private String vulnerability;
  private Long line;

  @Relationship(type = "HAS_PARAM")
  private List<Parameter> params;

  public static Sink toNode(wpfunctionpaths.analyzer.php.model.InsecureCall insecureCall) {
    Sink sink = new Sink();
    sink.setUuid(insecureCall.getUuid());
    sink.setName(insecureCall.getName());
    sink.setVulnerability(insecureCall.getVulnerability());
    sink.setParams(insecureCall.getVarParams().stream().map(Parameter::toNode).collect(Collectors.toList()));
    sink.setLine(insecureCall.getLine());
    return sink;
  }
}
