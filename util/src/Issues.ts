/**
 * Internal structure that is used during validation.
 *
 * This is used during the validation of the JSON data from 'metadata.json'
 * and the subsequent consistency checks (e.g. for the presence of the
 * declared screenshot file), to track warnings and errors.
 */
export type Issues = {
  errors: string[];
  warnings: string[];
};
