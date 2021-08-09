package wpfunctionpaths.db.node;

import lombok.Data;
import org.springframework.data.neo4j.core.schema.Node;
import wpfunctionpaths.analyzer.php.model.PhpFunction;

@Node
@Data
public class Function extends CallableNode {

  public static Function toNode(PhpFunction phpFunction) {
    Function function = new Function();
    CallableNode.toNode(phpFunction, function);
    return function;
  }

}
