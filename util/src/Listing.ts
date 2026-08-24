/**
 * A "Listing" is the description of one of the overview files
 * that are generated for each tag.
 *
 * For example, for the "showcase" tag, this will be used to
 * build the "./Models/Models-showcase.md" file.
 *
 * Note that this only really makes sense when the "tags"
 * array contains exactly one element for now...
 *
 * The available listings are provided by the "Listings" class.
 */
export type Listing = {
  file: string;
  tags: string[];
  summary: string;
};
