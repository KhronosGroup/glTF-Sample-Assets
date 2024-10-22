## Screenshot

![screenshot](screenshot/screenshot-large.gif)

## Description

This model tests the [`KHR_animation_pointer`](https://github.com/KhronosGroup/glTF/tree/main/extensions/2.0/Khronos/KHR_animation_pointer) extension's effect on the `baseColorFactor` of a material.  When animated, the top cube should rotate above other colored cubes, and change its own color to match the cubes below.

This model was created in Blender 4.2.0 by placing keyframes for a material's default base color and using the "Push Down Action" button in the NLA Track Editor.  The glTF export settings were configured with "Animation Mode" set to "NLA Tracks" and with a checkmark placed on "Animation Pointer (experimental)".

## Possible Problems

- If the top cube moves but stays red the entire time, it may indicate that `KHR_animation_pointer` is not supported in your renderer.

- If the color changes become out-of-sync with the physical motion of the cube, it may indicate that the various animation tracks are not being driven by the same clock.  The movement is defined for the `[0, 3]` range while colors are defined for the `[0, 2.5]` range. This checks that the last color keyframe remains active until the movement track is complete. A similar test exists in the [BoxAnimated](../BoxAnimated) sample without any animation pointers.
