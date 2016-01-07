If you want to create a beautiful landing page for your project, create a `_index.md` file in the root of the `/docs` folder. This file will then be used to create a landing page. You can also add a tagline and image to this page using the config file like this:

```json
{
	"title": "Daux.io",
	"tagline": "The Easiest Way To Document Your Project",
	"image": "app.png"
}
```

> The image can be a local or remote image. By default, the path is relative to the root of the documentation.

To disable the automatic landing page, you can set `auto_landing` to false in the `html` section of your configuration

```json
{
	"html": {
		"auto_landing": false
    }
}
```
