## Purpose

The purpose of this model is to provide a stress test for a model containing a huge number of nodes, meshes, primitives and materials, as well as a large number of textures    
The model contains a flat nodehierarchy with 10000 nodes.  
Each node is referencing one unique mesh and each mesh having one primitive, referencing one unique material.   
This sums up to 10000 nodes, meshes, primitives and materials.  

In total there are 100 textures in the model (usage of the textures are randomly generated when the model is created)

This model is very demanding when it comes to loading and handling a lot of nodes, materials and textures.  
The goal is to provide a stress test to allow implementations optimize loading, preparation and rendering of such a usecase.  