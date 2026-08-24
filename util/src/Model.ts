import * as fs from "fs";

import { Licenses } from "./Licenses";
import { Metadata } from "./Metadata";
import { Listings } from "./Listings";
import { ModelMetadata } from "./ModelMetadata";
import { Issues } from "./Issues";

/**
 * Representation of a 'Model' in the context of the creation of
 * the files that are associated with a model.
 *
 * This class summarizes everything that is necessary for creating
 * the "LICENSE.md" and "README.md" files for each model, as well
 * as the "model-index.json" file, based on the information that
 * was read from the "metadata.json" of the model.
 */
export class Model {
  /**
   * The (validated) Metadata that was read from "metadata.json".
   */
  private metadata: Metadata;

  /**
   * The directory name of the model, i.e. the subdirectory in
   * the "./Models" folder
   */
  private directoryName: string;

  /**
   * A mapping from variant names like "glTF-Binary" to the
   * respective file, e.g. "AnimatedTriangle.glb".
   */
  private variants: Record<string, string> = {};

  /**
   * Whether this model has an associated GLB file as part of
   * the "glTF-Binary" variant
   */
  private _hasGlb: boolean = false;

  /**
   * The 'extensionsUsed' that have been read from the "glTF"
   * variant of the model.
   */
  private extensionsUsed: string[] = [];

  /**
   * The 'extensionsRequired' that have been read from the "glTF"
   * variant of the model.
   */
  private extensionsRequired: string[] = [];

  /**
   * Creates a new instance
   *
   * @param directoryName - The directory name of the model
   * @param metadata - The validated Metadata from the metadata.json
   */
  constructor(directoryName: string, metadata: Metadata) {
    this.directoryName = directoryName;
    this.metadata = metadata;
  }

  /**
   * Initialize this model, gathering information that can not
   * be derived from the metadata, about the available variants
   * and glTF extensions.
   *
   * @param baseDirectory - The base directory ("./Models")
   */
  initialize(baseDirectory: string, issues: Issues) {
    const glbPath = this.getGlbPath();
    const fullGlbPath = `${baseDirectory}/${glbPath}`;
    this._hasGlb = fs.existsSync(fullGlbPath);

    this.variants = this.findVariants(baseDirectory, issues);
    const extensions = this.readExtensionsInfo(baseDirectory);
    this.extensionsUsed = extensions.used;
    this.extensionsRequired = extensions.required;
  }

  /**
   * Returns all variants that are available for this model.
   *
   * The result will be a record that maps the variant name like "glTF-Binary"
   * to the respective file, e.g. "AnimatedTriangle.glb".
   *
   * @param baseDirectory - The base directory ("./Models")
   * @param issues - Used for tracking errors and warnings
   * @returns The variants
   */
  private findVariants(
    baseDirectory: string,
    issues: Issues
  ): Record<string, string> {
    const variants: Record<string, string> = {};
    const modelDirectory = `${baseDirectory}/${this.getModelPath()}`;
    const errors = issues.errors;

    const knownVariantFileNameExtensions: Record<string, string> = {
      glTF: ".gltf",
      "glTF-Binary": ".glb",
      "glTF-Embedded": ".gltf",
    };

    const directoryEntries = fs.readdirSync(modelDirectory, {
      withFileTypes: true,
    });
    for (const directoryEntry of directoryEntries) {
      // Directories that start with "glTF" indicate variants
      const directoryName = directoryEntry.name;
      if (!directoryEntry.isDirectory() || !directoryName.startsWith("glTF")) {
        continue;
      }
      const variantName = directoryName;
      const variantDirectory = `${modelDirectory}/${variantName}`;

      // Expect a specific extension for the known variant names.
      // Search for ".gltf" or ".glb" files in other cases.
      const expectedExtension = knownVariantFileNameExtensions[variantName];
      if (expectedExtension === undefined) {
        const fileNameGlb = Model.findVariantFileName(variantDirectory, ".glb");
        if (fileNameGlb !== undefined) {
          variants[variantName] = fileNameGlb;
        } else {
          const fileNameGltf = Model.findVariantFileName(
            variantDirectory,
            ".gltf"
          );
          if (fileNameGltf !== undefined) {
            variants[variantName] = fileNameGltf;
          } else {
            errors.push(
              `Did not find a ".gltf" or ".glb" file (case-sensitively) for "${variantName}" variant in ${this.getModelPath()}`
            );
          }
        }
      } else {
        const fileName = Model.findVariantFileName(
          variantDirectory,
          expectedExtension
        );
        if (fileName === undefined) {
          errors.push(
            `Did not find a "${expectedExtension}" file (case-sensitively) for "${variantName}" variant in ${this.getModelPath()}`
          );
        } else {
          variants[variantName] = fileName;
        }
      }
    }
    const sortedVariants: Record<string, string> = {};
    Object.keys(variants)
      .sort()
      .forEach((key) => {
        sortedVariants[key] = variants[key]!;
      });
    return sortedVariants;
  }

