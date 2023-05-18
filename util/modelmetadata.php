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
	define ('AppVersion', '1.0.11-beta');
	define ('UrlSampleViewer', 'https://github.khronos.org/glTF-Sample-Viewer-Release/');
	define ('UrlModelRepoRaw', 'https://raw.GithubUserContent.com/DRx3D/glTF-Sample-Assets/main');
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
		$fullFile = $path . '/' . $file . '.json';
		if (!file_exists ($fullFile)) {
			$this->hasError = true;
			$this->errorMessage = "File not found: $fullFile";
			$this->metadata = $this->_createInitial ($path);
			$this->hasError = false;
		} else {
			if ($this->debugOutput >= $this->DebugDetail) 
				print "Loading $fullFile and storing in ->metadata\n";
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

		$this->_populateInternal ();
		$this->hasError = false;
		$this->errorMessage = "";
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

		if (!$this->isCurrent) $errors[] = 'JSON not current and automatically updated';
		if ($this->metadata['summary'] == '' || str_starts_with ($this->metadata['summary'], '_')) $errors[] = 'No asset summary';
		if ($this->metadata['legal'][0]['year'] <= 1920) $errors[] = 'Invalid Copyright year';
		if ($this->metadata['legal'][0]['owner'] == '' || str_starts_with ($this->metadata['legal'][0]['owner'], '_')) $errors[] = 'Missing  Copyright owner';
		if ($this->metadata['legal'][0]['license'] == '' || str_starts_with ($this->metadata['legal'][0]['license'], '_')) $errors[] = 'Missing license';
		if ($this->metadata['legal'][0]['license'] != 'Public Domain / CC0') {
			if ($this->metadata['legal'][0]['artist'] == '' || str_starts_with ($this->metadata['legal'][0]['artist'], '_')) $errors[] = 'Missing artist';
			if ($this->metadata['legal'][0]['what'] == '' || str_starts_with ($this->metadata['legal'][0]['what'], '_')) $errors[] = 'Missing work (what)';
		}
		
		if (!file_exists($this->metadata['basePathShot'])) {
			$errors[] = 'Screenshot file not found';
		} else {		// Check for proper size
		}
		
		if ($this->metadata['createReadme']) {
			// Verify that existing README.md is autogenerated
			$fileReadme = $this->metadata['basePath'] . 'README.md';
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
		} else {
			$warnings[] = 'README is not auto-generated';
		}
		
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
		$fileReadmeBody = $this->metadata['basePath'] . 'README.body.md';
		if (!$this->metadata['createReadme']) {return $this; }
		if ($this->debugOutput >= DebugModel) 
			print " .. Updating README\n";
		
		$screenshot = $this->metadata['screenshot'];
		$tagList = array();
		for ($ii=0; $ii<count($this->metadata['tags']); $ii++) {
			$path = $this->_getTagListingPath ($this->metadata['tags'][$ii], $tagListings);
			if ($path == '') {
				$tagList[] = sprintf ('%s', $this->metadata['tags'][$ii], $this->metadata['tags'][$ii]);
			} else {
				$tagList[] = sprintf ('![%s](../../%s)', $this->metadata['tags'][$ii], $path);
			}
		}
		$tagString = join (', ', $tagList);

		$readme = array();
		$readme[] = '# ' . $this->modelName;
		$readme[] = "## Tags";
		$readme[] = $tagString;
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
		$readme[] = "#### Assembled by " . AppName . ' ' . AppVersion;
		$output = join ("\n\n", $readme);

		if ($this->debugOutput >= $this->DebugModel) 
			print " .. writing README to $fileReadme\n";
		$FO = fopen ($fileReadme, 'w');
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
		$readme[] = '  * [' . $this->metadata['legal'][0]['text'] . ']("' . $this->metadata['legal'][0]['licenseUrl'] . '") [SPDX license identifier: "' . $this->metadata['legal'][0]['spdx'] . '"]';
		$readme[] = '* This file and all other metadocumentation files including "metadata.json":';
		$readme[] = '  * [Creative Commons Attribtution 4.0 International]("'.$this->LICENSE['CC-BY 4.0']['link'].'") [SPDX license identifier: "CC-BY-4.0"]';
		$readme[] = 'Full license text of these licenses are available at the links above';
		$readme[] = "#### Generated by " . AppName . ' ' . AppVersion;
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
		$this->hasError = false;
		$this->errorMessage = "";
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

	
// Populates internal values from the read in ones
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
		$this->metadata['credit']		= $this->_generateCredits();
		$this->metadata['summary'] = ($this->metadata['summary'] == '') ? '_No Summary_' : $this->metadata['summary'];
		$this->_handleScreenshot();

		return;
	}

