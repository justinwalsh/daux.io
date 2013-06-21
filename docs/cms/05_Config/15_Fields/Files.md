Creates multiple file upload fields with sort and caption options.

![todaycms files](http://space.todaymade.com/todaycms/files.jpg)

    "files": {
        "title": "Files Field",
        "type": "files"
    }

Returns a multidimensional array.

    [files] => Array
        (
            [0] => Array
                (
                    [caption] => Pre-Order Form
                    [date] => 2/13/2012
                    [url] => http://todaycms.s3.amazonaws.com/...filename.pdf
                    [name] => Author Visit - Beth McKinney.pdf
                )
        )