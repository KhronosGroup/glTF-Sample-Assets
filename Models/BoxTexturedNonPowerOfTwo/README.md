# Box Textured not 2^N

## Tags

[core](../../Models-core.md), [issues](../../Models-issues.md), [testing](../../Models-testing.md)

## Summary

Box with a non-power-of-2 (NPOT) texture. Not all implementations support NPOT textures. [Issues: non-Khronos mark]

## Operations

* [Display](https://github.khronos.org/glTF-Sample-Viewer-Release/?model=https://raw.GithubUserContent.com/KhronosGroup/glTF-Sample-Assets/main/./Models/BoxTexturedNonPowerOfTwo/glTF-Binary/BoxTexturedNonPowerOfTwo.glb) in SampleViewer
* [Download GLB](https://raw.GithubUserContent.com/KhronosGroup/glTF-Sample-Assets/main/./Models/BoxTexturedNonPowerOfTwo/glTF-Binary/BoxTexturedNonPowerOfTwo.glb)
* [Model Directory](./)

## Screenshot

![screenshot](screenshot/screenshot.png)

## Description

This model uses a Non Power-Of-Two texture with REPEAT mode wrapping.  This is an edge case that is technically a valid gltf model, but needs some renderer work to resize the texture before uploading to the GPU.  According to the spec:

glTF does not guarantee that a texture's dimensions are a power-of-two. At runtime, if a texture's width or height is not a power-of-two, it may have problems with certain wrapping or filtering modes.

See the Non-Power-Of-Two note at the bottom of the [Samplers section](https://github.com/KhronosGroup/glTF/tree/master/specification/2.0#samplers) of the spec.


## Legal

&copy; 2017, Cesium. [CC-BY 4.0 International with Trademark Limitations]()

 - Cesium for Everything

&copy; 2015, Cesium. [Cesium Trademark or Logo]()

 - Non-copyrightable logo for Cesium logo

#### Assembled by modelmetadata