  /**
   * Find the first file with the given extension in the given directory.
   *
   * @param variantDirectory - The directory
   * @returns The file name, if it is found
   */
  private static findVariantFileName(
    variantDirectory: string,
    expectedExtension: string
  ): string | undefined {
    const files = fs.readdirSync(variantDirectory);
    for (const file of files) {
      if (file.endsWith(expectedExtension)) {
        return file;
      }
    }
    return undefined;
  }

  /**
   * Tries to read information about the extensions from the "glTF" variant.
   *
   * This will try to read the ".gltf" file and extract its 'extensionsUsed'
   * and 'extensionsRequired' information, defaulting to empty arrays.
   *
   * @param baseDirectory - The base directory ("./Models")
   * @returns The extensions information
   */
  private readExtensionsInfo(baseDirectory: string): {
    used: string[];
    required: string[];
  } {
    const extensions = {
      used: [],
      required: [],
    };
    const gltfPath = this.getGltfPath();
    const fullPath = `${baseDirectory}/${gltfPath}`;
    const glTF = ModelMetadata.readJson(fullPath);
    if (glTF !== undefined) {
      extensions.used = glTF.extensionsUsed ?? [];
      extensions.required = glTF.extensionsRequired ?? [];
    }
    return extensions;
  }

  /**
   * Returns all used extensions
   *
   * @returns The extensions
   */
  getExtensionsUsed(): string[] {
    return this.extensionsUsed;
  }

  /**
   * Returns the metadata that was given in the constructor
   *
   * @returns The metadata
   */
  getMetadata(): Metadata {
    return this.metadata;
  }

  /**
   * Returns the metadata.legal of the metadata that was given in the
   * constructor.
   *
   * @returns The legals
   */
  getLegals() {
    return this.metadata.legal;
  }

  /**
   * Returns the name that was provided in the metadata JSON.
   *
   * This will usually be similar to the model directory name,
   * e.g. the model "AnimatedTriangle" will have the name
   * "Animated Triangle".
   *
   * This is only used for presentation, e.g. titles or link
   * descriptions. It may NOT be used in paths or URLs!
   *
   * @returns The name
   */
  getName(): string {
    return this.metadata.name;
  }

  /**
   * Returns the tags for this model.
   *
   * These are the tags that have been read from the metadata JSON,
   * but here, they will never be 'undefined' (but they may be
   * the empty array).
   *
   * @returns The tags
   */
  getTags(): string[] {
    return this.metadata.tags;
  }

  /**
   * Returns the summary that was read from the metadata JSON.
   *
   * This is a short, human-readable description of the model.
   *
   * @returns The summary
   */
  getSummary(): string {
    return this.metadata.summary;
  }

  /**
   * Returns the name of the directory that contains the model.
   *
   * This is the name of the directory that the actual model is
   * contained in, e.g. "AnimatedTriangle". This name may
   * contain spaces or special characters.
   *
   * @returns The directory name
   */
  getModelPath() {
    return this.directoryName;
  }

  /**
   * Returns the base URL for the model.
   *
   * This is getModelPath, URI-encoded
   *
   * @returns The URL
   */
  getModelUrl() {
    const p = this.getModelPath();
    const result = encodeURI(p);
    return result;
  }

  /**
   * Returns the path of the screenshot.
   *
   * This will be the path of the model, followed by the path
   * of the screenshot that was given in the metadata JSON,
   *
   * @returns The screenshot path
   */
  private getScreenshotPath() {
    const base = this.getModelPath();
    const screenshot = this.metadata.screenshot;
    const screenshotPath = `${base}/${screenshot}`;
    return screenshotPath;
  }