/*
 * Handles the screenshot for this model
**/	
	private function _handleScreenshot () {
		$path = $this->metadata['path'];
		$path = str_replace (' ', '%20', $path);
		$this->metadata['path'] = $path;

		$tmp = explode ('/', $path);				// Get the model directory. It is 
		$modelDirectory = $tmp[count($tmp)-1];		// the last item in $path
		
		$shotHeight = 150;
		$screenshot = $this->metadata['screenshot'];
		$tmp = explode ('.', $screenshot);
		$shotPathName = $tmp[0];
		$shotExtension = $tmp[1];

// basePath* is from the repo root directory
// path* is from the model directory
		$this->metadata['screenshotType'] = $shotExtension;
		$this->metadata['basePath'] = $path . '/';
		$this->metadata['basePathModel'] = 'path-to-model';
		$this->metadata['basePathShot'] = $path . '/' . $this->metadata['screenshot'];
		$this->metadata['UriShot'] = $this->metadata['basePathShot'];
		$this->metadata['shotHeight'] = sprintf ('%s-x%d.%s', $shotPathName, $shotHeight, $shotExtension);
		$this->metadata['basePathHeight'] = $path . '/' . $this->metadata['shotHeight'];
		
		$this->metadata['modelPath']	= sprintf ('%s/glTF/%s.gltf', $path, $modelDirectory);
		$this->metadata['pathModel']	= $this->metadata['modelPath'];
		$this->metadata['pathGLB']		= sprintf ('%s/glTF-Binary/%s.glb', $path, $modelDirectory);
		$this->metadata['hasGLB']		= file_exists ($this->metadata['pathGLB']);

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
			if ($rebuildSpdx || $link == '') {
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
			}
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
			$credit[] = sprintf ("&copy; %04d, %s. [%s](%s)", $this->metadata['legal'][$ii]['year'], $this->metadata['legal'][$ii]['owner'], $this->metadata['legal'][$ii]['text'], $this->metadata['legal'][$ii]['licenseUrl']);
			$credit[] = sprintf (" - %s for %s", $this->metadata['legal'][$ii]['artist'], $this->metadata['legal'][$ii]['what']);
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
		$jsonString = file_get_contents ($fullFile);
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
		$this->metadata['path'] = $unixPath;
		$this->metadata['filename'] = $file;
		$this->metadata['fullPath'] = $unixFull;
		$this->metadata['baseReadmePath'] = $unixFull;
		$this->metadata['baseLicensePath'] = sprintf ("%s/LICENSE.md", $unixPath);
		$this->modelKey = $file;
		$this->modelName = $file;
		$this->isCurrent = true;
	}

// Creates an initial data structure when the source metadata does not existing	
	private function _createInitial ($path) {
		if ($this->debugOutput >= $this->DebugModel) print " -- fixing $path\n";
		$pieces = explode ('/', $path);
		$this->metadata['name'] = $pieces[count($pieces)-1];
		$this->metadata['path'] = $path;
		$this->addTags (['issues']);
		$this->isCurrent = false;
		return $this->metadata;
	}


}

/*
 * =======================================================================================================
 * =======================================================================================================
 * =======================================================================================================
 *
 * Utility to process Assets. 
 *	php modelmetadata [--check] [--build] [<asset1> [<asset2> ...]]
 *
 * The application has several modes
 *	--check		Performs asset directory check. The specific checks are listed below
 *	--build		Builds the necessary support files in the asset directory
 *
 *	Assets
 *		By default this application runs on all directories in $ModelDirectory (defined below)
 *		Assets may be checked individually by specifying them on the command line. Do not include 
 *		  all directories in $ModelDirectory. 
 *
 * Processing control flags (these should eventually be moved to command line parameters)
 * These are only used when doing a mass update or conversion
 *	$useUserModelTags - reads the model tag update file (ModelRepoTagData.csv). See getModelTagData
 ^	$useUserModelData - reads the model metadata update file (). See getModelData
**/
$ModelDirectory = './Models';	// Directory relative to root containing all models
$useUserModelTags = false;		// Update model tags
$useUserModelData = false;		// Update model metadata

