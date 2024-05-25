<?php

/*
 * PHP script for automatically processing metadata files in the glTF Sample Repo
 * And creating the necessary support files including README and LICENSE
 *
 * Copyright: 2023, The Khronos Group.
 * Author: Leonard Daly, Daly Realism
 * Significant contributions from
 *	Marco Hutter (JSON design, tag structure, and overall design)
 *	Ed Mackey (license resolution for models in the Repo)
 *
 *	SPDX-FileCopyrightText: 2023, The Khronos Group
 *
 *	SPDX-License-Identifier: Apache-2.0
 *
 *	Licensed under the Apache License, Version 2.0 (the "License");
 *	you may not use this file except in compliance with the License.
 *	You may obtain a copy of the License at
 *	
 *	    http://www.apache.org/licenses/LICENSE-2.0
 *	
 *	Unless required by applicable law or agreed to in writing, software
 *	distributed under the License is distributed on an "AS IS" BASIS,
 *	WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *	See the License for the specific language governing permissions and
 *	limitations under the License.
**/

// Define a script-wide constants

	define ('AppName', 'modelmetadata');
	define ('AppVersionMajor', 1);
	define ('AppVersionMinor', 2);
	define ('AppVersionPatch', 17);
	define ('AppVersionPrerelease', '');
	define ('AppVersion', sprintf('%d.%d.%d%s', AppVersionMajor, AppVersionMinor, AppVersionPatch, ((AppVersionPrerelease == "") ? "" : "-".AppVersionPrerelease)));
	define ('UrlSampleViewer', 'https://github.khronos.org/glTF-Sample-Viewer-Release/');
	define ('UrlModelRepoRaw', 'https://raw.GithubUserContent.com/KhronosGroup/glTF-Sample-Assets/main');
	define ('DebugNone', 0);
	define ('DebugModel', 1);
	define ('DebugDetail', 2);

	
// Define a class to handle a single model
class ModelMetadata
{
	
// Public constants 
	public $jsonVERSION = 2;
/*
 * Debug output level
 *	0 - none
 *	1 - Model name and 
 *	2 - 
**/
	private $DebugNone = 0;
	private $DebugModel = 1;
	private $DebugDetail = 2;
	public $debugOutput = 0;

	
// Public variables for internal states
	public $isCurrent = false;
	public $hasError = false;
	public $errorMessage = '';
	public $modelKey = '';
	public $modelName = '';

