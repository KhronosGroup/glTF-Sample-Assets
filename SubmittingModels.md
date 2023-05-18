<p align="center">
<img src="2.0/glTF_RGB_June16.svg" height="100">
</p>

# Submitting New Models

[![glTF Validation](https://github.com/KhronosGroup/glTF-Sample-Models/workflows/glTF%20Validation/badge.svg?branch=master)](https://github.com/KhronosGroup/glTF-Sample-Models/actions)

We appreciate sample asset contributions; they help ensure a consistent glTF ecosystem.

## Summary of Requirements
There are a few things that are required for any contribution that are listed here. The details are below.
1. glTF model using glTF V2.0 plus any ratified extension
1. Screen shot of the model for the catalog
1. Description of the model and all important points for file structure, modeling, rendering, or animation.
1. Metadata about the model and asset files

## Requirement Details

To contribute a model, open a pull request with a new subdirectory containing the above listed items. many of the items go into subdirectories. The details are as follows:

1. glTF Model
    1. The model in as many glTF variations as reasonable (using the same directory structure as the others ([example](Models/Box))). Tools for converting to glTF are [here](https://github.com/KhronosGroup/glTF#converters).
    1. Each format should be in its own separate directory
    1. The model must use glTF Core V2.0 format and structure
    1. The model should only use ratified glTF extensions
1. A screenshot of the model, stored in a subdirectory called `/screenshot`
    1. The screenshot file should be called `screenshot` with an extension appropriate to the file format.
    1. The screenshot must include all of the model, preferably in the orientation when first displayed
        1. The largest dimension should be no more than 150 pixels
        1. The image file format should be the one that is smallest for the content. The preference is for JPEG formatted files, but PNG, WebP are also acceptable. If the image is animated, a GIF formatted file may be used to capture the animation.
1. The model description
    1. This is in Markdown format (`.md`).
    1. The filename must be README.body.md
    1. The description file must not include the following items. They are automatically provided by the build software.
        1. Top-level (`#`) tag for the model name
        1. Second-level (`##`) tag for the model tags
        1. Second-level (`##`) tag for the model summary
        1. Second-level (`##`) tag for the model legal, copyright, and license information
    1. The description file must include the following items.
        1. Second-level (`##`) tag for the model screenshot
        1. Second-level (`##`) tag for the model description
    1. The description should also have a screen shot. 
        1. This screen shot should have the horizontal dimension between 1000 to 2000 pixels
        1. The vertical dimension should be between 500 to 1000 pixels
        1. The model needs to retain the proper perspective.
        1. The file name may be anything but `screenshot`. `screenshot_large` is frequently used.
        1. The file must reside in the `screenshot` directory.

1. Metadata
    1. All metadata is stored in `metadata.json`. It contains various information about the model.
    1. There is an [experimental HTML application](../util/CreateJson.html) to assist in the creation of the Metadata file
    1. The Metadata file may be generated manually.
    1. The Metadata file will be automatically upgrade if needed during system upgrades.
    1. See **Example Metadata File** (below) for details

## Asset Licenses

Each asset requires detailed information about the asset. The information includes
* Copyright owner
* Copyright year
* License
* Credit name and work performed

An asset may have multiple copyrights and/or credits. For example, if Acme, Inc. created a model rocket and Wyle E Coyote animated it; there would be two copyrights, one for the model and one for the animation.

Assets to be incldued in the Sample Asset repository must have a license that allows Khronos to publish the asset and allow others to use the asset in public. Khronos recommends use of a permissive license like [Creative Commons Attribution 4.0 International License](http://creativecommons.org/licenses/by/4.0/) or even [Creative Commons 1.0 Universal Public Domain Dedication ](http://creativecommons.org/publicdomain/zero/1.0/). Assets with semi-restrictive licenses may be included in the repository provided arrangments are made prior to the Pull Request being posted.

The system will attemtp to list the copyrights with the oldest one first. Some manual adjustment may be necessary.

### Displayed License & Credit Format

The system can automatically generate a license and credit block in the format below. If you manually generate a README file, then please try to follow this format as much as possible. This item is repeated as many times as necessary. Put the oldest item first.

~~~
© <year>, <owner>. <license name> (with optional link to legal text)
 - <artist> for <what>
~~~

## Tags

All assets in the repository are assigned tags by the asset submittor and potentially by Repository managers. These tags allow others to easily find the asset and related assets. You may assign any tag you wish; however, only a limited number of tags are used to construct the summary displays. 

## Example Metadata File

The metadata file is always called metadata.json and is located in the model root directory (not the root directory of the repo). It specifies the necessary metadata of the model including its name, ownership, artist, license, and tags. The current version of the JSON structure is below. If you are unsure of the details, set the version number to less than the current and the system will automatically upgrade the file.

~~~
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
    "createReadme" : false
}
~~~

The _legal_ structure contains information about each owner of the model. It comprises of the following elements:

* _what_: What was done to get the model to this stage. Standard terminology includes _Everything_ or _Creation_ for the initial work; _Mesh_ for the geometry; _Texture_ for all materrials; _Animation_ for movement; and _Conversion_ for converting to glTF.
* _artist_: The name of the artist(s) who performed the _what_.
* _owner_: The owner of the model for this operation. This is may be the _artist_ or the organization responsible for the _artist_. If the work is in the Public Domain, then the _owner_ may be **Public**.
* _year_: The year the work was performed.
* _license_: The licensed assigned by the owner. Standard license shorthand should be used. The known shorthand is

  * CCO
  * PD
  * CC-BY / CC-BY International 4.0
  * CC-BY-ND
  * CC-BY-NC
  * CC-BY-NC-ND
  * _Other_ licenses may be used with agreement prior to submitting the Pull Request.