/*
 * Define internal arrays. 
 *	$listings is a structure for managing supported tags. All supported tags & tag combinations
 *		need to be included here
**/
$listings = array (
					array('type'=>'List', 'file'=>'Models.md', 'tags'=>array(), 'summary'=>'All models listed alphabetically.'),
					array('type'=>'List', 'file'=>'Models-core.md', 'tags'=>array('core'), 'summary'=>'Models that only use the core glTF V2.0 features and capabilities.'),
					array('type'=>'List', 'file'=>'Models-extension.md', 'tags'=>array('extension'), 'summary'=>'Models that use one or more extensions.'),
					array('type'=>'List', 'file'=>'Models-issues.md', 'tags'=>array('issues'), 'summary'=>'Models with one or more issues with respect to ownership, license, or markings.'),
					array('type'=>'List', 'file'=>'Models-showcase.md', 'tags'=>array('showcase'), 'summary'=>'Models that are featured in some glTF/Khronos publicity.'),
					array('type'=>'List', 'file'=>'Models-testing.md', 'tags'=>array('testing'), 'summary'=>'Models that are used for testing various features or capabilities of importers, viewers, or converters.'),
					array('type'=>'List', 'file'=>'Models-video.md', 'tags'=>array('video'), 'summary'=>'Models used in any glTF video tutorial.'),
					array('type'=>'List', 'file'=>'Models-written.md', 'tags'=>array('written'), 'summary'=>'Models used in any written glTF tutorial or guide.')
					);
$tagList = [];
for ($ii=0; $ii<count($listings); $ii++) {
	for ($jj=0; $jj<count($listings[$ii]['tags']); $jj++) {
		$tagList[] = $listings[$ii]['tags'][$jj];
	}
}
$tagList = array_unique ($tagList);

// Process Command Line arguments
$runArgs = clProcess($argv, $ModelDirectory);

// Set the correct directory
$directories = explode (DIRECTORY_SEPARATOR, getcwd());
if ($directories[count($directories)-1] == 'util') chdir('..');

// Get list of all assets to process
$assetList = getlistRequestedAssets ($runArgs, $ModelDirectory);

// Load all model objects
$allModels = getAllModels ($assetList);

/*
 * Mode block operations
 *	--check on looks at things
 *	--build updates existing files and may create or replace others
 *
 * --check is the default
 */
 
if (isset($runArgs['check']) || !isset($runArgs['build'])) {
	$errorCount = 0;
	for ($ii=0; $ii<count($allModels); $ii++) {
		$modelName = $allModels[$ii]->modelName;
		$issues = $allModels[$ii]->reportIssues();
		if (count($issues['error'])+count($issues['warning']) > 0) {
			print sprintf ("Checking %s (%d issues; %d errors, %d warnings)\n", $modelName, count($issues['error'])+count($issues['warning']), count($issues['error']), count($issues['warning']));
			for ($jj=0; $jj<count($issues['error']); $jj++) {
				print sprintf(" E-%d: %s\n", $jj+1, $issues['error'][$jj]);
			}
			for ($jj=0; $jj<count($issues['warning']); $jj++) {
				print sprintf(" W-%d: %s\n", $jj+1, $issues['warning'][$jj]);
			}
			$errorCount =+ count($issues['error']);
		}
	}
	
	exit (($errorCount == 0) ? 0 : 1);
}


// If requested load the user input metadata for each model. 
if ($useUserModelData) {
	$modelMetadata = getModelData();
	$allModels = updateModelsMetadata ($allModels, $modelMetadata, $listings);
}
// If requested load the user tag settings for each model. 
if ($useUserModelTags) {
	$modelTagData = getModelTagData();
	$allModels = updateModelsTags ($allModels, $modelTagData, $listings);
}

//	Update all model support files
updateAllModels ($allModels, $listings);

print "===============================\n";

// Now create various Repo files
for ($ii=0; $ii<count($listings); $ii++) {
	createReadme ($listings[$ii], $allModels, $listings, $listings[$ii]['tags']);
}

