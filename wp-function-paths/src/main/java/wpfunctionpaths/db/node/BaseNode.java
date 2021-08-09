package wpfunctionpaths.db.node;

import lombok.Data;
import org.springframework.data.neo4j.core.schema.GeneratedValue;
import org.springframework.data.neo4j.core.schema.Id;

@Data
public abstract class BaseNode {

  @GeneratedValue
  @Id
  private Long id;
  private String uuid;
}
