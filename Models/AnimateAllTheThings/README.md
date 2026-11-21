# Animate All The Things

## Screenshots

## Purpose

This sample model demonstrates the `KHR_animation_pointer` extension which allows animating most properties of a glTF file, in contrast to "regular" animation which can only animate node transforms and morph target weights. These new capabilities bring glTF on par with other powerful animation systems such as USD, Blender, Unity.  

The model serves as a complex integration case to test and ensure which specific extensions and animation capabilities are available in a specific viewer. 

## Support

Known runtimes that support `KHR_animation_pointer` at the time of writing are:  

- [Gestaltor](https://gestaltor.io/)  
- [Babylon.js](https://sandbox.babylonjs.com/)  
- [three.js (Needle's fork)](https://three.needle.tools/examples/?q=loader_mu#webgl_loader_multiple)  
- [Needle Engine](https://needle.tools)  

Known exporters that support `KHR_animation_pointer` at the time of writing are:  

- [UnityGltf (prefrontal cortex' fork)](https://github.com/prefrontalcortex/unitygltf)  

## Description

List of animated properties found in this sample:
```
/nodes/{}/translation
/nodes/{}/rotation
/nodes/{}/scale
/nodes/{}/weights

/cameras/{}/perspective/yfov
/cameras/{}/perspective/znear
/cameras/{}/perspective/zfar
/cameras/{}/orthographic/ymag
/cameras/{}/orthographic/xmag

/materials/{}/emissiveFactor
/materials/{}/alphaCutoff
/materials/{}/normalTexture/scale
/materials/{}/occlusionTexture/strength
/materials/{}/pbrMetallicRoughness/baseColorFactor
/materials/{}/pbrMetallicRoughness/roughnessFactor
/materials/{}/pbrMetallicRoughness/metallicFactor
/materials/{}/pbrMetallicRoughness/baseColorTexture/extensions/KHR_texture_transform/scale
/materials/{}/pbrMetallicRoughness/baseColorTexture/extensions/KHR_texture_transform/offset

/materials/{}/extensions/KHR_materials_emissive_strength/emissiveStrength
/materials/{}/extensions/KHR_materials_iridescence/iridescenceFactor
/materials/{}/extensions/KHR_materials_iridescence/iridescenceIor
/materials/{}/extensions/KHR_materials_iridescence/iridescenceThicknessMaximum
/materials/{}/extensions/KHR_materials_volume/thicknessFactor
/materials/{}/extensions/KHR_materials_volume/attenuationDistance
/materials/{}/extensions/KHR_materials_volume/attenuationColor
/materials/{}/extensions/KHR_materials_ior/ior
/materials/{}/extensions/KHR_materials_transmission/transmissionFactor

/extensions/KHR_lights_punctual/lights/{}/intensity
/extensions/KHR_lights_punctual/lights/{}/color
/extensions/KHR_lights_punctual/lights/{}/range
/extensions/KHR_lights_punctual/lights/{}/spot/outerConeAngle
/extensions/KHR_lights_punctual/lights/{}/spot/innerConeAngle
```

## License Information

CC-BY 4.0 https://creativecommons.org/licenses/by/4.0/ Felix Herbst, prefrontal cortex and Needle