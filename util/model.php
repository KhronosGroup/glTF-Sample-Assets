<?php

/*
 * =======================================================================================================
 * =======================================================================================================
 * =======================================================================================================
 *
 * Utility to process Assets. 
 *	php model [--check] [--build] [<asset1> [<asset2> ...]]
 *
 * The application has several modes
 *	--check		Performs asset directory check. The specific checks are listed below
 *	--build		Builds the necessary support files in the asset directory
 *	--process-repo	Is unset if any assets are specified. If set, then all repo-wide files are generated.
 *	--no-update		Do not update model folders. This is probably only used for testing. It has no effect
 *						if 'check' is set. Will set 'build'
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

require './modelmetadata.php';

$ModelDirectory = './Models';	// Directory relative to root containing all models
$useUserModelTags = false;		// Update model tags
$useUserModelData = false;		// Update model metadata

/*
 * Define internal arrays. 
 *	$listings is a structure for managing supported tags. All supported tags & tag combinations
 *		need to be included here
**/
$listings = array (
					array('type'=>'List', 'path' =>"$ModelDirectory/", 'file'=>'Models.md',			'tags'=>array(),			'summary'=>'All models listed alphabetically.'),
					array('type'=>'List', 'path' =>"$ModelDirectory/", 'file'=>'Models-core.md',		'tags'=>array('core'),		'summary'=>'Models that only use the core glTF V2.0 features and capabilities.'),
					array('type'=>'List', 'path' =>"$ModelDirectory/", 'file'=>'Models-extension.md','tags'=>array('extension'),	'summary'=>'Models that use one or more extensions.'),
					array('type'=>'List', 'path' =>"$ModelDirectory/", 'file'=>'Models-issues.md',	'tags'=>array('issues'),	'summary'=>'Models with one or more issues with respect to ownership, license, or markings.'),
					array('type'=>'List', 'path' =>"$ModelDirectory/", 'file'=>'Models-showcase.md',	'tags'=>array('showcase'),	'summary'=>'Models that are featured in some glTF/Khronos publicity.'),
					array('type'=>'List', 'path' =>"$ModelDirectory/", 'file'=>'Models-testing.md',	'tags'=>array('testing'),	'summary'=>'Models that are used for testing various features or capabilities of importers, viewers, or converters.'),
					array('type'=>'List', 'path' =>"$ModelDirectory/", 'file'=>'Models-video.md',	'tags'=>array('video'),		'summary'=>'Models used in any glTF video tutorial.'),
					array('type'=>'List', 'path' =>"$ModelDirectory/", 'file'=>'Models-written.md',	'tags'=>array('written'),	'summary'=>'Models used in any written glTF tutorial or guide.')
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
print "Processing Command Args: \n";
print_r($runArgs);

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
 
if (isset($runArgs['check'])) {
	$errorCount = 0;
	for ($ii=0; $ii<count($allModels); $ii++) {
		$modelName = $allModels[$ii]->modelName;
		$issues = $allModels[$ii]->reportIssues();
		if (count($issues['error'])+count($issues['warning']) > 0) {
			if (!isset($runArgs['no-warn']) || (isset($runArgs['no-warn']) && count($issues['error']) > 0)) {
				print sprintf ("Checking %s (%d issues; %d errors / %d warnings)\n", $modelName, count($issues['error'])+count($issues['warning']), count($issues['error']), count($issues['warning']));
				for ($jj=0; $jj<count($issues['error']); $jj++) {
					print sprintf(" E-%d: %s\n", $jj+1, $issues['error'][$jj]);
				}
				for ($jj=0; $jj<count($issues['warning']); $jj++) {
					print sprintf(" W-%d: %s\n", $jj+1, $issues['warning'][$jj]);
				}
				$errorCount =+ count($issues['error']);
			}
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
if (!isset($runArgs['no-update'])) {
	print "Updating all model folders\n";
	updateAllModels ($allModels, $listings);
}

print "===============================\n";

/*
 * The following depend on having all models in the repo. These
 *	cannot be run if the processing of models was limited
 *
 *	Skip all of these unless --process-repo is specified
 */
 
if (isset($runArgs['process-repo'])) {
	print "Generating Repo files\n";
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
	print "Using basepath of ".$folder->path."\n";
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
	$clHelp = [	array('switch'=>'build',		'long'=>'build',		'short'=>'b', 'text'=>'Builds all necessary files for the asset.'),
				array('switch'=>'check',		'long'=>'check',		'short'=>'c', 'text'=>'Checks consistency of the asset directory files.'),
				array('switch'=>'help',			'long'=>'help',			'short'=>'h', 'text'=>'Displays this informaiton.'),
				array('switch'=>'no-update',	'long'=>'no-update', 	'short'=>'u', 'text'=>'Do not update model folders. It has no effect "check" is set. Will set "build".'),
				array('switch'=>'no-warn',		'long'=>'no-warn',		'short'=>'w', 'text'=>'Do not show warnings if there are no errors.'),
				array('switch'=>'process-repo',	'long'=>'process-repo',	'short'=>'p', 'text'=>'Create repo-wide files. Will set "build".'),
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
	if (isset($options['_values'][1]))				unset ($options['_values']['process-repo']);
	if (isset($options['_values']['process-repo']))	$options['_values']['build']=1;
	if (isset($options['_values']['no-update']))	$options['_values']['build']=1;
	if (isset($options['_values']['build']))		unset ($options['_values']['check']);

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
		$variants = array();
		foreach ($modelMeta['variants'] as $folder=>$file) {
			$variants[] = sprintf ('"%s": "%s"', $folder, $file);
		}
		$variant = (count($variants) < 1) ? '' : 
					"\n\t\t\t" . join(",\n\t\t\t", $variants) . "\n\t\t";
		$modelEntry = sprintf ("\t{\n\t\t\"name\": \"%s\",\n\t\t\"path\": \"%s\",\n\t\t\"tags\": [\"%s\"],\n\t\t\"variants\": {%s}\n\t}", $modelMeta['name'], $modelMeta['path'], join('","', $modelMeta['tags']), $variant);
		$modelEntry = sprintf ("\t{\n\t\t\"name\": \"%s\",\n\t\t\"path\": \"%s\",\n\t\t\"tags\": [\"%s\"],\n\t\t\"variants\": {%s}\n\t}", $modelMeta['name'], $modelMeta['path'], join('","', $modelMeta['tags']), $variant);
		$modelEntry = sprintf ("\t{\n\t\t\"label\": \"%s\",\n\t\t\"name\": \"%s\",\n\t\t\"screenshot\": \"%s\",\n\t\t\"path\": \"%s\",\n\t\t\"tags\": [\"%s\"],\n\t\t\"variants\": {%s}\n\t}", 
								$modelMeta['name'], 
								$modelMeta['folder'], 
								$modelMeta['screenshot'], 
								$modelMeta['path'], 
								join('","', $modelMeta['tags']), 
								$variant);
		fwrite ($F, $modelEntry);
		if ($ii == count($allModels)-1) {
			fwrite ($F, "\n");
			//print_r($modelMeta);
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
	
	$F = fopen ($tagStrcture['path'].$tagStrcture['file'], 'w');
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
##		$otherTags[] = sprintf ("[%s](%s%s) - %s", $tagItem, $listings[$ii]['path'], $listings[$ii]['file'], $listings[$ii]['summary']);
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
							$modelMeta['folder'].'/README.md',			// was 'path'
							$modelMeta['name'], 
							$modelMeta['folderShot'],
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