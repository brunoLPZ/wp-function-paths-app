package wpfunctionpaths.db.repository;

import java.util.Optional;
import org.springframework.data.neo4j.repository.Neo4jRepository;
import org.springframework.data.neo4j.repository.query.Query;
import wpfunctionpaths.db.node.Sink;

public interface SinkRepository extends Neo4jRepository<Sink, Long> {
  Optional<Sink> findByUuid(String uuid);

}
