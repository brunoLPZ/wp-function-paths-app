package wpfunctionpaths.db.node;

import lombok.Data;
import org.springframework.data.neo4j.core.schema.Node;
import org.springframework.data.neo4j.core.schema.Relationship;
import wpfunctionpaths.analyzer.php.model.WpHook;

@Node
@Data
public class Hook extends BaseNode {

  private String name;
  private String type;
  private String triggeredFunction;

  @Relationship(type = "TRIGGER")
  private Function function;

  public static Hook toNode(WpHook wpHook) {
    Hook hook = new Hook();
    hook.setUuid(wpHook.getUuid());
    hook.setName(wpHook.getName());
    hook.setType(wpHook.getType());
    hook.setTriggeredFunction(wpHook.getTriggeredFunction());
    return hook;
  }
}
