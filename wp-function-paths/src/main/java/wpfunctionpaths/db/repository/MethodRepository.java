package wpfunctionpaths.db.repository;

import java.util.List;
import java.util.Optional;
import org.springframework.data.neo4j.repository.Neo4jRepository;
import org.springframework.data.neo4j.repository.query.Query;
import org.springframework.stereotype.Repository;
import wpfunctionpaths.db.node.Method;

@Repository
public interface MethodRepository extends Neo4jRepository<Method, Long> {

  Optional<Method> findByUuid(String uuid);

  @Query("MATCH (m:Method {name:$name})<-[:DEFINE]-(class:Class {name:$className}) RETURN m")
  List<Method> findByNameAndClassname(String name, String className);

  @Query("MATCH (m:Method {name:$name, paramCount:$params})<-[:DEFINE]-(class:Class {name:$className}) RETURN m")
  List<Method> findByNameAndClassnameAndParamCount(String name, String className, Long params);

  List<Method> findByName(String name);

  List<Method> findByNameAndParamCount(String name, Long params);
}