// Create repo-wide listing file
createModelList ($allModels);

// Create repo-wide license file
createDep5 ($allModels);

/*
 * Create CSV file for handling model tags. 
 *	This structure can be used on input with $useUserModelTags
 *	It can also be built from allModels.json (see createModelList)
**/
if (false) {
	createTagCsv ('ModelTags.csv', $allModels, $tagList);
}

exit;

function getlistRequestedAssets ($clParameters, $modelFolder='') {
	if ($modelFolder == '') {return null;}

	$acceptAll = false;
	if (!isset($clParameters[1])|| $clParameters[1] == '' || $clParameters[1] == '*') {
		$acceptAll = true;
	}

	$useModels = array();
	$folder = dir ($modelFolder);
	while (false !== ($model = $folder->read())) {
		$modelDir = $folder->path . '/' . $model;
		if (is_dir($modelDir) && !($model == '.' || $model == '..')) {
			if ($acceptAll || in_array($model, $clParameters)) {
				$useModels[] = $modelDir;
			}
		}
	}
	$folder->close();
	return $useModels;
}

/*
 * Pocess the command line
 *
 *	Processing includes handling the HELP model
 *	Return is an array of switches (stored by 'switch' name) and
 *		position arguments
 */
function clProcess($argv, $ModelDirectory) {
	$clHelp = [	array('switch' => 'build', 'long' => 'build', 'short'=>'b', 'text' => 'Builds all necessary files for the asset.'),
				array('switch' => 'check', 'long' => 'check', 'short'=>'c', 'text' => 'Checks consistency of the asset directory files.'),
				array('switch' => 'help',  'long' => 'help', 'short'=>'h', 'text' => 'Displays this informaiton'),
				];
	$options = array();
	$longOptions = array();
	$shortOptions = '';
	for ($ii=0; $ii<count($clHelp); $ii++) {
		$longOptions[] = $clHelp[$ii]['long'];
		$shortOptions .= $clHelp[$ii]['short'];
		$options[$clHelp[$ii]['long']] = $clHelp[$ii]['switch'];
		$options[$clHelp[$ii]['short']] = $clHelp[$ii]['switch'];
	}

	$clOptions = getopt ($shortOptions, $longOptions, $rest_index);
	$clParameters = array_slice ($argv, $rest_index);

	foreach ($clOptions as $key=>$value) {
		$options['_values'][$options[$key]] = $value;
	}
	$options['_values'][0] = $argv[0];
	for ($ii=0; $ii<count($clParameters); $ii++) {
		$options['_values'][] = $clParameters[$ii];
	}

// Handle --help
	if (isset($options['_values']['help'])) {
		echo ($argv[0] . " [--options] [assets]\n");
		for ($ii=0; $ii<count($clHelp); $ii++) {
			echo (sprintf (" --%-10s %s\n", $clHelp[$ii]['switch'], $clHelp[$ii]['text']));
		}
		echo (sprintf ("  %-11s %s\n", '[asset]', "Folder name in $ModelDirectory to process"));
		exit (0);
	}
	return $options['_values'];
}

// ---------------------------------------------------------------------------------------------

/*
 * Create repo-wide listing file
 *	This JSON file is an array with one entry per model
 *	Each entry contains the model name, relative path, and tags for that model
**/
function createModelList ($allModels) {
	$F = fopen ('./allModels.json', 'w');
	fwrite ($F, "[\n");
	for ($ii=0; $ii<count($allModels); $ii++) {
		$modelMeta = $allModels[$ii]->getMetadata();
		$modelEntry = sprintf ("\t{\n\t\t\"name\": \"%s\",\n\t\t\"path\": \"%s\",\n\t\t\"tags\": [\"%s\"]\n\t}", $modelMeta['name'], $modelMeta['path'], join('","', $modelMeta['tags']));
		fwrite ($F, $modelEntry);
		if ($ii == count($allModels)-1) {
			fwrite ($F, "\n");
		} else {
			fwrite ($F, ",\n");
		}
	}
	fwrite ($F, "]\n");
	fclose ($F);
	return;
}


