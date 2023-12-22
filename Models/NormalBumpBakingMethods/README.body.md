## Screenshot

![Screenshot from glTF Sample Viewer](screenshot/screenshot_Large.jpg)
<br/>_Screenshot from [glTF Sample Viewer](https://github.khronos.org/glTF-Sample-Viewer-Release/)._


## Description

This asset is for testing the rendering of tangent-space normal bump maps which are "baked" to specific geometries. 

## Tangent Space

Tangent space normal bump maps are often used in two ways: 
1. To represent surface curvature from high-resolution geometry on lower-resolution real-time geometry.
2. For tiled high-frequency bump details which can be repeated across arbitrary meshes.

When the first method is used, the texture is created ("baked") for a specific geometry, and cannot be reused properly on meshes with different vertex normals. The pixel value vectors in the normal map are encoded to translate lighting from world space into the specific model's own tangent space. Each model's tangent space is uniquely calculated from the model's vertex normals and UV coordinates.

The glTF specification says if the asset does not include tangents, then renderers should use (MikkTSpace)[https://github.com/mmikk/MikkTSpace] for the normal maps. The meshes in this asset do not include tangents, so this must be calculated by the renderers.

## Vertex Normals

Three baking methods are demonstrated in this asset:
1. Hard edges
2. Soft edges
3. Bevels 

`Hard edges` means the vertex normals are split for each vertex of the cube. Each face of the cube has its own set of vertex normals perpendicular to the face. This creates hard edges between the six faces. 

When hard edges are used, the UV coordinates must be split along each hard edge. This allows the use of padding in the gutters between the hard edges, which prevents lighting discontinuities between the cube faces. Padding prevents lower MIPs of the normal texture from introducing erroreous vectors from neighboring faces.

`Soft edges` means the vertex normals are not split. Each vertex contains a single vertex normal, so neighboring cube faces are smoothly shaded with each other, with no hard edges between them. Tangent space calculations are very difficult in this case because there are no natural seams to help resolve directional differences. This kind of layout will tend to cause the most errors in renderers.

When soft edges are used, the UV coordinates do not need to be split along the edges. The normal map must encode complex gradients to represent the local space of the mesh. The increased gradients cause increased errors in texture compression, which will cause banding or noise when the surface is lit.

`Bevels` means the low-resolution model has additional mesh along the hard edges. When bevels are used, the extra vertices allow the use of face-weighted vertex normals. This allows the large flat surfaces to encode a flat tangent space. The normal map does not need to use large gradients to store differences in curvature between the low-resolution mesh and high-resolution mesh. 

Bevels increase the complexity of the low-resolution model and its UVs, but the in-game vertex count is comparable with low-resolution meshes that use hard edges instead of bevels. 

## Source Textures

The normal bump textures were rendered in 3ds Max 2024.2 using V-Ray 6.10.08 and the tool Bake To Texture.V-Ray defaults to the DirectX encoding of the green channel to represent Y-down, while glTF uses the OpenGL convention of Y-up, so the green channel in each texture was inverted. 

## Rendering Errors

This asset can be used to stress-test glTF renderers to discover errors in handling tangent-space normal maps.

![Screenshot from glTF Sample Viewer](screenshot/glTFSampleViewer.jpg)
<br/>_Screenshot from [glTF Sample Viewer](https://github.khronos.org/glTF-Sample-Viewer-Release/). All three low-resolution cubes show errors in rendering. The middle "BoxSoft" shows significant errors._

![Screenshot from Babylon.js Sandbox](screenshot/BabylonJSSandbox.jpg)
<br/>_Screenshot from [Babylon.js Sandbox](https://sandbox.babylonjs.com/). The three cubes render correctly; the lighting direction is consistent._

![Screenshot from three.js editor](screenshot/ThreeJSEditor.jpg)
<br/>_Screenshot from [three.js Editor](https://threejs.org/editor/). The middle cube "SoftBox" shows increased errors, but shows fundamentally correct lighting directions._

![Screenshot from three.js editor](screenshot/ModelViewerEditor.jpg)
<br/>_Screenshot from [model-viewer Editor](https://modelviewer.dev/editor/). The three cubes render correctly; the lighting direction is consistent._

## Test Meshes

Isolated glTF assets are provided in the `glTF-Binary` folder for baking and rendering normal maps in different software:

* `BoxBevel.glb` = the cube on the right, with bevels and face-weighted normals.
* `BoxHard.glb` = the cube on the left, with hard edges and split vertex normals.
* `BoxHigh.glb` = the cube at the top, with complex curvature to be baked into a normal map.
* `BoxSoft.glb` = the cube in the middle, with averaged vertex normals.

