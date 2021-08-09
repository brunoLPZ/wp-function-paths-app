package wpfunctionpaths.db.repository;

import java.util.List;
import java.util.Optional;
import org.springframework.data.neo4j.repository.Neo4jRepository;
import org.springframework.data.neo4j.repository.query.Query;
import org.springframework.stereotype.Repository;
import wpfunctionpaths.db.node.Class;

@Repository
public interface ClassRepository extends Neo4jRepository<Class, Long> {

  Optional<Class> findByUuid(String uuid);

  @Query("MATCH (c:Class) WHERE id(c) = $classId WITH c "
      + "MATCH (parent:Class) WHERE id(parent) = $parentId "
      + "MERGE (c)-[:HAS_PARENT]->(parent) RETURN c")
  Optional<Class> saveParentById(Long classId, Long parentId);

  List<Class> findByName(String name);

}
