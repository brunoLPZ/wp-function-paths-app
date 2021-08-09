package wpfunctionpaths.db.node;

import java.util.List;
import java.util.stream.Collectors;
import lombok.Data;
import org.springframework.data.neo4j.core.schema.Node;
import org.springframework.data.neo4j.core.schema.Relationship;
import wpfunctionpaths.analyzer.php.model.PhpClass;

@Node
@Data
public class Class extends BaseNode {

  private String name;
  @Relationship(type = "HAS_PARENT")
  private List<Class> parentClasses;
  private Long startLine;
  private Long endLine;
  private Boolean isInterface;
  @Relationship(type = "DEFINE")
  private List<Method> methods;

  public static Class toNode(PhpClass phpClass) {
    Class clazz = new Class();
    clazz.setUuid(phpClass.getUuid());
    clazz.setEndLine(phpClass.getEndLine());
    clazz.setIsInterface(phpClass.getIsInterface());
    clazz.setMethods(phpClass.getMethods().stream().map(Method::toNode).collect(Collectors.toList()));
    clazz.setName(phpClass.getName());
    clazz.setStartLine(phpClass.getStartLine());
    return clazz;
  }

}
