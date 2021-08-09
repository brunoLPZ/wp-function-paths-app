package wpfunctionpaths.db.repository;

import java.util.Optional;
import org.springframework.data.neo4j.repository.Neo4jRepository;
import org.springframework.data.neo4j.repository.query.Query;
import org.springframework.stereotype.Repository;
import wpfunctionpaths.db.node.File;

@Repository
public interface FileRepository extends Neo4jRepository<File, Long> {

  Optional<File> findByUuid(String uuid);

  @Query("MATCH (file:File) WHERE id(file) = $fileId WITH file "
      + "MATCH (called) WHERE id(called) = $calledId "
      + "MERGE (file)-[:CALL]->(called) RETURN file")
  Optional<File> saveCallById(Long fileId, Long calledId);

  @Query("MATCH (n) WHERE NOT 'Plugin' IN labels(n) DETACH DELETE n")
  void deleteAll();

}
