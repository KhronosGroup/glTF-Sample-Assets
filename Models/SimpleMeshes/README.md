# SimpleMeshes

## Tags

[core](../../Models-core.md), [testing](../../Models-testing.md), [written](../../Models-written.md)

## Summary

A simple scene with two nodes, both containing the same mesh, namely a mesh with a single mesh.primitive with a single indexed triangle with multiple attributes (positions, normals and texture coordinates), but without a material

## Operations

* [Display](https://github.khronos.org/glTF-Sample-Viewer-Release/?model=https://raw.GithubUserContent.com/KhronosGroup/glTF-Sample-Assets/main/./Models/SimpleMeshes/glTF/SimpleMeshes.gltf) in SampleViewer
* [Model Directory](./)

## Screenshot

![screenshot](screenshot/screenshot.png)

## Notes##

This is an example showing how the same mesh may be appended to 
several nodes, to be rendered multiple times. It also shows 
how additional vertex attributes are defined in a `mesh.primitive` - 
namely, attributes for the vertex normals. 

**Note:** The additional vertex normal attribute in this example is
not yet used by any technique. This may cause a warning to be 
printed during the validation. The normal attribute will be used in 
the [AdvancedMaterial](../AdvancedMaterial) example.

## Data layout

The following image shows the data layout of this sample:

![triangle](screenshot/triangle.png)


## Legal

&copy; 2017, Public. [CC0 1.0 Universal](https://creativecommons.org/publicdomain/zero/1.0/legalcode)

 - javagl for Everything

#### Assembled by modelmetadata