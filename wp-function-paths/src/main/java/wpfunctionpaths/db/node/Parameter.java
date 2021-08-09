package wpfunctionpaths.db.node;

import lombok.Data;
import org.springframework.data.neo4j.core.schema.Node;

@Node
@Data
public class Parameter extends BaseNode {

  private String name;
  private Long position;

  public static Parameter toNode(wpfunctionpaths.analyzer.php.model.Parameter parameter) {
    Parameter parameterNode = new Parameter();
    parameterNode.setUuid(parameter.getUuid());
    parameterNode.setName(parameter.getName());
    parameterNode.setPosition(parameter.getPosition());
    return parameterNode;
  }

}
