import * as fs from "fs";
import { Listing } from "./Listing";

/**
 * A class that only offers all available listings, as read
 * from the "./data/listings.json".
 *
 * See the "Listing" type for further details.
 */
export class Listings {
  static readonly LISTINGS: Listing[] = JSON.parse(
    fs.readFileSync("./data/listings.json").toString()
  );
}
