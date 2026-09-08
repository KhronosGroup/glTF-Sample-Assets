import * as fs from "fs";

import { Legal } from "./Legal";
import { Licenses } from "./Licenses";
import { Metadata } from "./Metadata";
import { Issues } from "./Issues";
import { Model } from "./Model";
import { ModelMetadata } from "./ModelMetadata";

/**
 * Methods related to 'Model' objects
 */
export class Models {
  static readonly MetadataJsonVersion = 2;

  /**
   * Create all models from the given names in the given base directory.
   *
   * This will create one model for each of the given names, if the
   * model input could successfully be read.
   *
   * If a model can not be created, then the corresponding entry
   * in the 'modelIssues' record will contain information about
   * the errors and warnings that prevented the model from being
   * created.
   *
   * @param baseDirectory - The base directory ("./Models")
   * @param modelNames The names (subdirectory names) of the models
   * @param modelIssues Will be filled with information about issues
   * @returns The models
   */
  static createModels(
    baseDirectory: string,
    modelNames: string[],
    modelIssues: Record<string, Issues>
  ): Model[] {
    const models: Model[] = [];
    for (const modelName of modelNames) {
      const issues = {
        errors: [],
        warnings: [],
      };
      const mm = Models.createModel(baseDirectory, modelName, issues);
      modelIssues[modelName] = issues;
      if (mm !== undefined) {
        models.push(mm);
      }
    }
    return models;
  }

  /**
   * Create the specified model.
   *
   * If the model cannot be created, then the given issues will afterwards
   * contain warning- and error messages, and 'undefined' will be returned.
   *
   * @param baseDirectory - The base directory ("./Models")
   * @param modelName The name (subdirectory name) of the model
   * @param issues Will be filled with information about issues
   * @returns The model
   */
  private static createModel(
    baseDirectory: string,
    modelName: string,
    issues: Issues
  ): Model | undefined {
    const errors = issues.errors;
    const modelPath = `${baseDirectory}/${modelName}`;

    const readmeBodyFile = `${modelPath}/README.body.md`;
    if (!fs.existsSync(readmeBodyFile)) {
      errors.push(`Model README body file not found: ${readmeBodyFile}`);
      return undefined;
    }

    // Read the raw metadata JSON
    const metadataFileName = `${modelPath}/metadata.json`;
    if (!fs.existsSync(metadataFileName)) {
      errors.push(`Model metadata file not found: ${metadataFileName}`);
      return undefined;
    }
    const metadataJson = ModelMetadata.readJson(metadataFileName);

    // Insert default values for optional fields
    if (metadataJson.tags === undefined) {
      metadataJson.tags = [];
    }

    // Validate the metadata JSON, and bail out for errors
    Models.validateMetadataJson(metadataJson, issues);
    if (issues.errors.length > 0) {
      return undefined;
    }

    // Here, the metadata JSON should be a valid "Metadata"
    const metadata = metadataJson as Metadata;

    // Validate the metadata (e.g. presence of screenshots
    // and legal information), and bail out for errors
    Models.validateMetadata(modelPath, metadata, issues);
    if (issues.errors.length > 0) {
      return undefined;
    }

    // Actually create the model from the valid Metadata
    const model = new Model(modelName, metadata);
    model.initialize(baseDirectory, issues);
    if (issues.errors.length > 0) {
      return undefined;
    }

    // Return it if there have not been any errors
    return model;
  }

  /**
   * Validate the given object that was read from the metadata.json file.
   *
   * @param metadataJson - The metadata JSON
   * @param issues Will be filled with any issues
   */
  private static validateMetadataJson(metadataJson: any, issues: Issues) {
    const errors = issues.errors;

    const version = metadataJson.version;
    if (version === undefined) {
      errors.push(`The 'version' is required`);
    } else {
      const versionNumber = parseInt(version);
      if (versionNumber != Models.MetadataJsonVersion) {
        errors.push(
          `Expected version ${Models.MetadataJsonVersion}, but found ${version}`
        );
      }
    }

    const name = metadataJson.name;
    if (name === undefined) {
      errors.push("The 'name' is required");
    }
    const path = metadataJson.path;
    if (path === undefined) {
      errors.push("The 'path' is required");
    }
    const summary = metadataJson.summary;
    if (summary === undefined) {
      errors.push("The 'summary' is required");
    }
    const screenshot = metadataJson.screenshot;
    if (screenshot === undefined) {
      errors.push("The 'screenshot' is required");
    }
    const legal = metadataJson.legal;
    if (legal === undefined || !Array.isArray(legal)) {
      errors.push("The 'legal' must be an array");
    } else {
      for (const element of legal) {
        Models.validateLegalJson(element, issues);
      }
    }
  }

