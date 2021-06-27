package wpfunctionpaths.runner;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.util.Arrays;
import java.util.List;
import java.util.concurrent.TimeUnit;
import java.util.stream.Collectors;
import lombok.extern.slf4j.Slf4j;
import org.springframework.stereotype.Component;

@Component
@Slf4j
public abstract class ScriptRunner {

  protected String runOutputScript(List<String> command) throws IOException, InterruptedException {
    Process process = runCommand(command);
    String tokenAsString = new BufferedReader(new InputStreamReader(process.getInputStream())).lines()
        .collect(Collectors.joining("\n"));
    if (process.waitFor(2, TimeUnit.SECONDS)) {
      return tokenAsString;
    } else {
      return null;
    }
  }

  protected Boolean runSilentScript(List<String> command) throws IOException, InterruptedException {
    Process process = runCommand(command);
    new BufferedReader(new InputStreamReader(process.getInputStream())).lines()
      .collect(Collectors.joining("\n"));
    if (process.waitFor(2, TimeUnit.SECONDS)) {
      return true;
    } else {
      return false;
    }
  }

  private Process runCommand(List<String> command) throws IOException {
    return Runtime.getRuntime().exec(command.toArray(new String[command.size()]));
  }

}