// Create repo-wide license info.
//	This file ALWAYS goes in <root>/.reuse/dep5
function createDep5 ($allModels) {
	$F = fopen ('./.reuse/dep5', 'w');
	fwrite ($F, "Format: https://www.debian.org/doc/packaging-manuals/copyright-format/1.0/\n");
	fwrite ($F, "Source: glTF V2.0 models from various sources collected into a Repo\n");
	fwrite ($F, "Upstream-Name: glTF V2.0 Model Repo\n");
	fwrite ($F, "Upstream-Contact: https://GitHub.com/KhronosGroup/glTF-Sample-Models/\n");
	fwrite ($F, "Copyright: 2017-2023 Khronos Group\n");
	fwrite ($F, "License: CC-BY-4.0\n\n");

	fwrite ($F, "Files: *\n");
	fwrite ($F, "Copyright: 2017-2023 Khronos Group\n");
	fwrite ($F, "License: CC-BY-4.0\n\n");

	for ($ii=0; $ii<count($allModels); $ii++) {
		$modelMeta = $allModels[$ii]->getMetadata();
		fwrite ($F, sprintf ("Files: %s/*\n", substr($modelMeta['path'], 2)));
		$copyright = [];
		$license = array();
		$licenseLast = '';
		for ($jj=0; $jj<count($modelMeta['legal']); $jj++) {
			$copyright[] = sprintf ("%4d %s", $modelMeta['legal'][$jj]['year'], $modelMeta['legal'][$jj]['owner']);
			if ($modelMeta['legal'][$jj]['spdx'] != $licenseLast) {
				$license[] = $modelMeta['legal'][$jj]['spdx'];
				$licenseLast = $modelMeta['legal'][$jj]['spdx'];
			}
		}
		fwrite ($F, sprintf ("Copyright: \n %s\n", join("\n ", $copyright)));
		fwrite ($F, sprintf ("License: %s\n", join(' AND ', $license)));
		fwrite ($F, "\n");
		if (count($license) < 1) {
			print "Missing license for " . $modelMeta['name'] . "\n";
			print_r ($modelMeta);
		}
	}
	fclose ($F);
	return;
}

