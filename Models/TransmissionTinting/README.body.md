## Screenshot

![Screenshot from Adobe Substance 3D Stager](screenshot/raytraced.png)
<br/>_Screenshot from [Adobe Substance 3D Stager](https://www.adobe.com/products/substance3d/apps/stager.html)._

Rasterizer that only supports a single-layer of transparency. Notice that the volumetric materials include the double-tinting for both sides of the volume as an approximation while the thin-walled materials do not.
![Screenshot from Babylon.js](screenshot/single_layered_rasterizer.png)
<br/>_Screenshot from [Babylon.js](https://www.babylonjs.com/)._

Rasterizer that supports multiple layers of transparency.
![Screenshot from Adobe Substance 3D Stager](screenshot/multiple_layered_rasterizer.png)
<br/>_Screenshot from [Adobe Substance 3D Stager](https://www.adobe.com/products/substance3d/apps/stager.html)._

## Description

This asset tests the surface-tinting functionality of the [KHR_materials_transmission](https://github.com/KhronosGroup/glTF/tree/main/extensions/2.0/Khronos/KHR_materials_transmission) and [KHR_materials_diffuse_transmission](https://github.com/KhronosGroup/glTF/tree/main/extensions/2.0/Khronos/KHR_materials_diffuse_transmission) extensions used in conjunction with thin-walled meshes as well as volumetric (with the [KHR_materials_volume](https://github.com/KhronosGroup/glTF/tree/main/extensions/2.0/Khronos/KHR_materials_volume) extension). While the cubes are tinted on both sides, the plane represents a single layer of geometry and so should appear lighter than the cubes.
