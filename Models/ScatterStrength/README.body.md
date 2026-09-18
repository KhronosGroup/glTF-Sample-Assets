## Screenshot

![Screenshot of the asset in Adobe Substance 3D Stager](screenshot/screenshot_raytracer.png)
<br/>_Pathtraced render from [Adobe Substance 3D Stager](https://www.adobe.com/products/substance3d/apps/stager.html) with the environment Harties Cliff View._
![Screenshot of the asset in Babylon.js using OpenPBR](screenshot/screenshot_rasterizer.png)
<br/>_Rendered in Babylon.js using OpenPBR material._

## Description
This asset demonstrates the effect of varying scatter strength and transmission strength on volumetric scattering. Each shader ball uses the `KHR_materials_scatter` extension with values for `scatterStrengthFactor` ranging from 0 to 1. One column uses `scatterStrengthTexture` to show fragment-varying strength.

## Editing and Export
The shader ball asset is the USD Standard Shader Ball, converted to glTF. The material setup and export was done in Babylon.js.