// Function for creating READMEs
function createReadme ($tagStrcture, $metaAll, $listings, $tags=array('')) {
	
	$F = fopen ($tagStrcture['file'], 'w');
	$section = 'Tagged...';
	if (count($tags) == 0 || $tags[0] == '') {
		$section = 'All models';
		$singleTag = '';
	} else {		$section = 'Models tagged with **' . join(', ', $tags) . '**';
		$singleTag = $tags[0];
	}
	$type = $tagStrcture['type'];
	print "Generating $type for $section\n";
	
	fwrite ($F, "# glTF 2.0 Sample Assets\n\n");
	fwrite ($F, "## $section\n\n");
	fwrite ($F, $tagStrcture['summary']."\n\n");
	
	for ($ii=0; $ii<count($listings); $ii++) {
		if (count($listings[$ii]['tags']) > 0) {
			$tagItem = '#' . join(', #', $listings[$ii]['tags']);
		} else {
			$tagItem = '#all';
		}
		$otherTags[] = sprintf ("[%s](%s) - %s", $tagItem, $listings[$ii]['file'], $listings[$ii]['summary']);
	}
	fwrite ($F, "## Other Tagged Listings\n\n");
	fwrite ($F, "* " . join("\n* ", $otherTags) . "\n\n");

	if ($type == 'Image') {
		$fmtString = "[![%s](%s)](%s)\n";
		for ($ii=0; $ii<count($metaAll); $ii++) {
			fwrite ($F, sprintf ($fmtString, 
						$metaAll[$ii]->{'name'}, 
						$metaAll[$ii]->{'UriHeight'},
						$metaAll[$ii]->{'UriReadme'}
						));
		}

	} else if ($type == 'Detailed') {
		fwrite ($F, "| Model   | Legal | Description |\n");
		fwrite ($F, "|---------|-------|-------------|\n");
		$fmtString = "| [%s](%s) <br> ![](%s) | %s | %s |\n";

		for ($ii=0; $ii<count($metaAll); $ii++) {
			$modelMeta = $metaAll[$ii]->getMetadata();
			$summary = ($modelMeta['summary'] == '') ? '**NO DESCRIPTION**' : $modelMeta['summary'];

			fwrite ($F, sprintf ($fmtString, 
						$modelMeta['name'], 
						$modelMeta['path'].'/README.md',
						$modelMeta['basePathShot'],
						join("<br>", $modelMeta['credit']),
						$summary,
						));
		}
	} else if ($type == 'List') {
		fwrite ($F, "| Model   | Description |\n");
		fwrite ($F, "|---------|-------------|\n");
		$fmtString = "| [%s](%s)<br>[![%s](%s)](%s)<br>[Show in Sample Viewer](%s?model=%s/%s) | %s<br>Credit:<br>%s |\n";
		$fmtColumn1 = "| [%s](%s)<br>[![%s](%s)](%s)<br>[Show](%s?model=%s/%s) ";
		$fmtColumn2 = "| %s<br>Credit:<br>%s |\n";
		$fmtDownload = "-- [Download GLB](%s/%s) ";

		for ($ii=0; $ii<count($metaAll); $ii++) {
			$modelMeta = $metaAll[$ii]->getMetadata();
			if ($singleTag == '' || (is_array($modelMeta['tags']) && in_array($singleTag, $modelMeta['tags']))) {
				$summary = ($modelMeta['summary'] == '') ? '**NO DESCRIPTION**' : $modelMeta['summary'];
				$pathModel = ($modelMeta['hasGLB']) ? $modelMeta['pathGLB'] : $modelMeta['pathModel'];

				fwrite ($F, sprintf ($fmtColumn1, 
							$modelMeta['name'], 
							$modelMeta['path'].'/README.md',
							$modelMeta['name'], 
							$modelMeta['basePathShot'],
							$modelMeta['path'].'/README.md',
							UrlSampleViewer, UrlModelRepoRaw, $pathModel
							));
				if ($modelMeta['hasGLB']) {
					fwrite ($F, sprintf ($fmtDownload, 
								UrlModelRepoRaw, $modelMeta['pathGLB']
								));
				}
				fwrite ($F, sprintf ($fmtColumn2, 
							$summary,
							join("<br>", $modelMeta['credit']),
							));
			}
		}
	}
	fwrite ($F, "---\n");
	fwrite ($F, sprintf ("\n### Copyright\n\n&copy; %d, The Khronos Group.\n\n**License:** [Creative Commons Attribtution 4.0 International](%s)\n", 2023, $metaAll[0]->LICENSE['CC-BY 4.0']['link']));
	fwrite ($F, sprintf ("\n#### Generated by %s v%s\n", AppName, AppVersion));

	fclose ($F);
	return;
}

// Function for creating a list of tags per model
function createTagCsv ($fname, $metaAll, $tagList) {
	$tagMaster = array();
	for ($ii=0; $ii<count($tagList); $ii++) {
		$tagMaster[] = 'FALSE';
	}
	$F = fopen ($fname, 'w');
	fwrite ($F, sprintf("\"%s\",\"%s\"\n", 'Model Name', join('","', $tagList)));
	for ($ii=0; $ii<count($metaAll); $ii++) {
		$modelMeta = $metaAll[$ii]->getMetadata();
		$tags = $tagMaster;
		if (is_array($modelMeta['tags'])) {
			for ($jj=0; $jj<count($tagList); $jj++) {
				$tags[$jj] = (preg_grep("/$tagList[$jj]/i",$modelMeta['tags'])) ? 'TRUE' : 'FALSE';
			}
		}
		fwrite ($F, sprintf("\"%s\",%s\n", $modelMeta['name'], join(',', $tags)));
	}
	fclose ($F);
	return;
}
	



/*
 * Update tags of all models.
 *	Metadata of all models is reflect in the new tag set.
 *	These are replacement tags (existing tags are removed)
 *	Readme file may be updated
 *
 *	Arguments
 *		$allModels - array of model objects (see getAllModels)
 *		$modelsTags	 - hash of model tags. All models need to have an entry in $modelsTags referred by modelName.
 *		$tagListings - Data structure of supported tags. 
 *
 */

