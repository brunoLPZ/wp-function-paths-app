package wpfunctionpaths.analyzer.php.tool;

import lombok.extern.slf4j.Slf4j;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Component;
import wpfunctionpaths.analyzer.php.model.*;
import wpfunctionpaths.db.node.Class;
import wpfunctionpaths.db.node.*;
import wpfunctionpaths.db.repository.*;

import java.util.ArrayList;
import java.util.List;
import java.util.Optional;

@Component
@Slf4j
public class PhpTokenProcessor {

  private final FileRepository fileRepository;
  private final FunctionRepository functionRepository;
  private final MethodRepository methodRepository;
  private final ClassRepository classRepository;
  private final HookRepository hookRepository;
  private final SinkRepository sinkRepository;

  @Autowired
  public PhpTokenProcessor(
    FileRepository fileRepository,
    FunctionRepository functionRepository,
    MethodRepository methodRepository,
    ClassRepository classRepository,
    HookRepository hookRepository,
    SinkRepository sinkRepository) {
    this.fileRepository = fileRepository;
    this.functionRepository = functionRepository;
    this.methodRepository = methodRepository;
    this.classRepository = classRepository;
    this.hookRepository = hookRepository;
    this.sinkRepository = sinkRepository;
  }

  /**
   * Process all extracted tokens with the PHP analyzer tool to connect and save them to Neo4j
   *
   * @param phpFiles List of processed PHP files
   */
  public void processAndSaveTokens(List<PhpFile> phpFiles) {
    this.initialInsertion(phpFiles);
    this.addConnectionsBetweenNodes(phpFiles);
  }

  /**
   * Performs the initial insertion of nodes (without call and taint relationships)
   *
   * @param phpFiles List of processed PHP files
   */
  private void initialInsertion(List<PhpFile> phpFiles) {
    log.info("Performing initial insertion of nodes");
    List<File> files = new ArrayList<>();
    for (PhpFile phpFile : phpFiles) {
      files.add(File.toNode(phpFile));
    }
    fileRepository.saveAll(files);
  }

  /**
   * Establishes connection between two nodes
   *
   * @param phpFiles List of processed PHP files
   */
  private void addConnectionsBetweenNodes(List<PhpFile> phpFiles) {
    for (PhpFile phpFile : phpFiles) {
      Optional<File> fileOptional = fileRepository.findByUuid(phpFile.getUuid());
      if (fileOptional.isPresent()) {
        insertParentClasses(phpFile.getClasses());
        insertCallsFromFile(phpFile, fileOptional.get());
        insertCallsFromFunctions(phpFile.getFunctions());
        for (PhpClass phpClass : phpFile.getClasses()) {
          insertCallsFromMethods(phpClass.getMethods());
        }
        insertCallsFromHooks(phpFile.getHooks());
      }
    }
  }

  /**
   * Inserts parent classes for provided class if any
   *
   * @param phpClasses PHP class
   */
  private void insertParentClasses(List<PhpClass> phpClasses) {
    phpClasses.stream().filter(phpClass -> !phpClass.getParentClasses().isEmpty())
      .forEach(phpClass -> {
        log.info("Inserting parent classes for class: {}", phpClass.getName());
        Optional<Class> classOptional = classRepository
          .findByUuid(phpClass.getUuid());
        classOptional.ifPresent(clazz -> phpClass.getParentClasses().forEach(parentClassName -> {
          List<Class> parentClass = classRepository.findByName(parentClassName);
          if (parentClass.size() == 1) {
            classRepository.saveParentById(clazz.getId(), parentClass.get(0).getId());
          }
        }));
      });
  }

  /**
   * Inserts calls from provided file
   *
   * @param phpFile PHP file
   * @param file    File node
   */
  private void insertCallsFromFile(PhpFile phpFile, File file) {
    log.info("Inserting calls from file: {}", file.getFileName());
    insertInnerCalls(file, phpFile);
    insertSinks(file, phpFile);
  }

  /**
   * Inserts calls from provided functions and marks parameters as tainted if needed
   *
   * @param phpFunctions list of PHP functions
   */
  private void insertCallsFromFunctions(List<PhpFunction> phpFunctions) {
    for (PhpFunction phpFunction : phpFunctions) {
      log.info("Inserting calls from function: {}", phpFunction.getName());
      Optional<Function> functionOptional = functionRepository
        .findByUuid(phpFunction.getUuid());
      functionOptional.ifPresent(function -> {
        insertInnerCalls(function, phpFunction);
        insertSinks(function, phpFunction);
      });
    }
  }

