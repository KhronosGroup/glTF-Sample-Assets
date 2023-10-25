# Creating the HexaCube in Blender

![screenshot](Screenshots/screenshot.png)

## Steps

1. Creating the hexa-cube:
    1. In Modeling mode : Add -> Mesh -> Cube object
    2. Select "Vertex" mode : Select -> All
    3. Press delete key on keyboard : Delete "Only Faces" to keep skeleton of cube
    4. Select 4 vertices of a side to create new shapes
    5. Right click extrude edges (hold ctrl to snap) -> Click to confirm length
    6. Right click and select "New Edge/Face from Vertices"
    7. Repeat 4-6 for all faces
    8. For missing faces, Select 4 vertices of missing face -> Right click "New Edge/Face from Vertices"

2. Triangulate
    blender doesn’t have automatic triangulation, so must do it manually :
    Select 2 diagnoal vertices of a face and right click connect vertex pairs

3. Normalize
    1. Select "Face" mode : Select -> All
    2. Press shift N to recalculate normals
    _before Normalization :_ 
    ![before](Screenshots/before.png "before normalization")
    _after Normalization :_
    ![after](Screenshots/after.png "after normalization")

4. Bevel
    1. Select "Edge" mode : Select -> All
    2. Right click, select bevel edges
    3. Click to confirm bevel size

5. Export
    1. Choose file -> export -> glTF 2.0 (.glb/.gltf)
    2. Right side : Format, Choose appropriate filetype
    3. In Geometry select "Normals"
    4. Set Name and select Export glTF 2.0

#### Assembled by Hyerin Seok
