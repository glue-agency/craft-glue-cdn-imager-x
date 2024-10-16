# Glue CDN for Imager X

Glue Agency image CDN for Imager X

## Requirements

This plugin requires Craft CMS 5.0.0 or later, and PHP 8.2.0 or later.

## Installation

You can install this plugin from the Plugin Store or with Composer.

#### With Composer

Open your terminal and run the following commands:

```bash
# go to the project directory
cd /path/to/my-project.test

# tell Composer to load the plugin
composer require glue-agency/craft-glue-cdn-imager-x

# tell Craft to install the plugin
php craft plugin/install glue-cdn-imager-x
```

## Configuration

Create the `glue-cdn-imager-x.php` file in `/config` and add the following.

```php
return [
    'baseUrl'       => rtrim(getenv('GLUE_CDN_DOMAIN'), '/'),
    'signKey'       => getenv('GLUE_CDN_SIGN_KEY'),
    'defaultParams' => [
        'dpr' => 2,
    ],
];
```

Update the Imager-X transformer to `glue-cdn`.

## Supported Features

### Transforms

| Parameter | Type     | Allowed values                  | Default |                                                                                                                                                                                                                                    | 
|-----------|----------|---------------------------------|---------|:-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| format    | string   | jpg, pjpg, png, gif, webp, avif | jpg     | Format of the created image.                                                                                                                                                                                                       |
| width     | int      |                                 |         | Width of the image, in pixels.                                                                                                                                                                                                     |
| height    | int      |                                 |         | Height of the image, in pixels.                                                                                                                                                                                                    |
| mode      | string   | crop                            | crop    | Resizes the image to fill the width and height boundaries and crops any excess image data. The resulting image will match the width and height constraints without distorting the image.                                           |
|           |          | max                             |         | Resizes the image to fit within the width and height boundaries without cropping, distorting or altering the aspect ratio, and will also not increase the size of the image if it is smaller than the output size.                 |
|           |          | fit                             |         | Resizes the image to fit within the width and height boundaries without cropping or distorting the image, and the remaining space is filled with the background color. The resulting image will match the constraining dimensions. |
|           |          | stretch                         |         | Stretches the image to fit the constraining dimensions exactly. The resulting image will fill the dimensions, and will not maintain the aspect ratio of the input image.                                                           | 
| dpr       | int      | 1 - 8                           | 1       | This makes it possible to display images at the correct pixel density on a variety of devices.                                                                                                                                     |
| flip      | string   | v, h, both                      |         | Flips the image vertically, horizontally or in both directions.                                                                                                                                                                    |
| bgColor   | string   |                                 |         |                                                                                                                                                                                                                                    |
| border    | [object] |                                 |         | Add a border to the image. The parameters include:                                                                                                                                                                                 |
|           | width    |                                 |         | Sets the border width in pixels or relative dimensions (e.g., 5w = 5%).                                                                                                                                                            |
|           | color    |                                 |         | Sets the border color. Supports hexadecimal RGB, RGBA formats, and the 140 color names supported by modern browsers.                                                                                                               |
|           | method   | overlay, shrink, expand         | expand  | Defines how the border will be displayed: overlay (default), shrink (shrinks image within border), or expand (expands canvas to fit border).                                                                                       |

### Effects

| Parameter  | Type  | Allowed values | Default |                                                                                                                                  |
|------------|-------|----------------|---------|----------------------------------------------------------------------------------------------------------------------------------|
| brightness | int   | -100 to +100   |         | Changes the brightness of the image. Use negative values to darken, and positive values to brighten.                             |
| contrast   | int   | -100 to +100   |         | Increases or decreases the contrast of the image. A value greater than 0 increases contrast while a negative value decreases it. |
| gamma      | float | 0.1 to 9.99    |         | Adjusts the image gamma.                                                                                                         |
| sharpen    | int   | 0 to 100       |         | Sharpens the image.                                                                                                              |
| blur       | int   | 0 to 100       |         | Blurs the image.                                                                                                                 |
| pixelate   | int   | 0 to 1000      |         | Pixelates the image.                                                                                                             |
| grayscale  | bool  | true, false    | false   | Converts the image to grayscale.                                                                                                 |
| sepia      | bool  | true, false    | false   | Converts the image to sepia.                                                                                                     |
