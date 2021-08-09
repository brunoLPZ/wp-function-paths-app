package wpfunctionpaths.db.node;

import lombok.Data;
import org.springframework.data.neo4j.core.schema.Node;
import org.springframework.data.neo4j.core.schema.Relationship;
import wpfunctionpaths.analyzer.php.model.InsecureCall;
import wpfunctionpaths.analyzer.php.model.PhpFile;

import java.util.List;
import java.util.stream.Collectors;

@Node
@Data
public class File extends BaseNode {

  private String path;
  private String fileName;
  @Relationship(type = "DEFINE")
  private List<Function> functions;
  @Relationship(type = "DEFINE")
  private List<Class> classes;
  @Relationship(type = "CALL")
  private List<Callable> calls;
  @Relationship(type = "HAS_HOOK")
  private List<Hook> hooks;
  @Relationship(type = "TAINT_CALL")
  private List<Callable> taints;
  private List<String> sanitizers;
  private Boolean isControlledByUser;

  public static File toNode(PhpFile phpFile) {
    File file = new File();
    file.setUuid(phpFile.getUuid());
    file.setPath(phpFile.getPath());
    file.setSanitizers(phpFile.getSanitizers());
    String[] pathParts = phpFile.getPath().split("/");
    file.setFileName(pathParts[pathParts.length - 1]);
    file.setFunctions(phpFile.getFunctions().stream()
      .map(Function::toNode)
      .collect(Collectors.toList()));
    file.setClasses(phpFile.getClasses().stream()
      .map(Class::toNode)
      .collect(Collectors.toList()));
    file.setCalls(phpFile.getInsecureCalls().stream()
      .distinct().map(Sink::toNode)
      .collect(Collectors.toList()));
    file.setHooks(phpFile.getHooks().stream()
      .map(Hook::toNode)
      .collect(Collectors.toList()));
    file.setIsControlledByUser(phpFile.getIsControlledByUser());
    return file;
  }

}
