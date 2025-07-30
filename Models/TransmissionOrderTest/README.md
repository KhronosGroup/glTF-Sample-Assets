# Transmission Order Test

## Tags

[extension](../Models-extension.md), [testing](../Models-testing.md)

## Extensions Used

* KHR_materials_transmission
* KHR_materials_volume

## Summary

This model tests the interactions between blend modes and transmission.

## Operations

* [Display](https://github.khronos.org/glTF-Sample-Viewer-Release/?model=https://raw.GithubUserContent.com/KhronosGroup/glTF-Sample-Assets/main/./Models/TransmissionOrderTest/glTF-Binary/TransmissionOrderTest.glb) in SampleViewer
* [Download GLB](https://raw.GithubUserContent.com/KhronosGroup/glTF-Sample-Assets/main/./Models/TransmissionOrderTest/glTF-Binary/TransmissionOrderTest.glb)
* [Model Directory](./)

## Screenshot

![screenshot](screenshot/screenshot-large.jpg)

## Description

This model tests the interactions between blend modes and transmission.  Along the top row, the $\alpha$ symbol appears on an orange material using `"alphaMode" : "BLEND"`, and along the middle row, the same symbol appears using `"alphaMode" : "MASK"`.  The bottom row uses a more detailed mesh to replicate the symbol using `"alphaMode" : "OPAQUE"`. Each test area also features a small blue gem that uses the `KHR_materials_transmission` and `KHR_materials_volume` glTF extensions.  In the left column, the symbol appears behind the gem.  In the center column, the symbol is embedded, intersecting the gem.  In the right column, the symbol appears in front of the gem.

However, the above image was rendered in real-time by an engine that was forced to use some approximations to enable faster rendering.  In particular, the right-most column shows a side-effect of screen-space refraction, where objects in front of a transmissive volume can produce echos inside the volume.  Likewise, the real-time render did not distinguish the location of the rear surface of the blue gem relative to the symbol, and so the left and center columns show the same amount of blue shading present on the symbol.

Such side-effects of real-time approximations are not forbidden by any part of the glTF specification.  However, they are not physically correct, and so not desirable in any engine that can afford to perform a more careful render, such as a path tracer.

## Path Tracers

![path-traced screenshot](screenshot/path-traced.jpg)

When full path tracing is available, one can expect the $\alpha$ symbol to appear correctly relative to the blue gem.  Compare this to the real-time rendered screenshot at the top of this document, where the right-most column shows echos of the symbol being refracted in the gem behind it.

Note also that the gem is colored blue only at the surface, not with any volumetric attenuation. The path-traced version here shows the left column (with the symbol behind the gem) has been colored twice (for front and back surfaces of the gem), resulting in a darker symbol than the one that appears in the center column, where only the front surface of the gem is between the symbol and the viewer.

## Missing Alpha Blend

![missing alpha blend screenshot](screenshot/missing-blend.jpg)

In some real-time engines, the $\alpha$ symbol may appear to be missing from the blue gems across the top row (left and center columns, top row).  This may be the result of choices in the engine related to how the different render passes are ordered.

There's some informal conversation about this in the glTF Sample Viewer source code, under Git commit [`f4cd6b11`](https://github.com/KhronosGroup/glTF-Sample-Viewer/commit/f4cd6b11de9787db0cd35c06dfa46be7b5440aab).  The rendering strategy outlined there suggests that alpha-blended materials should be rendered after opaque materials, and included in the "opaque" render texture.  After the opaque and alpha-blend passes are done, the finished texture is used as a screen-space lookup texture for objects using transmission, such as the blue gems shown here.  The alpha-blended materials must be present in that texture in order for the symbols to be visible through the gems.  Of course, all of this is an imperfect approximation when compared to full path tracing as shown in the previous section above.

Note also that the middle row shows that alpha masked materials did not disappear in this example.  Alpha masking allows the object with the $\alpha$ symbol to be considered opaque, and so is included in the texture lookup for the gems.  This corrects the middle-left and center gems, but returns the familiar problem with the middle-right gem where echos of an object placed in front of the gem have appeared refracted within the gem.  There's no perfect solution here with this kind of rendering, and it's likely that full ray-tracing or path tracing will be needed to completely solve it.


## Legal

&copy; 2025, Public. [CC0 1.0 Universal](https://creativecommons.org/publicdomain/zero/1.0/legalcode)

 - Ed Mackey for Everything but the cloth backdrop

&copy; 2021, Public. [CC0 1.0 Universal](https://creativecommons.org/publicdomain/zero/1.0/legalcode)

 - Adobe for Cloth backdrop

#### Assembled by modelmetadata