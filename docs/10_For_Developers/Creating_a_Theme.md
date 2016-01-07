In its simplest form, a theme is an empty folder with a `config.json` file containing `{}`

After that, every setting is optional, but you can override everything if you'd like to.

## `config.json` options

Here is an example `config.json` file :

```json
{
    "favicon": "<theme_url>img/favicon.png",
    "css": ["<theme_url>css/theme.min.css"],
    "js": [],
    "fonts": ["//fonts.googleapis.com/css?family=Roboto+Slab:400,100,300,700&subset=latin,cyrillic-ext,cyrillic"],
    "variants": {
        "blue": {
            "favicon": "<theme_url>img/favicon-blue.png",
            "css": ["<theme_url>css/theme-blue.min.css"]
        },
        "green": {
            "favicon": "<theme_url>img/favicon-green.png",
            "css": ["<theme_url>css/theme-green.min.css"]
        }
    }
}
```

There are five options :

- __`favicon`__: The URL to your favicon
- __`css`__: An array of CSS Stylesheets to add to the page
- __`js`__: An array of JavaScript files to load
- __`fonts`__: An array of Font sources, these are added as stylesheets
- __`variants`__: An object containing sub-themes. Each sub theme, can provide the same configurations as the main theme (`favicon`, `css`, `js`, `fonts`) 


You will also notice this `<theme_url>` in the url. 
This is automatically substituted with the final url to the theme when generating the final page.

There are two possible substitutions :
 - __`<theme_url>`__: The url to the current theme
 - __`<base_url>`__: The url to the documentation root
 
## Theme variants
 
Like the default Daux.io theme, you might want to provide variants of your theme.
 
In the example before, there were two variants : blue and green.

The configuration of a variant is added to the configuration of the main theme, it doesn't replace it.

For example the main CSS files defined are: `["<theme_url>css/theme.min.css"]` and the green variant defines `["<theme_url>css/theme-green.min.css"]`.

The final list of CSS files will be `["<theme_url>css/theme.min.css", "<theme_url>css/theme-green.min.css"]`.

This doesn't apply to `favicon`, only the last value set is kept.

## Setting the theme for your documentation

In your documentation's `config.json` (not the theme's `config.json`)

Change the `theme` option inside `html`

```json
{
	"html": {
		"theme": "{theme}-{variant}"
    }
}
```

The name of the theme, is the folder name.

A variant is optional, if you want to add one, separate it from the theme with a dash.

## Overriding templates

By default, you have a list of templates in `templates`

You can create a folder named `templates` in your theme, copy-paste the original template in that folder, and you can modify it freely.

You can even do it one template at a time if you wish to do only small changes.

By default, we have the following templates :
- `content.php`: The content page.
- `home.php`: The landing page.
- `error.php`: The page to show when a page is not found or some other error happened.
- `partials/navbar_content.php`: The content of the top navigation bar.
- `partials/google_analytics.php`: The script to load Google Analytics.
- `partials/piwik_analytics.php`: The script to load Piwik Analytics.
- `layout/00_layout.php`: The master template, containing the `<html>` tag.
- `layout/05_page.php`: The page layout, with left navigation.
