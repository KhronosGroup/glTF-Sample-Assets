# SheenTestGrid

## Tags

[showcase](../../Models-showcase.md), [extension](../../Models-extension.md)

## Extensions

### Required

* KHR_materials_sheen

### Used

* KHR_materials_sheen
* KHR_materials_sheen

## Summary

Grid of spheres over a checkered backdrop to test Sheen rendering.

## Operations

* [Display](https://github.khronos.org/glTF-Sample-Viewer-Release/?model=https://raw.GithubUserContent.com/KhronosGroup/glTF-Sample-Assets/main/./Models/SheenTestGrid/glTF-Binary/SheenTestGrid.glb) in SampleViewer
* [Download GLB](https://raw.GithubUserContent.com/KhronosGroup/glTF-Sample-Assets/main/./Models/SheenTestGrid/glTF-Binary/SheenTestGrid.glb)
* [Model Directory](./)

## Screenshot

![Screenshot from glTF Sample Viewer](screenshot/screenshot_Large.jpg)
<br/>_Screenshot from [glTF Sample Viewer](https://github.khronos.org/glTF-Sample-Viewer-Release/) using the Environment "Footprint Court"._

## Description

This model tests sheenColorFactor versus sheenRoughnessFactor using the extension [KHR_materials_sheen](https://github.com/KhronosGroup/glTF/tree/main/extensions/2.0/Khronos/KHR_materials_sheen). 

The baseColorFactor for all spheres is 0.5 blue, and the sheenColorFactor varies from 0 black to 0,1,1 cyan. 

The sheen extension has been explicitly added to each of the sixteen materials, even when the sheenColorFactor is set to black and therefore the extension would usually be omitted.

![Screenshot from glTF Sample Viewer](screenshot/sheen-sheenColor-sheenRough.jpg)
<br/>_Screenshots from [glTF Sample Viewer](https://github.khronos.org/glTF-Sample-Viewer-Release/) using the Environment "Studio Neutral" and showing debug views Sheen, Sheen Color, and Sheen Roughness._

![Screenshot from glTF Sample Viewer](screenshot/screenshot_Punctual.jpg)
<br/>_Screenshot from [glTF Sample Viewer](https://github.khronos.org/glTF-Sample-Viewer-Release/) with the Punctual Lighting option, which uses two directional lights from opposing angles._

## Legal

&copy; 2023, Public. [CC0 1.0 Universal](https://creativecommons.org/publicdomain/zero/1.0/legalcode)

 - Eric Chadwick for Everything

#### Assembled by modelmetadata