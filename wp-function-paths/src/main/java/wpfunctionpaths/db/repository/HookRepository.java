package wpfunctionpaths.db.repository;

import java.util.Optional;
import org.springframework.data.neo4j.repository.Neo4jRepository;
import org.springframework.data.neo4j.repository.query.Query;
import org.springframework.stereotype.Repository;
import wpfunctionpaths.db.node.Hook;

@Repository
public interface HookRepository extends Neo4jRepository<Hook, Long> {

  Optional<Hook> findByUuid(String uuid);

  @Query("MATCH (hook:Hook) WHERE id(hook) = $hookId WITH hook "
      + "MATCH (function) WHERE id(function) = $functionId "
      + "MERGE (hook)-[:TRIGGER]->(function)"
      + "RETURN hook")
  Optional<Hook> saveTriggeredFunctionById(Long hookId, Long functionId);
}
