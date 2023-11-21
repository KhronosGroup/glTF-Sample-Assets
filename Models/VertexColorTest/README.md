# Vertex Color Test

## Tags

[core](../../Models-core.md), [testing](../../Models-testing.md)

## Summary

Tests if vertex colors are supported.

## Operations

* [Display](https://github.khronos.org/glTF-Sample-Viewer-Release/?model=https://raw.GithubUserContent.com/KhronosGroup/glTF-Sample-Assets/main/./Models/VertexColorTest/glTF-Binary/VertexColorTest.glb) in SampleViewer
* [Download GLB](https://raw.GithubUserContent.com/KhronosGroup/glTF-Sample-Assets/main/./Models/VertexColorTest/glTF-Binary/VertexColorTest.glb)
* [Model Directory](./)

## Screenshot

![screenshot](screenshot/screenshot.png)

## Description

This model tests the attribute semantic `COLOR_0`, as defined in the glTF [Metallic Roughness Material](https://github.com/KhronosGroup/glTF/tree/master/specification/2.0#metallic-roughness-material), to check if it has been multipled by `baseColor`.  For engines that read the vertex colors and apply them, you should see two rows of checkmarks, as shown in the screenshot.  The top row is the "Test" row, which has been multipled by red, green, and blue vertex colors to reveal checkmarks in the corresponding color channels.  The bottom row is a "sample pass" row, where checkmarks of each color are multiplied by white and should appear the same as the screenshot regardless of the rendering engine's ability to process vertex colors.

For engines that ignore vertex colors, the top row of checks will look noticably mangled.  The red check has a cyan X, the green check has a magenta X, and the blue check has a yellow X, occupying the other two color channels of each test checkmark.  If you see these "X" marks fighting with the checkmarks, then you are seeing color channels that are supposed to have been zeroed out by the applied vertex colors on the mesh, and it means your rendering engine has not applied the vertex colors.



## Legal

&copy; 2018, Analytical Graphics, Inc.. [CC BY 4.0 International](https://creativecommons.org/licenses/by/4.0/legalcode)

 - Ed Mackey for Everything

#### Assembled by modelmetadata