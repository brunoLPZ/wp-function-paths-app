package wpfunctionpaths.db.node;

import lombok.Data;
import org.springframework.data.neo4j.core.schema.Node;

import java.util.UUID;

@Node
@Data
public class Plugin extends BaseNode {

  private String pluginSlug;
  private String version;
  private String path;
  private Boolean isDownloaded;
  private Long files;
  private Boolean isScanned;
  private Boolean isFromSvn;

  public Plugin() {
  }

  public Plugin(String slug) {
    this.setUuid(UUID.randomUUID().toString());
    this.pluginSlug = slug;
    this.path = "/usr/plugins/" + this.pluginSlug;
  }

  public Plugin(String slug, String version) {
    this.setUuid(UUID.randomUUID().toString());
    this.pluginSlug = slug;
    this.version = version;
    this.path = "/usr/plugins/" + this.pluginSlug + "-" + this.version;
  }

}
