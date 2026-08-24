<p align="center">
<img src="https://github.com/KhronosGroup/glTF-Sample-Assets/blob/main/Models/glTF_RGB_June16.svg" height="100">
</p>

# Managing Models in this Repository

We are actively accepting new or revised models for the Sample Assets repo. This keeps the repo active and relevant. All changes, whether they are new models or updates of existing ones, are handled as Pull Requests (PR) and must meet the same quality requirements.

## Model Quality Requirements

A summary of the quality requirements for models is given here. Details for submitting new models will be given in the next section.

- Each model is contained in a subdirectory of the `./Models` directory. This subdirectory contains all model variants and associated files.
- Each model must pass the [glTF-Validator](https://github.khronos.org/glTF-Validator/).
- Each model must have an associated `README.md` markdown file that describes the model and the features of the model that make it appropriate for this repo
- Each model must have an associated `metadata.json` file that includes legal information (ownership, copyright, and license) 
- Each model must have a properly formatted screenshot. For details, see the [screenshot section](#the-screenshot).

## Submitting New Models

New models are added via pull requests. Opening a pull request will run the continuous integration pipeline that includes the validation of the model itself (using the glTF-Validator), and a consistency check for the associated files. Each model must be contained in a subdirectory of the `./Models` directory. The name of this subdirectory should make it easy to identify the model. For example, a model representing an animated triangle may be in a subdirectory `./Models/AnimatedTriangle`.

### Required Files

#### The `metadata.json`

Each submission must include a `metadata.json` file in the subdirectory of the model. This file contains a description of the model that is used for properly integrating the model into the sample assets repository. The following is a _template_ for such a `metadata.json` file. The individual fields will be explained below:
```json
{
  "version": 2,
  "name": "",
  "path": "",
  "summary": "",
  "screenshot": "screenshot/screenshot.jpg",
  "tags": ["core"],
  "legal": [
    {
      "license": "",
      "year": "",
      "artist": "",
      "what": "",
      "owner": ""
    }
  ],
}
```

The meaning of the fields:

- `version`: The version for the metadata JSON format. Must be `2`
- `name`: A name for the model that will be used to refer to be model in titles and link descriptions. This will usually resemble the name of the subdirectory that contains the model. For example, the `name` of a model that is contained in `./Models/AnimatedTriangle` may be `"Animated Triangle"`.
- `path`: The path to the model, referring to the root of the repository. For example, `"./Models/AnimatedTriangle"`.
- `summary`: A short summary of the model, to be displayed in tables and overviews. It should usually be a single sentence or short paragraph, and _not_ a full description of the model. For example, a summary might be `"A simple triangle with a rotation animation"`.
- `screenshot`: The path to a screenshot that should be displayed for the model, suitable for being displayed in an overview table. 
- `tags`: An array of tags that are used for classifying the model. See the [Tags](#tags) section for the set of tags that are currently supported.
- `legal`: An array with at least one entry, summarizing the copyright information of the model. Details are given below.


Each element of the `legal` array contains information about the copyright and licensing of the model:

- `license`: The license assigned by the owner. This should be a valid [SPDX](https://spdx.org/licenses/) license identifier. See the [Licenses](#licenses) section for a list of supported licenses. Other, custom licenses may be used with agreement prior to submitting the Pull Request. See the section about [Custom Licenses](#custom-licenses) below.
- `year`: The year the work was created or modified.
- `artist`: The name of the artist(s) who created or modified (parts of) the model. 
* `what`: What was done by the artist. Standard terminology includes _Everything_ or _Creation_ for the initial work; _Mesh_ for the geometry; _Texture_ for all materials; _Animation_ for movement; and _Conversion_ for converting to glTF.
- `owner`: The owner of the model. This is may be the same as the `artist`, or the organization responsible for the `artist`. If the work is in the Public Domain, then the `owner` may be `"Public"`.


The information from the `legal` array will be used to create credits in the license files and summary tables, using the following pattern:
```
© <year>, <owner>. <license name> (with optional link to legal text)
 - <artist> for <what>
```


##### Custom Licenses

For the case that (parts of) the model should be published with a license that is not one of the standard licenses, the `legal` entry must have the following properties:

```json
{
  "license": "",  
  "text": "",
  "year": "",
  "artist": "",
  "what": "",
  "owner": ""
}
```

- `license`: An identifier for the license, e.g. `"LicenseRef-LegalMark-Khronos"`
- `text`: A short description or name of the license and what it refers to
- The remaining fields have the same meaning as for standard licenses (defined in the previous section)

Custom licenses must be stored as text files in the `LICENSES/` directory of the repository. The license text must be a file with the naming pattern _`<license>`_`.txt`. For example, such a license file could be `./LICENSES/LicenseRef-LegalMark-Khronos.txt`.


#### The Screenshot

Each model must have at least one associated screenshot. This should be a small image file that is suitable for being displayed in an overview table. The screenshot must be a PNG, JPG, WEBP, or (possibly animated) GIF file. The screenshot should have a width of roughly 150 to 500 pixels, and not be extremely elongated in one direction.

The screenshot must be in the `screenshot` subdirectory of the model. The path to this screenshot must be given in the `screenshot` property of the `metadata.json`. Additional (larger) screenshots may be contained in the `screenshot` directory, and used in the `README.body.md` file (explained below).


#### The README body

Each model must have a file that is called `README.body.md`. This file should contain additional details of the model. The contents of this file will automatically be inserted into the main `README.md` of the model. This `README.body.md` can contain additional subsections and text explaining the purpose and structure of the model. The lowest indentation level for the sections in this file should be `##`. This file may also refer to additional screenshots that may be included in the `screenshot` directory of the model. 

#### The Actual Model

Usually, models will be submitted in multiple variants. Each variant is stored in its own subdirectory within the model subdirectory. The following variants are commonly used:

- `glTF`: This is required to be present for all models. It represents the model in the "default" structure, consisting of one `.gltf` file, and one or more binary files for the buffers and images.
- `glTF-Binary`: This is the model as a glTF binary (`.glb`) file. This will usually be a single, complete, self-contained file without external references.
- `glTF-Embedded`: The model in the "embedded" representation. This is a `.gltf` file where the binary buffers and images are base64-encoded. _Note:_ This is discouraged for larger models. It should only be used for smaller test models.

Optionally, there may be additional variants. For example, for offering the model in compressed form. The following variant names are commonly used for compressed models:

- `glTF-Quantized`: Using [`KHR_mesh_quantization`](https://github.com/KhronosGroup/glTF/blob/main/extensions/2.0/Khronos/KHR_mesh_quantization)
- `glTF-Draco`: Using [`KHR_draco_mesh_compression`](https://github.com/KhronosGroup/glTF/blob/main/extensions/2.0/Khronos/KHR_draco_mesh_compression)
- `glTF-Meshopt`: Using [`KHR_meshopt_compression`](https://github.com/KhronosGroup/glTF/tree/main/extensions/2.0/Khronos/KHR_meshopt_compression)
- `glTF-Meshopt-EXT`: Using [`EXT_meshopt_compression`](https://github.com/KhronosGroup/glTF/tree/main/extensions/2.0/Vendor/EXT_meshopt_compression)
- `glTF-KTX-BasisU`: Using [`KHR_texture_basisu`](https://github.com/KhronosGroup/glTF/tree/main/extensions/2.0/Khronos/KHR_texture_basisu)
- `glTF-WEBP`: Using [`EXT_texture_webp`](https://github.com/KhronosGroup/glTF/tree/main/extensions/2.0/Vendor/EXT_texture_webp)

Other variant names may be introduced for specific forms of models, e.g. for different combinations of compression methods. Details should be discussed in the Pull Request.



## Appendix A: Metadata JSON fields

Details about the fields that are allowed to appear in the `metadata.json`.

### Tags

The following tags are currently recognized as elements of the `tags` array in the `metadata.json`

- `core`: Models that only use the core glTF 2.0 features and capabilities.
- `extension`: Models that use one or more extensions.
- `showcase`: Models that are featured in some glTF/Khronos publicity.
- `testing`: Models that are used for testing various features or capabilities of importers, viewers, or converters.
- `pbrtest`: Models that are used for illustrating the effect of PBR properties.
- `video`: Models used in any glTF video tutorial.
- `written`: Models used in any written glTF tutorial or guide.
- `issues` (only used internally): Models with one or more issues with respect to ownership, license, or markings.


### Licenses

The following licenses are currently supported. Their 'SPDX identifier' can be used as the `license` property of a `legal` entry in the `metadata.json`:

| SPDX identifier | Text | Link |
| :--- | :--- | :--- |
| CC0-1.0 | Creative Commons Zero v1.0 Universal | https://creativecommons.org/publicdomain/zero/1.0/legalcode |
| CC-BY-4.0 | Creative Commons Attribution 4.0 International | https://creativecommons.org/licenses/by/4.0/legalcode |
| CC-BY-NC-4.0 | Creative Commons Attribution Non Commercial 4.0 International | https://creativecommons.org/licenses/by-nc/4.0/legalcode |
| CC-BY-NC-SA-4.0 | Creative Commons, Attribution-NonCommercial-ShareAlike 4.0 International | https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode |


## Appendix B: AI-Assisted Contributions

By submitting a Contribution to this repository, you additionally represent that, to the extent any of Your Contributions were developed with the assistance of artificial intelligence tools or AI-generated code, You have exercised sufficient review, judgment, and creative direction over such tools and resulting material to reasonably consider it Your original creation, and You are not aware of any third-party license, intellectual property claim, or other restriction arising from such use that is associated with any part of Your Contribution or use thereof.
