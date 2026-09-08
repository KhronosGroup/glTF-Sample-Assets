import { Legal } from "./Legal";

/**
 * Plain type definition for what is found in the 'metadata.json'
 */
export type Metadata = {
  version: number;
  legal: Legal[];
  tags: string[];
  screenshot: string;
  name: string;
  summary: string;
};
