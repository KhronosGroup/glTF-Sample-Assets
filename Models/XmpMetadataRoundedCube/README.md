# Xmp Metadata Rounded Cube

## Tags

[extension](../../Models-extension.md), [testing](../../Models-testing.md)

## Extensions Used

* KHR_xmp_json_ld

## Summary

Test of the XMP metadata extension - KHR_xmp_json_ld.

## Operations

* [Display](https://github.khronos.org/glTF-Sample-Viewer-Release/?model=https://raw.GithubUserContent.com/KhronosGroup/glTF-Sample-Assets/main/./Models/XmpMetadataRoundedCube/glTF-Binary/XmpMetadataRoundedCube.glb) in SampleViewer
* [Download GLB](https://raw.GithubUserContent.com/KhronosGroup/glTF-Sample-Assets/main/./Models/XmpMetadataRoundedCube/glTF-Binary/XmpMetadataRoundedCube.glb)
* [Model Directory](./)

## Description

This model shows how XML metadata is stored with the KHR_xmp_json_ld extension.

## Sample XMP JSON

```json
{
  "extensionsUsed": ["KHR_xmp_json_ld"],
  "extensions": {
    "KHR_xmp_json_ld": {
      "packets": [
        {
          "@context": {
            "dc": "http://purl.org/dc/elements/1.1/",
            "rdf": "http://www.w3.org/1999/02/22-rdf-syntax-ns#"
          },
          "@id": "",
          "dc:contributor": {
            "@set": [
              "Creator1Name",
              "Creator2Email@email.com",
              "Creator3Name<Email@email.com>"
            ]
          },
          "dc:coverage": "Bay Area, California, United States",
          "dc:creator": {
            "@list": ["CreatorName", "CreatorEmail@email.com"]
          },
          "dc:date": {
            "@list": ["2019-05-16T19:20:30+01:00"]
          },
          "dc:description": {
            "@type": "rdf:Alt",
            "rdf:_1": {
              "@language": "en-us",
              "@value": "An example of a glTF file with XMP metadata in it"
            }
          },
          "dc:format": "model/gltf+json",
          "dc:language": {
            "@set": ["en"]
          },
          "dc:publisher": {
            "@set": ["Khronos"]
          },
          "dc:title": {
            "@type": "rdf:Alt",
            "rdf:_1": {
              "@language": "en-us",
              "@value": "Sample glTF with XMP metadata"
            }
          }
        },
        {
          "@context": {
            "dc": "http://purl.org/dc/elements/1.1/",
            "rdf": "http://www.w3.org/1999/02/22-rdf-syntax-ns#"
          },
          "dc:title": {
            "@type": "rdf:Alt",
            "rdf:_1": {
              "@language": "en-us",
              "@value": "My Cube Mesh"
            }
          }
        }
      ]
    }
  },
  ...
}
```


## Legal

&copy; 2021, Adam Morris. [CC BY 4.0 International](https://creativecommons.org/licenses/by/4.0/legalcode)

 - Adam Morris for Everything

#### Assembled by modelmetadata