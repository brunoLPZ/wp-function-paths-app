package wpfunctionpaths.db.repository;

import org.springframework.data.neo4j.repository.Neo4jRepository;
import org.springframework.data.neo4j.repository.query.Query;
import org.springframework.stereotype.Repository;
import wpfunctionpaths.db.node.Plugin;

import java.util.Optional;

@Repository
public interface PluginRepository extends Neo4jRepository<Plugin, Long> {

  Optional<Plugin> findByUuid(String uuid);
  Optional<Plugin> findByPluginSlug(String slug);
  Optional<Plugin> findByPluginSlugAndVersion(String slug, String version);

  @Query("MATCH (p:Plugin) WHERE p.isScanned = true SET p.isScanned = false")
  void markAsNotScanned();
}
