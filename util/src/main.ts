import { ModelMetadata } from "./ModelMetadata";

/**
 * Simple type definition for a command line option
 */
type CommandLineOption = {
  /**
   * The argument, prefixed by two dashes
   */
  argument: string;

  /**
   * A description, to be shown in the help output
   */
  text: string;
};

/**
 * Definition of all available command line options
 */
const commandLineOptions: CommandLineOption[] = [
  {
    argument: "help",
    text: "Displays this information.",
  },
  {
    argument: "verbose",
    text: "Dump intermediate and debug information.",
  },
  {
    argument: "check",
    text: "Checks consistency of the asset directory files.",
  },
  {
    argument: "update",
    text: 'Update model folders. It will set "check", and not perform any updates if the check fails.',
  },
  {
    argument: "dry-run",
    text: "Option to perform all checks and updates, but not write out actual files",
  },
  {
    argument: "process-repo",
    text: 'Processes all models that are found in the "./Models" subdirectory',
  },
];

/**
 * Simple type definition for a parsed command line
 */
type ParsedCommandLine = {
  /**
   * A mapping from the 'argument' of an option to whether it was enabled
   */
  options: Record<string, boolean>;

  /**
   * The list of model names that may have been given explicitly.
   *
   * When this is empty, then it means that all models should be
   * processed.
   */
  modelNames: string[];
};

/**
 * Parses the command line arguments
 *
 * @param argv - The arguments
 * @returns The parsed command line
 */
function parseCommandLine(argv: string[]): ParsedCommandLine {
  const result: ParsedCommandLine = {
    options: {},
    modelNames: [],
  };

  // Note: 'argv[0]' and 'argv[1]' are the script names!
  for (let i = 2; i < argv.length; i++) {
    const arg = argv[i]!;
    let wasOption = false;
    for (const option of commandLineOptions) {
      if (arg === `--${option.argument}`) {
        result.options[option.argument] = true;
        wasOption = true;
      }
    }
    if (!wasOption) {
      result.modelNames.push(arg);
    }
  }

  // Always check when update or process-repo is true
  if (result.options["update"]) {
    result.options["check"] = true;
  }
  if (result.options["process-repo"]) {
    result.options["check"] = true;
  }
  if (result.modelNames.length !== 0) {
    result.options["process-repo"] = false;
  }
  return result;
}

// Parse the command line arguments
const parsedCommandLine = parseCommandLine(process.argv);

// Check if help should be printed - either because it
// was requested, or neither the "process-repo" nor
// any model names had been given.
let shouldPrintHelp = parsedCommandLine.options["help"];
if (!parsedCommandLine.options["process-repo"]) {
  if (parsedCommandLine.modelNames.length === 0) {
    shouldPrintHelp = true;
  }
}

// If help was requested, print help and exit
if (shouldPrintHelp) {
  console.log(`main.ts [--options] [asset]`);
  for (const option of commandLineOptions) {
    console.log(` --${option.argument.padEnd(16)} ${option.text}`);
  }
  console.log(
    ` ${"[asset]".padEnd(
      18
    )} Folder name in model directory to process (defaults to all)`
  );
  process.exit(0);
}

const options = parsedCommandLine.options;
const verbose = options["verbose"] === true;
if (verbose) {
  console.log("Parsed command line: ", parsedCommandLine);
}

// Go to the repo root, and run the processing
process.chdir("..");
const code = ModelMetadata.process(
  parsedCommandLine.options,
  parsedCommandLine.modelNames
);
process.exit(code);
