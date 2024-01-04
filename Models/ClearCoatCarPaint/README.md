# Clear Coat Car Paint

## Tags

[extension](../../Models-extension.md), [testing](../../Models-testing.md)

## Extensions

### Required

* KHR_texture_transform
* KHR_materials_clearcoat

### Used

* KHR_texture_transform
* KHR_materials_clearcoat
* KHR_texture_transform
* KHR_materials_clearcoat

## Summary

This model is a sphere using the glTF ClearCoat extension overtop a car paint base material. 

## Operations

* [Display](https://github.khronos.org/glTF-Sample-Viewer-Release/?model=https://raw.GithubUserContent.com/KhronosGroup/glTF-Sample-Assets/main/./Models/ClearCoatCarPaint/glTF-Binary/ClearCoatCarPaint.glb) in SampleViewer
* [Download GLB](https://raw.GithubUserContent.com/KhronosGroup/glTF-Sample-Assets/main/./Models/ClearCoatCarPaint/glTF-Binary/ClearCoatCarPaint.glb)
* [Model Directory](./)

## Screenshot

![screenshot](screenshot/screenshot_large.jpg)

Screenshot from the [glTF Sample Viewer](https://github.khronos.org/glTF-Sample-Viewer-Release/) with the Wide Street environment light.

## Description

This model is a sphere using the glTF extension [`KHR_materials_clearcoat`](https://github.com/KhronosGroup/glTF/tree/master/extensions/2.0/Khronos/KHR_materials_clearcoat) overtop a car paint base material. 

The base material is meant to mimic the scattering from micro flakes such as used in some car paints. 

The model uses a single bitmap for the normal bump texture with a random per-pixel noise pattern, and the glTF extension [`KHR_texture_transform`](https://github.com/KhronosGroup/glTF/tree/main/extensions/2.0/Khronos/KHR_texture_transform) to increase bump tiling. 

![bump texture](screenshot/normal_bump_enlarged.jpg)

The normal bump texture, enlarged 200% to show detail.


Roughness was increased to 0.4 to scatter the reflected light and to provide a contrast with the clear coat layer which uses zero Roughness. Metalness was set arbitrarily to a partial value of 0.3 to increase the specularity of the base material.



## Legal

&copy; 2023, Public. [CC0 1.0 Universal](https://creativecommons.org/publicdomain/zero/1.0/legalcode)

 - Eric Chadwick for Everything

#### Assembled by modelmetadata