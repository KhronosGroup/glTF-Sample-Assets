## Screenshot

![screenshot](screenshot/screenshot-large.jpg)

## Description

This model tests various colors of point lights (via `KHR_lights_punctual`) that all share the same intensity (1.0).

It is intended to serve as a test for the following clarification that was added to the `KHR_lights_punctual` extension:

> The `intensity` represents the luminous intensity that the light would emit if it were colored pure white (`[1.0, 1.0, 1.0]`). The `color` property acts as a wavelength-specific multiplier.

In other words, all the lights share the same amount of raw energy, but do not share their perceptual brightness.  The blue lamp in particular should appear visually not as bright as other colors.

The test material in each case is (0.8, 0.8, 0.8).  The lights are as follows:

| Label | Color |
|-------|-------|
| Red                | (1.0, 0.0, 0.0) |
| Green              | (0.0, 1.0, 0.0) |
| Blue               | (0.0, 0.0, 1.0) |
| Red + Green + Blue | Three colocated lights, matching the above |
| White              | (1.0, 1.0, 1.0) |
| Gray               | (0.5, 0.5, 0.5) |
