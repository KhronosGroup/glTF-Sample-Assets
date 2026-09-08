/**
 * Plain type definition for what is found in the 'legal' array
 * property of the 'metadata.json'.
 *
 * The properties that are REQUIRED in the input are
 * - "license"
 * - "year"
 * - "owner"
 * - "artist"
 * - "what"
 *
 * When the 'license' is a known SPDX identifier, as defined in the
 * 'Licenses.LICENSES', then the optional fields will be filled
 * with the information from these licenses.
 *
 * Otherwise, the 'license' must be a custom license name, like
 * "LicenseRef-LegalMark-Khronos". In this case, the 'text' is
 * also required, and there must be a corresponding license file
 * in the repository, e.g.
 * "./LICENSES/LicenseRef-LegalMark-Khronos.txt"
 */
export type Legal = {
  license: string;
  year: string;
  owner: string;
  artist: string;
  what: string;

  // This is required for custom (non-SPDX) licenses
  text: string | undefined;

  // These remaining fields will be filled automatically:

  // For SPDX licenses, these will be the official SPDX identifier
  // and the official icon. For custom licenses, they will remain
  // undefined.
  spdx: string | undefined;
  icon: string | undefined;

  // For SPDX licenses, this will be the official license URL.
  // For custom licenses, the license URL will be of the pattern
  // "../../LICENSES/LicenseRef-LegalMark-Khronos.txt"
  // to refer to the root 'LICENSES' directory, FROM the
  // model folder
  licenseUrl: string | undefined;
};
