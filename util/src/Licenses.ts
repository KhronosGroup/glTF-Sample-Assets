import * as fs from "fs";

/**
 * A class that only exports the known licenses, as read from
 * the "./data/licenses.json" file.
 */
export class Licenses {
  static LICENSE: Record<string, any> = JSON.parse(
    fs.readFileSync("./data/licenses.json").toString()
  );
}
