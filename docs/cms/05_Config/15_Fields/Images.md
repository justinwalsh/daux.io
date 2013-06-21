Creates a multi image upload field. Useful for a gallery or rotating image.

![images](http://space.todaymade.com/todaycms/images.jpeg)

    "images": {
        "title": "Images Field",
        "type": "images",
        "sizes": {
            "thumb": {
                "height": 150,
                "width": 150,
                "resize_strategy": "crop"
            },
            "large": {
                "height": 500,
                "width": 500,
                "resize_strategy": "crop"
            }
        }
    }

See [parameters table](https://github.com/justinwalsh/todaycms/wiki/Image).

See [resize strategies table](https://github.com/justinwalsh/todaycms/wiki/Resize-Strategies-Table).