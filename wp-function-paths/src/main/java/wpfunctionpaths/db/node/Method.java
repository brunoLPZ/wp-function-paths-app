package wpfunctionpaths.db.node;

import lombok.Data;
import org.springframework.data.neo4j.core.schema.Node;
import wpfunctionpaths.analyzer.php.model.PhpMethod;

@Node
@Data
public class Method extends CallableNode {

  public static Method toNode(PhpMethod phpMethod) {
    Method method = new Method();
    CallableNode.toNode(phpMethod, method);
    return method;
  }

}
