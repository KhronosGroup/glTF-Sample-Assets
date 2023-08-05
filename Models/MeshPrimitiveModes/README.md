# Mesh Primitive Modes

## Tags

[testing](../../Models-testing.md)

## Summary

An example that shows rendering modes that are supported for mesh primitives in glTF.

## Operations

* [Display](https://github.khronos.org/glTF-Sample-Viewer-Release/?model=https://raw.GithubUserContent.com/KhronosGroup/glTF-Sample-Assets/main/./Models/MeshPrimitiveModes/glTF/MeshPrimitiveModes.gltf) in SampleViewer
* [Model Directory](./)

## Screenshot

![screenshot](screenshot/screenshot.png)

## Description

Seven mesh primitive types: Points, Lines, Line Loops, Line Strips, Triangles, Triangle Strips, Triangle Fans.

## Structure

The example contains seven meshes, each with one mesh primitive. All mesh primitives have the same vertex positions, forming a regular hexagon:
```
       3
   4       2
       0   
   5       1
       6
```

Each mesh primitive has a different `mode`, corresponding to the glTF [mesh topology types](https://registry.khronos.org/glTF/specs/2.0/glTF-2.0.html#meshes-overview). The `indices` of each mesh primitive for the respective modes are as follows:

- indices for `mode=0` (`POINTS`): `[0, 1, 2, 3, 4, 5, 6]`
- indices for `mode=1` (`LINES`): `[0, 1, 0, 2, 0, 3, 0, 4, 0, 5, 0, 6]`
- indices for `mode=2` (`LINE_LOOP`): `[0, 1, 2, 3, 4, 5, 6]`
- indices for `mode=3` (`LINE_STRIP`): `[0, 1, 2, 3, 4, 5, 6]`
- indices for `mode=4` (`TRIANGLES`): `[0, 1, 2, 0, 2, 3, 0, 3, 4, 0, 4, 5, 0, 5, 6, 0, 6, 1]`
- indices for `mode=5` (`TRIANGLE_STRIP`): `[2, 3, 1, 4, 6, 5]` (vertex `0` is unused here)
- indices for `mode=6` (`TRIANGLE_FAN`): `[0, 1, 2, 3, 4, 5, 6, 1]`


## Legal

&copy; 2023, Public. [CC0 1.0 Universal](https://creativecommons.org/publicdomain/zero/1.0/legalcode)

 - Marco Hutter for Everything

#### Assembled by modelmetadata