  /**
   * Validate the given 'metadata.legal' field as it was read
   * from the metadata.json file.
   *
   * @param legalJson - The legal JSON
   * @param issues Will be filled with any issues
   */
  private static validateLegalJson(legalJson: any, issues: Issues) {
    const errors = issues.errors;
    if (legalJson === undefined) {
      errors.push(`Invalid 'legal' element`);
      return;
    }

    const year = legalJson.year;
    if (year === undefined) {
      errors.push(`The 'year' is required`);
    } else {
      const yearNumber = parseInt(year);
      if (yearNumber <= 1920) {
        errors.push(`The 'year' must be greater than 1920`);
      }
    }
    const owner = legalJson.owner;
    if (owner === undefined) {
      errors.push(`The 'owner' is required`);
    }
    const license = legalJson.license;
    if (license === undefined) {
      errors.push(`The 'license' is required`);
    }
    const artist = legalJson.artist;
    if (artist === undefined) {
      errors.push(`The 'artist' is required`);
    }
    const what = legalJson.what;
    if (what === undefined) {
      errors.push(`The 'what' is required`);
    }
  }

  /**
   * Validate the given metadata object, checking for the presence
   * of the screenshot and the validity of the legal information.
   *
   * @param modelPath - The path that contains the model, e.g.
   * "./Models/AnimatedTriangle".
   * @param metadata The metadata
   * @param issues Will be filled with any issues
   */
  private static validateMetadata(
    modelPath: string,
    metadata: Metadata,
    issues: Issues
  ) {
    const errors = issues.errors;

    const screenshot = metadata.screenshot;
    const screenshotPath = `${modelPath}/${screenshot}`;
    if (!fs.existsSync(screenshotPath)) {
      errors.push(`Screenshot not found: ${screenshotPath}`);
    }

    const legal = metadata.legal;
    for (const element of legal) {
      Models.validateLegal(element, issues);
    }
  }

  /**
   * Validate the given legal object.
   *
   * For known licenses (i.e. SPDX licenses that are listed in the
   * data/licenses.json), this will fill out the 'spdx', 'licenseUrl',
   * 'text' and 'icon' fields of the given object based on the
   * canonical information from the 'licenses.json' file.
   *
   * For custom licenses, this will check for the consistency of the
   * license name and the required license file.
   *
   * @param modelPath - The path that contains the model, e.g.
   * "./Models/AnimatedTriangle".
   * @param metadata The metadata
   * @param issues Will be filled with any issues
   */
  private static validateLegal(legal: Legal, issues: Issues) {
    const errors = issues.errors;
    const knownLicenses = Licenses.LICENSE;
    const license = legal.license;
    const knownLicense = knownLicenses[license];

    if (knownLicense) {
      // For known (SPDX) licenses, fill out the canonical fields
      legal.spdx = license;
      legal.icon = knownLicense.icon;
      legal.text = knownLicense.text;
      legal.licenseUrl = knownLicense.link;
    } else {
      // For custom license files, check for the presence of the 'text'
      // (which is used as the link text)
      if (legal.text === undefined) {
        errors.push(`License ${license} is not known - the 'text' is required`);
      }

      // Check for the presence of the custom license file
      const expectedLicenseFile = `./LICENSES/${license}.txt`;
      if (!fs.existsSync(expectedLicenseFile)) {
        errors.push(
          `License ${license} requires a file '${expectedLicenseFile}' to be present`
        );
      } else {
        // The actual license URL has to go up TWO levels,
        // because it refers to the model directory
        legal.licenseUrl = `../../LICENSES/${license}.txt`;
      }
    }
  }
}
