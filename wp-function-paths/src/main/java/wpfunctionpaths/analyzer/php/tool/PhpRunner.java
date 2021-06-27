package wpfunctionpaths.analyzer.php.tool;

import com.fasterxml.jackson.databind.ObjectMapper;
import java.io.IOException;
import java.util.Arrays;
import java.util.List;

import lombok.extern.slf4j.Slf4j;
import org.springframework.stereotype.Component;
import wpfunctionpaths.runner.ScriptRunner;
import wpfunctionpaths.analyzer.php.model.PhpFile;

@Component
@Slf4j
public class PhpRunner extends ScriptRunner {

  private static final String STATIC_ANALYZER_SCRIPT = "/opt/static-analyzer/main.php";

  public PhpFile extract(String path) throws IOException, InterruptedException {
    ObjectMapper objectMapper = new ObjectMapper();
    List<String> command = Arrays.asList("php", STATIC_ANALYZER_SCRIPT, path);
    return objectMapper.readValue(runOutputScript(command), PhpFile.class);
  }

}