function updateModelsMetadata ($allModels, $modelUpdateMetadata, $tagListings) {

	for ($ii=0; $ii<count($allModels); $ii++) {
		$modelName = $allModels[$ii]->modelName;
		print "\nMetadata processing $modelName\n";
		if ($modelUpData[$modelName]['UpdateLegal'] != 'FALSE') {
			$allModels[$ii] = $allModels[$ii]
								->addLicense ( array(
										'license'=>$modelUpData[$modelName]['License'],
										'licenseUrl'=>'', 
										'artist'=>$modelUpData[$modelName]['Author'],
										'owner'=>$modelUpData[$modelName]['Owner'],
										'year'=>$modelUpData[$modelName]['Year'],
										'what'=>'Everything'),
									true)
								->setWriteReadme ($modelUpData[$modelName]['AutoGenerateREADME']);
		}
		$allModels[$ii] = $allModels[$ii]
								->setSummary ($modelUpData[$modelName]['Summary'])
								->writeMetadata()
								->writeReadme($tagListings)
								->writeLicense();
	}
	return $allModels;
}
/*
 * Update tags of all models.
 *	Metadata of all models is reflect in the new tag set.
 *	These are replacement tags (existing tags are removed)
 *	Readme file may be updated
 *
 *	Arguments
 *		$allModels - array of model objects (see getAllModels)
 *		$modelsTags	 - hash of model tags. All models need to have an entry in $modelsTags referred by modelName.
 *		$tagListings - Data structure of supported tags. 
 *
 */
function updateModelsTags ($allModels, $modelsTags, $tagListings) {

	for ($ii=0; $ii<count($allModels); $ii++) {
		$modelName = $allModels[$ii]->modelName;
		if (isset($modelsTags[$modelName])) {
			print "\nTag adding [$modelName]: ".join(',',$modelsTags[$modelName])."\n";
			$allModels[$ii] = $allModels[$ii]->addTags ($modelsTags[$modelName]);
		}
	}
	return $allModels;
}

/*
 * Get all models into a single data structure (array of hashes of ...)
 *	This routine processes each model and performs internal updates
 *	License, Metadata, and Readme files may (or will) be updated
 *
 *	Model data array (of model objects) is returned
**/
function updateAllModels ($allModels, $tagListings) {
	for ($ii=0; $ii<count($allModels); $ii++) {
		//print "Save model " . $allModels[$ii]->modelName . "\n";
		$allModels[$ii] = $allModels[$ii]
								->writeMetadata()
								->writeReadme($tagListings)
								->writeLicense();
	}
}

//function getAllModels ($tagListings, $modelFolder='') {
function getAllModels ($useModels) {

	$allModels = array();
	for ($ii=0; $ii<count($useModels); $ii++) {
		$modelDir = $useModels[$ii];
		//print "Processing $modelDir\n";
		$mm = new ModelMetadata($modelDir, 'metadata');
		if ($mm->hasError) {
			print $mm->errorMessage."\n";
			exit;
		}
		//$mm = $mm->setNotCurrent();
		$allModels[] = $mm;
	}
	return $allModels;
}

/*
 * Returns a hash of a hash of the CSV containing the updated model data
 *	Primary Key is model name. Secondary keys are the column name that corresponds to the JSON field
 *	This data generally replaces the license from the JSON metadata
**/
function getModelData() {
	$dataFile = 'ModelRepoData.csv';
	$FH = fopen ($dataFile, "r");
	$ModelData = array();
	$keys = fgetcsv($FH, 5000);
	while (($row = fgetcsv($FH, 5000)) !== false) { 
		$new = array();
		for ($ii=0; $ii<count($row); $ii++) {
			$new[$keys[$ii]] = $row[$ii];
		}
		$ModelData[$new['Key']] = $new;
	}
	fclose ($FH);
	return $ModelData;
}

/*
 * Returns a hash of a hash of the CSV containing the updated model tag data
 *	Primary Key is model name. Each primary key contains an array of tags for that model
**/
function getModelTagData() {
	$dataFile = 'ModelRepoTagData.csv';
	$FH = fopen ($dataFile, "r");
	$ModelData = array();
	$keys = fgetcsv($FH, 5000);
	while (($row = fgetcsv($FH, 5000)) !== false) { 
		$new = array();
		for ($ii=1; $ii<count($row); $ii++) {
			if ($row[$ii] == 'TRUE') {
				$new[] = $keys[$ii];
			}
		}
		$ModelData[$row[0]] = $new;
	}
	fclose ($FH);
	return $ModelData;
}
?>