  /**
   * Returns the URL of the screenshot.
   *
   * This is getScreenshotPath, URI-encoded
   *
   * @returns The screenshot URL
   */
  getScreenshotUrl() {
    const p = this.getScreenshotPath();
    const result = encodeURI(p);
    return result;
  }

  /**
   * Returns the screenshot name that was read from the metadata JSON
   *
   * @returns The screenshot name
   */
  getScreenshotName() {
    return this.metadata.screenshot;
  }

  /**
   * Returns the path of the ".glb"" file for the "glTF-Binary" variant.
   *
   * Note: This will return the path, even when the GLB variant
   * does not exist! Use "hasGlb" to check whether the GLB is
   * actually present.
   *
   * @returns The GLB path
   */
  private getGlbPath(): string {
    const name = this.getModelPath();
    const glbPath = `${name}/glTF-Binary/${name}.glb`;
    return glbPath;
  }

  /**
   * Returns the URL of the ".glb"" file for the "glTF-Binary" variant.
   *
   * This is getGlbPath, URI-encoded
   *
   * @returns The GLB URL
   */
  getGlbUrl(): string {
    const p = this.getGlbPath();
    const result = encodeURI(p);
    return result;
  }

  /**
   * Whether this model has an associated GLB file as part
   * of a "glTF-Binary" variant.
   */
  get hasGlb(): boolean {
    return this._hasGlb;
  }

  /**
   * Returns the path of the ".gltf" file for the "glTF" variant.
   *
   * @returns The glTF path
   */
  private getGltfPath(): string {
    const name = this.getModelPath();
    const gltfPath = `${name}/glTF/${name}.gltf`;
    return gltfPath;
  }

  /**
   * Returns the URL of the ".gltf" file for the "glTF" variant.
   *
   * This is getGltfPath, URI-encoded
   *
   * @returns The glTF URL
   */
  getGltfUrl(): string {
    const p = this.getGltfPath();
    const result = encodeURI(p);
    return result;
  }

  /**
   * Returns the available variants.
   *
   * This is a record that maps variant names like "glTF-Binary" to
   * the respective file name, e.g. "AnimatedTriangle.glb".
   *
   * @returns The variants
   */
  getVariants(): Record<string, string> {
    return this.variants;
  }

  /**
   * Creates the markdown string that goes into the "README.md" of
   * the model.
   *
   * @param baseDirectory - The base directory ("./Models")
   * @returns The markdown
   */
  createReadmeMarkdown(baseDirectory: string): string {
    // Header
    const md: string[] = [];
    md.push(`# ${this.getName()}`);
    md.push("");
    md.push(
      `<!-- This file is auto-generated by modelmetadata. Do not edit by hand. -->`
    );
    md.push("");

    // The tags: Each (known) tag is a link to the corresponding listing
    // file, e.g. "showcase" will link to "../Models-showcase.md".
    md.push("## Tags");
    md.push("");
    const tagList: string[] = [];
    const tags = this.getTags();
    for (const tag of tags) {
      const path = Model.findTagListingFile(tag);
      if (path === undefined) {
        tagList.push(`${tag}`);
      } else {
        tagList.push(`[${tag}](../${path})`);
      }
    }
    const tagString = tagList.join(", ");
    md.push(tagString);
    md.push("");

    // Information about the used and required extensions
    if (this.extensionsRequired.length > 0) {
      md.push("## Extensions Required");
      md.push("");
      md.push(this.extensionsRequired.map((e) => "* " + e).join("\n"));
      md.push("");
    }
    if (this.extensionsUsed.length > 0) {
      md.push("## Extensions Used");
      md.push("");
      md.push(this.extensionsUsed.map((e) => "* " + e).join("\n"));
      md.push("");
    }

    // The summary from the metadata JSON
    md.push("## Summary");
    md.push("");
    md.push(this.metadata.summary);
    md.push("");

    // Convenience operations to open in sample viewer or
    // download the GLB
    md.push("## Operations");
    md.push("");
    const displayUrl = this.hasGlb ? this.getGlbUrl() : this.getGltfUrl();
    const fullDisplayUrl = `${ModelMetadata.UrlModelRepoRaw}/${baseDirectory}/${displayUrl}`;
    md.push(
      `* [Display](${ModelMetadata.UrlSampleViewer}?model=${fullDisplayUrl}) in SampleViewer`
    );
    if (this.hasGlb) {
      const fullGlbUrl = `${
        ModelMetadata.UrlModelRepoRaw
      }/${baseDirectory}/${this.getGlbUrl()}`;
      md.push(`* [Download GLB](${fullGlbUrl})`);
    }
    md.push(`* [Model Directory](./)`);
    md.push("");

    // Inline the "README.body.md". The existence of this file
    // was validated by 'Models.createModel'.
    const readmeBodyFile = `${baseDirectory}/${this.getModelPath()}/README.body.md`;
    md.push(fs.readFileSync(readmeBodyFile, "utf8").toString());
    md.push("");

    // Legal information
    md.push("## Legal");
    md.push("");
    const credits = this.createCreditsMarkdownLines(false);
    md.push(credits.join("\n\n"));
    md.push("");

    md.push(
      `<!-- This file is auto-generated by modelmetadata. Do not edit by hand. -->`
    );
    md.push("");
    const result = md.join("\n");
    return result;
  }

