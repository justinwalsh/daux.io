Creates a single image upload field.

![image](http://space.todaymade.com/todaycms/image-field.jpg)

    "image": {
        "title": "Image Field",
        "type": "image",
        "sizes": {
            "thumb": {
                "height": 150,
                "width": 150,
                "resize_strategy": "crop"
            },
            "large": {
                "height": 500,
                "width": 500,
                "resize_strategy": "fit"
            }
        }
    }

Image fields allows you to determine multiple thumbnail/image sizes and cropping techniques.

**Parameters**

| Name | Type | Default | Description |
| ------------- | ------------- | ------------- | ------------- |
| width | 1-5000 | Width of the input image | Width of the new image, in pixels |
| height | 1-5000 | Height of the input image | Height of the new image, in pixels |
| strip | boolean | false | Strips all metadata from the image. This is useful to keep thumbnails as small as possible. |
| flatten | boolean | true | Flattens all layers onto the specified background to achive better results from transparent formats to non-transparent formats, as explained in the [ImageMagick](http://www.imagemagick.org/script/command-line-options.php?#layers) documentation. **Important:** To preserve animations, GIF files are not flattened when this is set to true. To flatten GIF animations, use the frame parameter. |
| correct_gamma | boolean | false | Prevents gamma errors [common in many image scaling algorithms](http://www.4p8.com/eric.brasseur/gamma.html). |
| quality | 1-100 | Quality of the input image, or 92 | Controls the image compression for JPG and PNG images. |
| background | string | "#FFFFFF" | Either the hexadecimal code or [name](http://www.imagemagick.org/script/color.php#color_names) of the color used to fill the background (only used for the pad resize strategy). **Important:** By default, the background of transparent images is changed to white. For details about how to preserve transparency across all image types, see [this demo](https://transloadit.com/demos/image-resize/properly-preserve-transparency-across-all-image-types). |
| resize_strategy | string | "fit" | See [[Resize Strategies Table]] |
| zoom | boolean | true | If this is set to false, smaller images will not be stretched to the desired width and height. For details about the impact of zooming for your preferred resize strategy, see the [[Resize Strategies Table]]. |
| crop | { x1: integer, y1: integer, x2: integer, y2: integer } | {} | Specify an object containing coordinates for the top left and bottom right corners of the rectangle to be cropped from the original image(s). For example, `{x1: 80, y1: 100, x2: 160, y2: 180}` will crop the area from `(80,100)` to `(160,180)` which is a square whose width and height are 80px. If crop is set, the width and height parameters are ignored, and the resize_strategy is set to crop automatically. |
| format | string | Format of the input image | The available formats are `"jpg"`, `"png"`, `"gif"`, and `"tiff"`. |
| gravity | string | center | The direction from which the image is to be cropped. The available options are `"center"`, `"top"`, `"bottom"`, `"left"`, and `"right"`. You can also combine options with a hyphen, such as `"bottom-right"`. |
| frame | integer | null (all frames) | Use this parameter when dealing with animated GIF files to specify which frame of the GIF is used for the operation. Specify 1 to use the first frame, 2 to use the second, and so on. |
| colorspace | string | " " | Sets the image colorspace. For details about the available values, see the [ImageMagick documentation](http://www.imagemagick.org/script/command-line-options.php#colorspace). |
| rotation | string / boolean / integer | true | Determines whether the image should be rotated. Set this to true to auto-rotate images that are misrotated, or depend on EXIF rotation settings. You can also set this to an integer to specify the rotation in degrees. You can also specify degrees> to rotate only when the image width exceeds the height (or degrees< if the width must be less than the height). Specify false to disable auto-fixing of misrotated images. |
| compress | string | null | Specifies pixel compression for when the image is written. Valid values are None, `"BZip"`, `"Fax"`, `"Group4"`, `"JPEG"`, `"JPEG2000"`, `"Lossless"`, `"LZW"`, `"RLE"`, and `"Zip"`. Compression is disabled by default. |
| blur | string | null | Specifies gaussian blur, using a value with the form `{radius}x{sigma}`. The radius value specifies the size of area the operator should look at when spreading pixels, and should typically be either `"0"` or at least two times the sigma value. The sigma value is an approximation of how many pixels the image is "spread"; think of it as the size of the brush used to blur the image. This number is a floating point value, enabling small values like `"0.5"` to be used. For details about how the radius and sigma values affect blurring, see [this example](http://www.imagemagick.org/Usage/blur/blur_montage.jpg). |

**Watermark Parameters**

| Name | Type | Default | Description |
| ------------- | ------------- | ------------- | ------------- |
| watermark_url | string | " " | A url indicating a PNG image to be overlaid above this image. |
| watermark_position | string/array | "center" | The position at which the watermark is placed. The available options are `"center"`, `"top"`, `"bottom"`, `"left"`, and `"right"`. You can also combine options, such as `"bottom-right"`. An array of possible values can also be specified, in which case one value will be selected at random, such as `["center","left","bottom-left","bottom-right"]`. _Note that this setting puts the watermark in the specified corner. To use a specific pixel offset for the watermark, you will need to add the padding to the image itself._ |
| watermark_size | string | "" | The size of the watermark, as a percentage. For example, a value of `"50%"` means that size of the watermark will be 50% of the size of image on which it is placed. |
| watermark_resize_strategy | string | "fit" | Available values are `"fit"` and `"stretch"`. |
