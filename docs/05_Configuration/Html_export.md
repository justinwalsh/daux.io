## Themes
We have 4 built-in Bootstrap themes. To use one of the themes, just set the `theme` option to one of the following:

* daux-blue
* daux-green
* daux-navy
* daux-red

```json
{
    "html": { "theme": "daux-blue" }
}
```

## Custom Theme
To use a custom theme, just copy over the theme folder into the `themes` directory and set its value in the `theme` param in config.json

```json
{
	"html": { "theme": "new-theme" }
}
```

## Code Floating
By default your code blocks will be floated to a column on the right side of your content. To disable this feature, set the `float` property to `false`.

```json
{
	"html": { "float": false }
}
```

## Toggling Code Blocks
Some users might wish to hide the code blocks & view just the documentation. By setting the `toggle_code` property to `true`, you can offer a toggle button on the page.

```json
{
	"html": { "toggle_code": true }
}
```


## GitHub Repo
Add a 'Fork me on GitHub' ribbon.

```json
{
	"html": { "repo": "justinwalsh/daux.io" }
}
```

## Twitter
Include twitter follow buttons in the sidebar.

```json
{
	"html": { "twitter": ["justin_walsh", "todaymade"] }
}
```

## Links
Include custom links in the sidebar.

```json
{
	"html": {
	    "links": {
		    "GitHub Repo": "https://github.com/justinwalsh/daux.io",
		    "Help/Support/Bugs": "https://github.com/justinwalsh/daux.io/issues",
		    "Made by Todaymade": "http://todaymade.com"
	    }
	}
}
```

## Google Analytics
This will embed the google analytics tracking code.

```json
{
	"html": { "google_analytics": "UA-XXXXXXXXX-XX" }
}
```

## Piwik Analytics
This will embed the piwik tracking code.

```json
{
	"html": { "piwik_analytics": "my-url-for-piwik.com" }
}
```

You can Also give a specific Piwik ID as well.

```json
{
	"html": { "piwik_analytics_id": "43" }
}
```

## Breadcrumb titles
Daux.io provides the option to present page titles as breadcrumb navigation. You can *optionally* specify the separator used for breadcrumbs.

```json
{
    "html": {
		"breadcrumbs": true,
		"breadcrumb_separator" : " > "
    }
}
```

## Date Modified
By default, daux.io will display the last modified time as reported by the system underneath the title for each document. To disable this, change the option in your config.json to false.

```json
{
	"html": { "date_modified": false }
}
```
