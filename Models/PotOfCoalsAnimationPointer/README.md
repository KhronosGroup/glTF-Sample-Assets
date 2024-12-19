# PotOfCoalsAnimationPointer

## Tags

[showcase](../../Models-showcase.md), [extension](../../Models-extension.md)

## Extensions

### Required

* KHR_materials_specular
* KHR_materials_transmission
* KHR_materials_volume
* KHR_texture_transform

### Used

* KHR_materials_specular
* KHR_materials_transmission
* KHR_materials_volume
* KHR_texture_transform
* KHR_animation_pointer
* KHR_materials_clearcoat
* KHR_materials_specular
* KHR_materials_transmission
* KHR_materials_volume
* KHR_texture_transform

## Summary

Burnt copper pot with a clearcoat, containing emissive hot coals, and animated heat refraction.

## Operations

* [Display](https://github.khronos.org/glTF-Sample-Viewer-Release/?model=https://raw.GithubUserContent.com/KhronosGroup/glTF-Sample-Assets/main/./Models/PotOfCoalsAnimationPointer/glTF-Binary/PotOfCoalsAnimationPointer.glb) in SampleViewer
* [Download GLB](https://raw.GithubUserContent.com/KhronosGroup/glTF-Sample-Assets/main/./Models/PotOfCoalsAnimationPointer/glTF-Binary/PotOfCoalsAnimationPointer.glb)
* [Model Directory](./)

## Screenshot

![Screenshot from glTF Sample Viewer](screenshot/screenshot_Large.jpg)
<br/>_Screenshot from [Babylon.js Sandbox](https://sandbox.babylonjs.com/)._

## Description

This asset uses the extension [KHR_animation_pointer](https://github.com/KhronosGroup/glTF/tree/main/extensions/2.0/Khronos/KHR_animation_pointer#khr_animation_pointer) to animate a heat refraction effect over the [PotOfCoals asset](../PotOfCoals/README.md).

![Animated GIF of the heat refraction](screenshot/PotOfCoalsAnimationPointer.gif)
<br/>_The animated heat refraction effect._

## Animation

![Thickness and normal textures](screenshot/thickness_and_normal.jpg)
<br/>_The refraction effect uses two counter-rotating textures._

A mesh was placed above the coals and assigned a refractive material with [KHR_materials_transmission](https://github.com/KhronosGroup/glTF/blob/main/extensions/2.0/Khronos/KHR_materials_transmission/README.md#khr_materials_transmission-) and [KHR_materials_volume](https://github.com/KhronosGroup/glTF/tree/main/extensions/2.0/Khronos/KHR_materials_volume#khr_materials_volume). The volume thickness texture was animated to rotate counter-clockwise, and the normal texture was animated to rotate in the opposite direction. This causes the refraction animation to be less noticeable as a rotating effect, as the two distortions produce an interference pattern. Each texture uses a radial gradient to gradually flatten the texture towards the outside edges, which helps separate the refraction from the edges of the copper pot.

## Specular

![Disabling the Specular extension](screenshot/specular_disabled.jpg)
<br/>_The specular extension was used to remove reflections._

The extension [KHR_materials_specular](https://github.com/KhronosGroup/glTF/tree/main/extensions/2.0/Khronos/KHR_materials_specular#khr_materials_specular) was added to the refraction mesh to disable all reflections. This prevents the mesh from looking like reflective glass or water, but more like non-reflective air being refracted by heat.

## Legal

&copy; 2024, Darmstadt Graphics Group GmbH. [CC BY 4.0 International](https://creativecommons.org/licenses/by/4.0/legalcode)

 - Eric Chadwick for Model and textures

&copy; 2015, Khronos Group. [Khronos Trademark or Logo]()

 - Non-copyrightable logo for Khronos logo

&copy; 2017, Khronos Group. [Khronos Trademark or Logo]()

 - Non-copyrightable logo for glTF logo

#### Assembled by modelmetadata