  /**
   * Inserts calls from provided methods and marks parameters as tainted if needed
   *
   * @param phpMethods list of PHP methods
   */
  private void insertCallsFromMethods(List<PhpMethod> phpMethods) {
    for (PhpMethod phpMethod : phpMethods) {
      log.info("Inserting calls from method: {}", phpMethod.getName());
      Optional<Method> methodOptional = methodRepository
        .findByUuid(phpMethod.getUuid());
      methodOptional.ifPresent(method -> {
        insertInnerCalls(method, phpMethod);
        insertSinks(method, phpMethod);
      });
    }
  }

  /**
   * Inserts calls from provided caller node (file, function or method)
   *
   * @param callerNode caller node (file, function or method)
   * @param phpItem    php caller (file, function or method)
   */
  private void insertInnerCalls(BaseNode callerNode, PhpCaller phpItem) {
    for (PossibleCall possibleCall : phpItem.getPossibleCalls()) {
      List<CallableNode> callableNodes =
        getPossibleCallsWithParamCount(possibleCall.getClassName(), possibleCall.getName(), possibleCall.getParams());
      callableNodes.forEach(node -> {
        functionRepository.saveCallById(callerNode.getId(), node.getId());
        if (possibleCall.getIsTainted() && possibleCall.getCondition() != null) {
          functionRepository.addTaintCall(callerNode.getId(), node.getId(), possibleCall.getPositions(),
            possibleCall.getCondition());
        } else if (possibleCall.getIsTainted()) {
          functionRepository.addTaintCall(callerNode.getId(), node.getId(), possibleCall.getPositions());
        }
      });
    }
  }

  /**
   * Inserts sinks from provided caller node (file, function or method)
   *
   * @param callerNode caller node (file, function or method)
   * @param phpItem    php caller (file, function or method)
   */
  private void insertSinks(BaseNode callerNode, PhpCaller phpItem) {
    for (InsecureCall insecureCall : phpItem.getInsecureCalls()) {
      Optional<Sink> sinkOptional = sinkRepository.findByUuid(insecureCall.getUuid());
      sinkOptional.ifPresent(sink -> {
        if (insecureCall.getIsTainted()) {
          functionRepository.addTaintCall(callerNode.getId(), sink.getId());
        }
      });
    }
  }

  /**
   * Inserts calls from hooks
   *
   * @param wpHooks List of WordPress hooks
   */
  private void insertCallsFromHooks(List<WpHook> wpHooks) {
    wpHooks.forEach(wpHook -> {
      log.info("Inserting calls from hook: {}", wpHook.getName());
      Optional<Hook> hookOptional = hookRepository
        .findByUuid(wpHook.getUuid());
      hookOptional.ifPresent(hook -> {
        List<CallableNode> callableNodes = getPossibleCalls(wpHook.getTriggeredFunction());
        callableNodes.forEach(callableNode ->
          hookRepository.saveTriggeredFunctionById(hook.getId(), callableNode.getId()));
      });
    });
  }

  /**
   * Gets possible call to provided class name and called item name
   *
   * @param className class name
   * @param callName  called item name
   * @return Optional callable node (function or method)
   */
  private List<CallableNode> getPossibleCallsWithParamCount(String className, String callName, Long params) {
    List<Method> possibleMethods;
    List<CallableNode> possibleCalls = new ArrayList<>();
    if (className != null && !className.isBlank()) {
      possibleMethods = methodRepository
        .findByNameAndClassnameAndParamCount(callName, className, params);
    } else {
      possibleMethods = methodRepository.findByNameAndParamCount(callName, params);
    }
    List<Function> possibleFunctions = functionRepository.findByNameAndParamCount(callName, params);
    if (possibleMethods.size() == 1) {
      possibleCalls.add(possibleMethods.get(0));
    }
    if (possibleFunctions.size() == 1) {
      possibleCalls.add(possibleFunctions.get(0));
    }
    return possibleCalls;
  }

  /**
   * Gets possible call to provided called item name
   *
   * @param callName called item name
   * @return Optional callable node (function or method)
   */
  private List<CallableNode> getPossibleCalls(String callName) {
    List<CallableNode> callableNodes = new ArrayList<>();
    List<Method> possibleMethods = methodRepository.findByName(callName);
    List<Function> possibleFunctions = functionRepository.findByName(callName);
    if (possibleMethods.size() == 1) {
      callableNodes.add(possibleMethods.get(0));
    }
    if (possibleFunctions.size() == 1) {
      callableNodes.add(possibleFunctions.get(0));
    }
    return callableNodes;
  }


}