	private $metaJson = '
{
    "version" : 2,
    "legal" : [
        {
            "artist" : "",
            "owner" : "",
            "year" : "",
            "license" : "",
            "what" : ""
        }
      ],
    "tags" : [],
    "screenshot" : "screenshot/screenshot.jpg",
    "name" : "",
    "path" : "",
    "summary" : "",
    "AutoGenerateREADME" : false,
    "createReadme" : false
}';

// Placeholder for later use
	private $metaPhp = 0;
	
// Array to convert from a variety of user values to PHP's true or false
	public $TF = array (
						'FALSE'	=> false,
						'0'		=> false,
						'F'		=> false,
						'false'	=> false,
						'no'	=> false,
						'NO'	=> false,
						false	=> false,
						'TRUE'	=> true,
						'1'		=> true,
						'T'		=> true,
						'true'	=> true,
						'yes'	=> true,
						'YES'	=> true,
						true	=> true
						);

// Array of standard model licenses
	public $LICENSE = array (
			'CC0'		=> array (
							'icon'=>'https://licensebuttons.net/p/zero/1.0/88x31.png', 
							'link'=>'https://creativecommons.org/publicdomain/zero/1.0/legalcode',
							'text'=>'CC0 1.0 Universal',
							'spdx'=>'CC0-1.0',
							),
			'PD'		=> array (
							'icon'=>'', 
							'link'=>'',
							'text'=>'Public Domain',
							'spdx'=>'PublicDomain',
							),
			'Public Domain'		=> array (
							'icon'=>'', 
							'link'=>'',
							'text'=>'Public Domain',
							'spdx'=>'PublicDomain',
							),
			'Public Domain / CC0'		=> array (
							'icon'=>'https://licensebuttons.net/p/zero/1.0/88x31.png', 
							'link'=>'https://creativecommons.org/publicdomain/zero/1.0/legalcode',
							'text'=>'CC0 1.0 Universal',
							'spdx'=>'CC0-1.0',
							),
			'CC-BY'		=> array (
							'icon'=>'https://licensebuttons.net/l/by/3.0/88x31.png', 
							'link'=>'https://creativecommons.org/licenses/by/4.0/legalcode',
							'text'=>'CC BY 4.0 International',
							'spdx'=>'CC-BY-4.0',
							),
			'CC-BY-NC'		=> array (
							'icon'=>'https://mirrors.creativecommons.org/presskit/buttons/88x31/png/by-nc.png', 
							'link'=>'https://creativecommons.org/licenses/by-nc/4.0/legalcode',
							'text'=>'CC BY-NC 4.0 International',
							'spdx'=>'CC-BY-NC-4.0',
							),
			'CC-BY 4.0'	=> array (
							'icon'=>'https://licensebuttons.net/l/by/3.0/88x31.png', 
							'link'=>'https://creativecommons.org/licenses/by/4.0/legalcode',
							'text'=>'CC BY 4.0 International',
							'spdx'=>'CC-BY-4.0',
							),
			'CC-BY-4.0'	=> array (
							'icon'=>'https://licensebuttons.net/l/by/3.0/88x31.png', 
							'link'=>'https://creativecommons.org/licenses/by/4.0/legalcode',
							'text'=>'CC BY 4.0 International',
							'spdx'=>'CC-BY-4.0',
							),
			'CC-BY International 4.0'	=> array (
							'icon'=>'https://licensebuttons.net/l/by/3.0/88x31.png', 
							'link'=>'https://creativecommons.org/licenses/by/4.0/legalcode',
							'text'=>'CC BY 4.0 International',
							'spdx'=>'CC-BY-4.0',
							),
			'SCEA'	=> array (
							'icon'=>'', 
							'link'=>'https://spdx.org/licenses/SCEA.html',
							'text'=>'SCEA Shared Source License, Version 1.0',
							'spdx'=>'SCEA',
							),
			'LicenseRef-Stanford-Graphics'	=> array (
							'icon'=>'', 
							'link'=>'https://graphics.stanford.edu/data/3Dscanrep/',
							'text'=>'Stanford Graphics Library',
							'spdx'=>'LicenseRef-Stanford-Graphics',
							),
			'LicenseRef-CC-BY-TM'	=> array (
							'icon'=>'https://licensebuttons.net/l/by/3.0/88x31.png', 
							'link'=>'',
							'text'=>'CC-BY 4.0 International with Trademark Limitations',
							'spdx'=>'LicenseRef-CC-BY-TM',
							),
			'LicenseRef-LegalMark-Cesium'	=> array (
							'icon'=>'', 
							'link'=>'',
							'text'=>'Cesium Trademark or Logo',
							'spdx'=>'LicenseRef-LegalMark-Cesium',
							),
			'LicenseRef-LegalMark-Khronos'	=> array (
							'icon'=>'', 
							'link'=>'',
							'text'=>'Khronos Trademark or Logo',
							'spdx'=>'LicenseRef-LegalMark-Khronos',
							),
			'LicenseRef-LegalMark-UX3D'	=> array (
							'icon'=>'', 
							'link'=>'',
							'text'=>'UX3D Trademark or Logo',
							'spdx'=>'LicenseRef-LegalMark-UX3D',
							),
			'LicenseRef-Khronos-Assumed'	=> array (
							'icon'=>'', 
							'link'=>'',
							'text'=>'Assumed Khronos license, treat as TESTING only',
							'spdx'=>'LicenseRef-Khronos-Assumed',
							),
			'LicenseRef-3DRT-Testing'	=> array (
							'icon'=>'', 
							'link'=>'',
							'text'=>'3DRT license with allowances for glTF Testing',
							'spdx'=>'LicenseRef-3DRT-Testing',
							),
			'LicenseRef-Adobe-Stock'	=> array (
							'icon'=>'', 
							'link'=>'https://stock.adobe.com/license-terms?prev_url=detail&comparison-full#enhanced-license-terms',
							'text'=>'Adobe Stock License',
							'spdx'=>'LicenseRef-Adobe-Stock',
							),
			'LicenseRef-Poser-EULA'	=> array (
							'icon'=>'', 
							'link'=>'https://archive.org/stream/poser-pro-2014-reference-manual/Poser_Pro_2014_reference_manual_djvu.txt',
							'text'=>'Poser EULA',
							'spdx'=>'LicenseRef-Poser-EULA',
							),
			'LicenseRef-CRYENGINE-Agreement'	=> array (
							'icon'=>'', 
							'link'=>'https://www.cryengine.com/ce-terms',
							'text'=>'Cryengine Limited License Agreement',
							'spdx'=>'LicenseRef-CRYENGINE-Agreement',
							),
			);

// Model's metadata, either stored in the Repo or derrived from it
	private $metadata = array();
	
// Method construct the object
	public function __construct ($path='', $file=null) {
		$this->metaPhp = json_decode ($this->metaJson, true);
		foreach ($this->metaPhp as $key => $value) {
			$this->metadata[$key] = $value;
		}
		
/*
 *	// Special testing for "weird" directory paths
		if ($path == './Models/Box With Spaces') {
			//$this->debugOutput = $this->DebugModel;
			$this->debugOutput = $this->DebugDetail;
			print "$path: ".$this->debugOutput."\n";
		} else {
			$this->debugOutput = $this->DebugNone;
		}
*/		
		if ($path != '') {
			$this->load ($path, $file);
		}
		return $this;
	}

/*
 * Method to load a metadata JSON file
 * This method reads the specified JSON file from disk, decodes it, and stores it
 * If necessary the version is upgraded to the latest supported
 * Additional data extractions and compositions are performed for internal use
 * At the conclusion, the class object is fully ready for processing.
 * Additional data may be stored or changed with other methods
**/
	public function load ($path, $file='metadata') {
		$this->hasError = false;
		$this->errorMessage = "";
		$fullFile = $path . '/' . $file . '.json';
		if (!file_exists ($fullFile)) {
			$this->hasError = true;
			$this->errorMessage = "File not found: $fullFile";
			$this->metadata = $this->_createInitial ($path);
			$this->hasError = false;
		} else {
			if ($this->debugOutput >= $this->DebugDetail) 
				print "Loading |$fullFile| and storing in ->metadata\n";
			$this->metadata = $this->_readJson ($fullFile);
			$this->isCurrent = true;
			if ($this->debugOutput >= $this->DebugDetail) 
				print_r($this->metadata);
		}
		
		if ($this->debugOutput >= $this->DebugDetail) {
			print "After loading default\n";
			print_r ($this);
		}

		$this->_addFileInfo ($path, $file, 'json');
		if ($this->debugOutput >= $this->DebugModel) {
			print "Checking for required update. Existing " . $this->metadata['version'] . "; target: " . $this->jsonVERSION. "\n";
			print "Loading $path with V".$this->metadata['version']."\n";
		}
		if ($this->metadata['version'] < $this->jsonVERSION) {
			$this->_updateMetadata();
			$this->isCurrent = false;
		}

		if ($this->debugOutput >= $this->DebugModel) 
				print "Populating internal structures for ".$this->metadata['path']."\n";
		$this->_populateInternal ();
		return $this;
	}
/*
 * Method to check various characteristics of the Asset directory
 *	The following error checks are performed
 *	 - JSON current (irrelevant because it is updated in ->load)
 *	 - JSON complete (items below are not default)
 *	   - Summary
 *	   - License (at least 1) with year, owner (Public OK), license (must be known), {artist, what} if license != PD
 *	 - Snapshot exists as specified in metadata
 *	 - createReadme == TRUE
 *	   - nearly last line of README.md looks like "#### Assembled by " . AppName . ' ' . AppVersion;
 *
 *	The following generate warning messages
 *	 - screenshot is > 200px in width
 *	 - createReadme != TRUE
**/
	public function reportIssues() {
		$errors = [];
		$warnings = [];

		if (!$this->isCurrent) $warnings[] = 'JSON not current and automatically updated';
		if ($this->metadata['summary'] == '' || str_starts_with ($this->metadata['summary'], '_')) $errors[] = 'No asset summary';
		if ($this->metadata['legal'][0]['year'] <= 1920) $errors[] = 'Invalid Copyright year';
		if ($this->metadata['legal'][0]['owner'] == '' || str_starts_with ($this->metadata['legal'][0]['owner'], '_')) $errors[] = 'Missing  Copyright owner';
		if ($this->metadata['legal'][0]['license'] == '' || str_starts_with ($this->metadata['legal'][0]['license'], '_')) $errors[] = 'Missing license';
		if ($this->metadata['legal'][0]['license'] != 'Public Domain / CC0') {
			if ($this->metadata['legal'][0]['artist'] == '' || str_starts_with ($this->metadata['legal'][0]['artist'], '_')) $errors[] = 'Missing artist';
			if ($this->metadata['legal'][0]['what'] == '' || str_starts_with ($this->metadata['legal'][0]['what'], '_')) $errors[] = 'Missing work (what)';
		}
		
		if (!file_exists($this->metadata['basePathShot'])) {
			print 'Screenshot not fount: ' . $this->metadata['basePathShot'] . "\n";
			$errors[] = 'Screenshot file not found';
		} else {		// Check for proper size
		}
		
		if ($this->metadata['createReadme']) {
			// Verify that existing README.md is autogenerated
			$fileReadme = $this->metadata['basePath'] . 'README.md';
			if (file_exists($fileReadme)) {
				$readme = file ($fileReadme);
				$ii=count($readme)-1;
				$keepGoing = true;
				while ($ii>=0 && $keepGoing) {
					if ($readme[$ii] != '') {
						if (!str_starts_with($readme[$ii], '#### Assembled by modelmetadata')) {
							$errors[] = 'Autogenerate README requested, but README.md is not already autogenerated.';
						}
						$keepGoing = false;
					}
					$ii--;
				}
			}
		} else {
			$warnings[] = 'README is not auto-generated';
		}
		if (count($errors) > 2) {$warnings[] = 'basePath: ' . $this->metadata['basePath'];}
		$issues = array ('error'=>$errors, 'warning'=>$warnings);
		return $issues;
	}


/*
 * Method to overwrite JSON metadata file in the latest version
**/
	public function writeMetadata() {
		if ($this->isCurrent) {
			return $this;
		}
		
		$tmp = array();
		foreach ($this->metaPhp as $key => $value) {
			$tmp[$key] = $this->metadata[$key];
		}
		$tmp['version'] = $this->jsonVERSION;
		unset ($tmp['AutoGenerateREADME']);
		$string = json_encode($tmp, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
		
		if ($this->debugOutput >= $this->DebugDetail) 
			print " .. writing METADATA to ".$this->metadata['fullPath']."\n";
		if ($this->metadata['fullPath'] == '') {
			print "*** Error: Full path is empty\n";
			print_r($this->metadata);
			exit;
		}
		$FH = fopen ($this->metadata['fullPath'], "w");
		fwrite ($FH, $string);
		fclose ($FH);

		$this->hasError = false;
		$this->errorMessage = "";
		return $this;
	}

	public function getMetadata() {
		return $this->metadata;
	}
/*
 * Methods to output processed data
 *	README, LICENSE, etc
 *
**/
	public function writeReadme ($tagListings=null) {
		$fileReadme = $this->metadata['basePath'] . 'README.md';
		$fileReadme = str_replace ('%20', ' ', $fileReadme);
		$fileReadmeBody = $this->metadata['basePath'] . 'README.body.md';
		if (!$this->metadata['createReadme']) {return $this; }
		if ($this->debugOutput >= DebugModel) 
			print " .. Updating README\n";
		
		$screenshot = $this->metadata['screenshot'];
		$tagList = array();
		for ($ii=0; $ii<count($this->metadata['tags']); $ii++) {
			$path = $this->_getTagListingPath ($this->metadata['tags'][$ii], $tagListings);
			if ($path == '') {
				$tagList[] = sprintf ('%s', $this->metadata['tags'][$ii]);
			} else {
				$tagList[] = sprintf ('[%s](../../%s)', $this->metadata['tags'][$ii], $path);
			}
		}
		$tagString = join (', ', $tagList);

		$readme = array();
		$readme[] = '# ' . $this->modelName;
		$readme[] = "## Tags";
		$readme[] = $tagString;

		if (count($this->metadata['extensions']['Used']) > 0 || count($this->metadata['extensions']['Required']) > 0) {
			//print " --- Extensions ...";
			$extList = [];
			if (count($this->metadata['extensions']['Required']) == 0) {
				//print " Used";
				$readme[] = "## Extensions Used";
				for ($ii=0; $ii<count($this->metadata['extensions']['Used']); $ii++) {
					$extList[] = "* " . $this->metadata['extensions']['Used'][$ii];
				}
				$readme[] = join("\n", $extList);

			} else if (count($this->metadata['extensions']['Used']) == 0) {
				//print " Required";
				$readme[] = "## Extensions Required";
				for ($ii=0; $ii<count($this->metadata['extensions']['Required']); $ii++) {
					$extList[] = "* " . $this->metadata['extensions']['Required'][$ii];
				}
				$readme[] = join("\n", $extList);

			} else {
				//print " Both";
				$readme[] = "## Extensions";
				$readme[] = "### Required";
				for ($ii=0; $ii<count($this->metadata['extensions']['Required']); $ii++) {
					$extList[] = "* " . $this->metadata['extensions']['Required'][$ii];
				}
				$readme[] = join("\n", $extList);
				$readme[] = "### Used";
				for ($ii=0; $ii<count($this->metadata['extensions']['Used']); $ii++) {
					$extList[] = "* " . $this->metadata['extensions']['Used'][$ii];
				}
				$readme[] = join("\n", $extList);
			}
			//print "\n";
		}
		$readme[] = "## Summary";
		$readme[] = $this->metadata['summary'];

		$readme[] = "## Operations";
		$sf1 = "* [Display](%s?model=%s/%s) in SampleViewer";
		$sf2 = "* [Download GLB](%s/%s)";
		$sf3 = "* [Model Directory](%s)";
		
		$pathModel = ($this->metadata['hasGLB']) ? $this->metadata['pathGLB'] : $this->metadata['pathModel'];
		$operations = array (sprintf ($sf1, UrlSampleViewer, UrlModelRepoRaw, $pathModel));
		if ($this->metadata['hasGLB']) {
			$operations[] = sprintf ($sf2, UrlModelRepoRaw, $this->metadata['pathGLB']);
		}
//		$operations[] = sprintf ($sf3, $this->metadata['basePath']);
		$operations[] = sprintf ($sf3, './');
		$readme[] = join("\n", $operations);


/*
 * Insert body
 *	The body needs to include the screen shot because there may be 
 *	captions or multiple shots to illustrate the contents.
 *	BODY is included as a single string to preserve line structure from original.
 *	At a mininum, the following can be used in the BODY file
 *	Where the content in <...> is the value of the internal variable
 *		## Screenshot
 *
 *		![screenshot](<$this->metadata['screenshot']>)
 *
 *	The equivalent code lines are:
 *		$readme[] = '## Screenshot';
		$readme[] = "![screenshot](".$this->metadata['screenshot'].")";
 *
**/
		if (file_exists($fileReadmeBody)) {
			$readme[] = file_get_contents($fileReadmeBody);
		} else {
			$readme[] = '## Screenshot';
			$readme[] = "![screenshot](".$this->metadata['screenshot'].")";
			$readme[] = '## Description';
			$readme[] = "_None provided._";
		}
		$readme[] = '## Legal';
		for ($ii=0; $ii<count($this->metadata['credit']); $ii++) {
			$readme[] = $this->metadata['credit'][$ii];
		}
		//$readme[] = "#### Assembled by " . AppName . ' ' . AppVersion;
		$readme[] = "#### Assembled by " . AppName;
		$output = join ("\n\n", $readme);

		if ($this->debugOutput >= $this->DebugModel) 
			print " .. writing README to $fileReadme\n";
		$FO = fopen ("$fileReadme", 'w');
		fwrite ($FO, $output);
		fclose ($FO);

		return $this;
	}
	private function _getTagListingPath ($tag, $tagListings) {
		if (count($tagListings) < 1) {return ''; }
		for ($ii=0; $ii<count($tagListings); $ii++) {
			if (isset($tagListings[$ii]['tags'][0]) && $tag == $tagListings[$ii]['tags'][0]) {
				return $tagListings[$ii]['file'];
			}
		}
		return '';
	}

/*
 * Create the model's LICENSE markdown file.
 * This is always created based on the information in $metadata
 * Uses data from the Summary, License, Author, Owner, Year
 */
	public function writeLicense () {

		$readme = array();
		$readme[] = '# LICENSE file for the model: ' . $this->modelName;
		$readme[] = 'All files in this directory tree are licensed as indicated below.';
		$readme[] = '* All files directly associated with the model including all text, image and binary files:';
		for ($ii=0; $ii<count($this->metadata['legal']); $ii++) {
			$readme[] = '  * [' . $this->metadata['legal'][$ii]['text'] . ']("' . $this->metadata['legal'][$ii]['licenseUrl'] . '") [SPDX license identifier: "' . $this->metadata['legal'][$ii]['spdx'] . '"]';
		}
		$readme[] = '* This file and all other metadocumentation files including "metadata.json":';
		$readme[] = '  * [Creative Commons Attribtution 4.0 International]("'.$this->LICENSE['CC-BY 4.0']['link'].'") [SPDX license identifier: "CC-BY-4.0"]';
		$readme[] = 'Full license text of these licenses are available at the links above';
		//$readme[] = "#### Generated by " . AppName . ' ' . AppVersion;
		$readme[] = "#### Generated by " . AppName;
		$output = join ("\n\n", $readme);

		$FO = fopen ($this->metadata['baseLicensePath'], 'w');
		fwrite ($FO, $output);
		fclose ($FO);

		return $this;
	}

/*
 * Methods to deal with tags & license(s)
 * set* sets the entire structure for '*'
 * add* adds to the existing structure
 * get* returns the structure for '*'
 *
 * Licenses must contain at least a name, if the name is standard (see LICENSES)
 *	if the name is not standard, then it needs to also include the URL of the license text
 *	After the new license is in place, the system will do a cleanup, then regenerate the credits
**/
	public function setWriteReadme ($write=false) {
		$this->metadata['createReadme'] = (isset($this->TF[$write])) ? $this->TF[$write] : false;
		$this->metadata['AutoGenerateREADME'] = $this->metadata['createReadme'];
		$this->hasError = false;
		$this->errorMessage = "";
		return $this;
	}
	public function setNotCurrent () {
		$this->isCurrent = false;
		$this->_cleanupLicense(true);
		$this->hasError = false;
		$this->errorMessage = "";
		return $this;
	}
	public function setSummary ($newSummary='') {
		$this->metadata['summary'] = $newSummary;
		$this->isCurrent = false;
		$this->hasError = false;
		$this->errorMessage = "";
		return $this;
	}
	public function addTags ($tags=null) {
		if (!isset($this->metadata['tags'])) {
			$this->metadata['tags'] = $tags;
		} else {
			for ($ii=0; $ii<count($tags); $ii++) {
				if (!preg_grep("/$tags[$ii]/i",$this->metadata['tags'])) {
					$this->metadata['tags'][] = strtolower ($tags[$ii]);
				}
			}
		}
		$this->isCurrent = false;
		$this->hasError = false;
		$this->errorMessage = "";
		return $this;
	}
	public function addLicense ($license, $removeAll=false) {
		if (!isset($license)) {
			$this->hasError = false;
			$this->errorMessage = "";
			return $this;
		}
//		$this->hasError = false;
//		$this->errorMessage = "";
		if ($removeAll) {$this->metadata['legal'] = null;}
		$this->_addLicense ($license);

		// Generate link to license if standard license and link not provided
		$this->_cleanupLicense (true);
		$this->metadata['credit'] = $this->_generateCredits();

		if ($this->debugOutput >= $this->DebugDetail) {
			print "Before return\n";
			print_r($this->metadata['credit']);
			print "==================================\n\n";
		}
		$this->isCurrent = false;
		return $this;
	}
	private function _addLicense ($license) {
		$ndx = (isset($this->metadata['legal'][0])) ? count($this->metadata['legal']) : 0;
		$this->metadata['legal'][$ndx]['license']		= (isset($license['license'])) ? $license['license'] : '';
		$this->metadata['legal'][$ndx]['licenseUrl']	= (isset($license['licenseUrl'])) ? $license['licenseUrl'] : '';
		$this->metadata['legal'][$ndx]['artist']		= (isset($license['artist'])) ? $license['artist'] : '';
		$this->metadata['legal'][$ndx]['year']			= (isset($license['year'])) ? $license['year'] : '';
		$this->metadata['legal'][$ndx]['owner']			= (isset($license['owner'])) ? $license['owner'] : '';
		$this->metadata['legal'][$ndx]['what']			= (isset($license['what'])) ? $license['what'] : '';
		$this->metadata['legal'][$ndx]['text']			= (isset($license['text'])) ? $license['text'] : $this->metadata['legal'][$ndx]['license'];
	}

/*
 * Populates internal values from the read in ones
 *	Several data fields are populated based on the directory structure
 *	This is done so the information does not need to be in the metadata File
 *	The fields are
 *		All fields related to screenshot (see _handleScreenshot)
 *		The 'variants' field containing a dictionary of glTF directories with associated glTF file (see _findVariants)
 */ 
	private function _populateInternal () {
		//$this->metadata->foo = f ($this->metadata->bar);
		if (isset($this->metadata['name'])) {
			$this->modelKey  = $this->metadata['name'];
			$this->modelName = $this->metadata['name'];
		}

		// Minor change that does not warrant a version # upgraded
		if (!isset($this->metadata['legal'])) {
			$this->isCurrent = false;
			$this->metadata['legal'] = $this->metadata['Legal'];
		}

		// Generate link to license if standard license and link not provided
		$this->_cleanupLicense ();
		
		$this->metadata['legalGood'] = ($this->metadata['legal'][0]['owner'] == '_No Owner_' || $this->metadata['legal'][0]['year'] == 0) ? false : true;

		$this->metadata['createReadme']	= (isset($this->metadata['AutoGenerateREADME'])) ? $this->metadata['AutoGenerateREADME'] && $this->metadata['createReadme'] : $this->metadata['createReadme'];
		$this->metadata['createReadme'] = $this->TF[$this->metadata['createReadme']];
		$this->metadata['AutoGenerateREADME']	= $this->metadata['createReadme'];
		$creditEntry = $this->_generateCredits();
		if ($this->hasError) {
			$this->errorMessage = "Error processing " . $this->modelName . "\n -- " . join ("\n -- ", $creditEntry);
		}
		$this->metadata['credit']		= $creditEntry;
		$this->metadata['summary']		= ($this->metadata['summary'] == '') ? '_No Summary_' : $this->metadata['summary'];
		$this->_handleScreenshot();
		$this->_findVariants();
		
		$this->_getExtensionsList($this->metadata['pathModel']);

		return;
	}

/*
 * Get the list of extensions used in this model. 
 * Extensions come as 'Required' and 'Used'.
 * These categories are defined as arrays in $this->metadata['extensions']
 *	= array('used': array(), 'required': array())
**/
	private function _getExtensionsList($pathGlTF) {
		$extensions = array(
						'Used' 		=> array(),
						'Required'	=> array(),
						);
		// Open glTF file (JSON version)
		// Read and parse it
		 $glTF = $this->_readJson ($pathGlTF);
		// Save off the stuff from the Extensions structure
		if (isset($glTF['extensionsUsed'])) {
			$extensions['Used'] = $glTF['extensionsUsed'];
			//print "-- " . join(', ', $glTF['extensionsUsed']) . "\n";
		}
		if (isset($glTF['extensionsRequired'])) {
			$extensions['Required'] = $glTF['extensionsRequired'];
		}
		
		$this->metadata['extensions'] = $extensions;
		return;
	}

/*
 * Handles the screenshot for this model
**/	
	private function _handleScreenshot () {
		$path = $this->metadata['path'];
		$pathSafe = str_replace (' ', '%20', $path);
		$folder = $this->metadata['folder'];
		$folderSafe = str_replace (' ', '%20', $folder);
		$this->metadata['pathSafe'] = $pathSafe;
		$this->metadata['folderSafe'] = $folderSafe;
		
		$shotHeight = 150;
		$screenshot = $this->metadata['screenshot'];
		$tmp = explode ('.', $screenshot);
		$shotPathName = $tmp[0];
		$shotExtension = $tmp[1];

// basePath* is from the repo root directory
// path* is from the model directory
		$this->metadata['screenshotType']	= $shotExtension;
		$this->metadata['basePath']			= $pathSafe . '/';
		$this->metadata['basePathModel']	= 'path-to-model';
		$this->metadata['basePathShot']		= $path . '/' . $this->metadata['screenshot'];
		$this->metadata['safePathShot']		= $folderSafe . '/' . $this->metadata['screenshot'];
		$this->metadata['folderShot'] 		= $this->metadata['folder'] . '/' . $this->metadata['screenshot'];
		$this->metadata['UriShot']			= $this->metadata['basePathShot'];
		$this->metadata['shotHeight'] = sprintf ('%s-x%d.%s', $shotPathName, $shotHeight, $shotExtension);
		$this->metadata['basePathHeight'] = $path . '/' . $this->metadata['shotHeight'];
		
		$this->metadata['modelPath']	= sprintf ('%s/glTF/%s.gltf', $pathSafe, $folderSafe);
		$this->metadata['pathModel']	= $this->metadata['modelPath'];
		$this->metadata['pathGLB']		= sprintf ('%s/glTF-Binary/%s.glb', $pathSafe, $folderSafe);
		$this->metadata['hasGLB']		= file_exists ($this->metadata['pathGLB']);

		return;
	}

/*
 * Populate the 'variants' field for later export
 *	In the model directory, find all directories that start 'glTF'
 *	In each of those, file the first file that ends '.glb' or '.gltf' and use that ones
 *	The result is a structure containing at least one element of the form 'glTF*' with the value
 *		of a file name that ends '.glb' or '.gltf'
 */
 
	private function _findVariants () {
		// Get all directories of the form $this->metadata['path'] + '/glTF*
		// Search each of those for .glb or .gltf. 
		// Take first one
		$variants = array();
		$path = str_replace ('%20', ' ', $this->metadata['path']);

		$folder = dir ($path);
		while (false !== ($directory = $folder->read())) {
			if (substr($directory, 0, 4) == 'glTF') {
				$files = dir ($path . '/' . $directory);
				$looking = true;
				while ($looking && (false !== ($check = $files->read()))) {
					if (substr($check, -4, 4) == '.glb') {
						$variants[$directory] = $check;
						$looking = false;
					} else if (substr($check, -5, 5) == '.gltf') {
						$variants[$directory] = $check;
						$looking = false;
					}
				}
				$files->close();
			}
		}
		$folder->close();
		ksort($variants);
		$this->metadata['variants'] = $variants;
		return;
	}
/*
 * Cleans up license information
**/	
	private function _cleanupLicense ($rebuildSpdx=false) {
		if ($this->debugOutput >= $this->DebugDetail) {
			print "In _cleanupLicense\n";
			print_r($this->metadata);
			print_r($this->metadata['legal'][0]);
		}

		for ($ii=0; $ii<count($this->metadata['legal']); $ii++) {
			$license = $this->metadata['legal'][$ii]['license'];
			$link = (isset($this->metadata['legal'][$ii]['licenseUrl'])) ? $this->metadata['legal'][$ii]['licenseUrl'] : '';
			if ($rebuildSpdx || $license == 'CC0' || $link == '') {
				if (isset($this->LICENSE[$license])) {
					$link = $this->LICENSE[$license]['link'];
					$text = $this->LICENSE[$license]['text'];
					$spdx = $this->LICENSE[$license]['spdx'];
					$icon = $this->LICENSE[$license]['icon'];

				} else {			// Non-standard license
					$link = '';
					$text = $license;
					$spdx = '';
					$icon = '';
				}
				$this->metadata['legal'][$ii]['licenseUrl'] = $link;
				$this->metadata['legal'][$ii]['text'] = $text;
				$this->metadata['legal'][$ii]['spdx'] = $spdx;
				$this->metadata['legal'][$ii]['icon'] = $icon;
				$this->isCurrent = false;
			}
			//if ($this->metadata['legal'][$ii]['artist']
		}

		if ($this->debugOutput >= $this->DebugDetail) {
			print "At completion\n";
			print_r($this->metadata['legal'][0]);
			print "==================================\n\n";
		}
		return;
	}

/*
 * Generates the credit entry for this model
**/	
	private function _generateCredits () {
		$credit = array();
		for ($ii=0; $ii<count($this->metadata['legal']); $ii++) {
		if (! (isset($this->metadata['legal'][$ii]['artist']) && isset($this->metadata['legal'][$ii]['what']))) {
				$this->hasError = true;
				$credit[] = "E: Missing 'artist' or 'what' for index $ii";
			} else {
				$credit[] = sprintf ("&copy; %04d, %s. [%s](%s)", $this->metadata['legal'][$ii]['year'], $this->metadata['legal'][$ii]['owner'], $this->metadata['legal'][$ii]['text'], $this->metadata['legal'][$ii]['licenseUrl']);
				$credit[] = sprintf (" - %s for %s", $this->metadata['legal'][$ii]['artist'], $this->metadata['legal'][$ii]['what']);
			}
		}
		
		return $credit;
	}

/*
 * Updates the low-version JSON structure to match the current version
 * Information updated:
 *	Legal & Credits
**/	
	private function _updateMetadata () {
		$artist  = (isset($this->metadata->author) && $this->metadata->author != '') ? $this->metadata->author : '';
		$artist  = (isset($this->metadata->artist)) ? $this->metadata->artist : $artist;
		$artist  = ($artist == '') ? '_No Artist_' : $artist;
		$owner  = (isset($this->metadata->owner) && $this->metadata->owner != '') ? $this->metadata->owner : '_No Owner_';
		$license = (isset($this->metadata->license) && $this->metadata->license != '') ? $this->metadata->licenseText : '_No License_';
		$year = (isset($this->metadata->year) && $this->metadata->year > 1900) ? $this->metadata->year : 0;
		$legal = array ();
		$legal[] = array(
						'year'			=> $year,
						'owner'			=> $owner,
						'license'		=> $license,
						'licenseUrl'	=> '',
						'artist'		=> $artist,
						'what'			=> ''
					);
		$this->metadata['legal'] = $legal;
	}
	
// Reads the JSON model metadata file and returns the data structure
	private function _readJson ($fullFile) {
		$localName = str_replace ('%20', ' ', $fullFile);
		//print "Processing |$localName|\n";
		$jsonString = file_get_contents ($localName);
		if ($jsonString == '') {
			$jsonString = $this->metaJson;
		}
		return json_decode ($jsonString, true);
	}

// Adds file info to internal data structure
// This is necessary so the file can be overwritten later on
	private function _addFileInfo ($path, $file, $extension) {
		$unixPath = str_replace ('\\', '/', $path);
		$unixFull = sprintf ("%s/%s.%s", $unixPath, $file, $extension);
		$pieces = explode ('/', $unixPath);
		$this->metadata['path'] = $unixPath;
		$this->metadata['folder'] = $pieces[count($pieces)-1];
		$this->metadata['filename'] = $file;
		$this->metadata['fullPath'] = $unixFull;
		$this->metadata['baseReadmePath'] = $unixFull;
		$this->metadata['baseLicensePath'] = sprintf ("%s/LICENSE.md", $unixPath);
		$this->modelKey = $file;
		$this->modelName = $file;
		$this->isCurrent = true;
	}

// Creates an initial data structure when the source metadata does not exist
	private function _createInitial ($path) {
		if ($this->debugOutput >= $this->DebugModel) print " -- fixing $path\n";
		$pieces = explode ('/', $path);
		$this->metadata['name'] = $pieces[count($pieces)-1];
		$this->metadata['folder'] = $this->metadata['name'];
		$this->metadata['path'] = $path;
		$this->addTags (['issues']);
		$this->isCurrent = false;
		return $this->metadata;
	}


}

?>