  /**
   * Creates lines of markdown for the credits.
   *
   * These are two lines for each 'metadata.legal' entry:
   * - The copyright year and owner (and license link)
   * - Information about the artist and content
   *
   * The 'inListing' indicates whether these credits are going
   * into one of the 'listing' files that are stored directly
   * in the `./Models` directory. In this case, relative URLs
   * of the 'metadata.legal' structure that go up TWO levels
   * from the model directory ("../../") have to be modified
   * to only go up ONE level ("../");
   *
   * @param inListing - Whether this is going into a listing file
   * @returns The lines
   */
  createCreditsMarkdownLines(inListing: boolean): string[] {
    const credits: string[] = [];

    const metadata = this.metadata;
    for (const element of metadata.legal) {
      let url = element.licenseUrl ?? "";
      if (inListing) {
        if (url.startsWith("../../")) {
          url = url.substring(3);
        }
      }
      const urlEncoded = encodeURI(url);
      const year = element.year;
      const owner = element.owner;
      const artist = element.artist;
      const what = element.what;
      const text = element.text;
      credits.push(`&copy; ${year}, ${owner}. [${text}](${urlEncoded})`);
      credits.push(` - ${artist} for ${what}`);
    }
    return credits;
  }

  /**
   * Returns the name of the Listing file for the given tag.
   *
   * For known tags (as defined in the "Listings" class), this will
   * return the corresponding file. E.g. for "showcase", it will
   * return the file name of the listing tagged with "showcase".
   *
   * @param tag - The tag
   * @returns The path
   */
  private static findTagListingFile(tag: string): string | undefined {
    const listings = Listings.LISTINGS;
    for (const listing of listings) {
      const listingTags = listing.tags;
      if (listingTags.includes(tag)) {
        return listing.file;
      }
    }
    return undefined;
  }

  /**
   * Creates the markdown string that goes into the "LICENSE.md" of
   * the model.
   *
   * @returns The markdown
   */
  createLicenseMarkdown(): string {
    const md: string[] = [];

    // Header
    md.push(`# LICENSE file for the model: ${this.getName()}`);
    md.push(
      "All files in this directory tree are licensed as indicated below."
    );
    md.push(
      "* All files directly associated with the model including all text, image and binary files:"
    );

    // The "text" (short name) of the licenses, linking to the
    // license URL
    for (const legal of this.metadata.legal) {
      const url = legal.licenseUrl ?? "";
      const urlEncoded = encodeURI(url);
      let spdxInfo = "";
      if (legal.spdx !== undefined) {
        spdxInfo = ` [SPDX license identifier: "${legal.spdx}"]`;
      }
      md.push(`  * [${legal.text}](${urlEncoded})${spdxInfo}`);
    }

    // Meta-license
    md.push(
      '* This file and all other metadocumentation files including "metadata.json":'
    );
    const license = Licenses.LICENSE["CC-BY-4.0"];
    md.push(
      `  * [${license.text}]("${license.link}") [SPDX license identifier: "CC-BY-4.0"]`
    );

    // Footer
    md.push(
      "Full license text of these licenses are available at the links above."
    );
    md.push("This license excludes logos and associated trademarks.");
    md.push(
      `<!-- This file is auto-generated by modelmetadata. Do not edit by hand. -->`
    );

    const result = md.join("\n\n") + "\n";
    return result;
  }
}
