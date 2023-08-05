# Texture Coordinate Test

## Tags

[core](../../Models-core.md), [testing](../../Models-testing.md)

## Summary

Shows how XYZ and UV positions relate to displayed geometry.

## Operations

* [Display](https://github.khronos.org/glTF-Sample-Viewer-Release/?model=https://raw.GithubUserContent.com/KhronosGroup/glTF-Sample-Assets/main/./Models/TextureCoordinateTest/glTF-Binary/TextureCoordinateTest.glb) in SampleViewer
* [Download GLB](https://raw.GithubUserContent.com/KhronosGroup/glTF-Sample-Assets/main/./Models/TextureCoordinateTest/glTF-Binary/TextureCoordinateTest.glb)
* [Model Directory](./)

## Screenshot

![screenshot](screenshot/screenshot.png)

## Description

This model demonstrates the orientation of texture coordinates.  The materials and accessors have all been named according to function.  In particular, the accessor named `TopLeft_TEXCOORD_0` shows that the upper-left portion of the texture image is represented by texture coordinates ranging from roughly `0.0` to roughly `0.4`.

```
{
    "name": "TopLeft_TEXCOORD_0",
    "bufferView" : 7,
    "componentType" : 5126,
    "count" : 4,
    "max" : [
        0.3999999463558197,
        0.3999999761581421
    ],
    "min" : [
        7.915305388905836e-08,
        0.0
    ],
    "type" : "VEC2"
},
```



## Legal

&copy; 2017, Analytical Graphics, Inc.. [CC0 1.0 Universal](https://creativecommons.org/publicdomain/zero/1.0/legalcode)

 - Ed Mackey for Everything

#### Assembled by modelmetadata