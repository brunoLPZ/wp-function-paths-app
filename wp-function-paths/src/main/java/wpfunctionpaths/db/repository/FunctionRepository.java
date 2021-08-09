package wpfunctionpaths.db.repository;

import java.util.List;
import java.util.Optional;
import org.springframework.data.neo4j.repository.Neo4jRepository;
import org.springframework.data.neo4j.repository.query.Query;
import org.springframework.stereotype.Repository;
import wpfunctionpaths.db.node.Function;

@Repository
public interface FunctionRepository extends Neo4jRepository<Function, Long> {

  Optional<Function> findByUuid(String uuid);

  @Query("MATCH (caller) WHERE id(caller) = $callerId WITH caller "
      + "MATCH (called) WHERE id(called) = $calledId "
      + "MERGE (caller)-[:CALL]->(called)")
  void saveCallById(Long callerId, Long calledId);

  @Query(
    "MATCH (caller) WHERE id(caller) = $callerId WITH caller "
      + "MATCH (called) WHERE id(called) = $calledId "
      + "MERGE (caller)-[:TAINT_CALL]->(called)")
  void addTaintCall(Long callerId, Long calledId);

  @Query(
    "MATCH (caller) WHERE id(caller) = $callerId WITH caller "
      + "MATCH (called) WHERE id(called) = $calledId "
      + "MERGE (caller)-[:TAINT_CALL{positions:$positions}]->(called)")
  void addTaintCall(Long callerId, Long calledId, List<Long> positions);

  @Query(
    "MATCH (caller) WHERE id(caller) = $callerId WITH caller "
      + "MATCH (called) WHERE id(called) = $calledId "
      + "MERGE (caller)-[:TAINT_CALL{positions:$positions, condition:$condition}]->(called)")
  void addTaintCall(Long callerId, Long calledId, List<Long> positions, Long condition);

  List<Function> findByName(String name);

  List<Function> findByNameAndParamCount(String name, Long params);

}
