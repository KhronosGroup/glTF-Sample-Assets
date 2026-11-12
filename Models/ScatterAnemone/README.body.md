## Screenshot

![Screenshot from glTF Sample Viewer](screenshot/screenshot_Large.jpg)
<br/>_Screenshot from [glTF Sample Viewer](https://github.khronos.org/glTF-Sample-Viewer-Release/)._

## Description

This asset demonstrates the use of [KHR_materials_scatter](https://github.com/KhronosGroup/glTF/tree/main/extensions) to represent an anemone with volumetric light transmission.

An animated fish swims in a loop through the arms of the anemone, with an emissive texture and a point light.

The point light was added slightly in front of the fish mesh, instead of inside, because rendering with a pathtracer would cause the fish body to exclude the light.

An animated chase camera was added behind the fish. Another camera was added at the base of the anemone, looking upwards.
