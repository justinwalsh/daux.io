**Daux.io** is an documentation generator that uses a simple folder structure and Markdown files to create custom documentation on the fly. It helps you create great looking documentation in a developer friendly way.

## Features

* 100% Mobile Responsive
* Supports GitHub Flavored Markdown
* Auto created homepage/landing page
* Auto Syntax Highlighting
* Auto Generated Navigation
* 4 Built-In Themes or roll your own
* Functional, Flat Design Style
* Shareable/Linkable SEO Friendly URLs
* Built On Bootstrap
* No Build Step
* Git/SVN Friendly
* Supports Google Analytics and Piwik Analytics
* Optional code float layout
* Static Output Generation

## Demos

This is a list of sites using Daux.io:

* [Daux.io](http://daux.io)
* [Gltn - An open-source word processor webapp](http://felkerdigitalmedia.com/gltn/docs/)
* [Invade & Annex 3 - An Arma 3 Co-operative Mission](http://ia3.ahoyworld.co.uk/)
* [Munee: Standalone PHP 5.3 Asset Optimisation & Manipulation](http://mun.ee)
* [ICADMIN: An admin panel powered by CodeIgniter.](http://istocode.com/shared/ic-admin/)
* [TrackJs](http://docs.trackjs.com) (uses a customized theme)
* [Sugoi](http://doc.sugoi.ventrux.com/)
* [wallabag](http://doc.wallabag.org/index)
* [iGeo-Topo](http://igeo-topo.fr/doc)

Do you use Daux.io? Send me a pull request or open an [issue](https://github.com/justinwalsh/daux.io/issues) and I will add you to the list.

## Download

Download this repository as a zip, and unpack. Copy the files to a web server that can run PHP 5.4 or greater. You can also run the documentation locally using Grunt.js, which is covered at the end of this readme.

## Folders

By default, the generator will look for folders in the `docs` folder. Add your folders inside the `docs` folder. This project contains some example folders and files to get you started.

You can nest folders any number of levels to get the exact structure you want. The folder structure will be converted to the nested navigation.

If you'd prefer to keep your docs somewhere else (like outside of the daux.io root directory) you can specify your docs path in the `global.json` file.

## Files

The generator will look for Markdown files (`*.md` and `*.markdown`) inside the `docs` folder and any of the subfolders within `docs`. Additional extensions can be added by editing `global.json`

You must use underscores instead of spaces. Here are some example file names and what they will be converted to:

**Good:**

* 01_Getting_Started.md = Getting Started
* API_Calls.md = API Calls
* 200_Something_Else-Cool.md = Something Else-Cool
* _5_Ways_to_Be_Happy.md = 5 Ways To Be Happy

**Bad:**

* File Name With Space.md = FAIL

## Sorting

To sort your files and folders in a specific way, you can prefix them with a number and underscore, e.g. `/docs/01_Hello_World.md` and `/docs/05_Features.md` This will list *Hello World* before *Features*, overriding the default alpha-numeric sorting. The numbers will be stripped out of the navigation and urls. For the file `6 Ways to Get Rich`, you can use `/docs/_6_Ways_to_Get_Rich.md`

## Landing page

If you want to create a beautiful landing page for your project, simply create a `index.md` file in the root of the `/docs` folder. This file will then be used to create a landing page. You can also add a tagline and image to this page using the config file like this:

```json
{
	"title": "Daux.io",
	"tagline": "The Easiest Way To Document Your Project",
	"image": "app.png"
}
```

Note: The image can be a local or remote image. Use the convention `<base_url>` to refer to the root directory of the Daux instance.

## Section landing page

If you are interested in having a landing page for a subsection of your docs, all you need to do is add an `index.md` file to the folder. For example, `/docs/01_Examples` has a landing page for that section since there exists a `/docs/01_Examples/index.md` file. If you wish to have an index page for a section without a landing page format, use the name `_index.md`

## Configuration

To customize the look and feel of your documentation, you can create a `config.json` file in the of the `/docs` folder. The `config.json` file is a simple JSON object that you can use to change some of the basic settings of the documentation.

###Title:
Change the title bar in the docs

```json
{
	"title": "Daux.io"
}
```

###Themes:
We have 4 built-in Bootstrap themes. To use one of the themes, just set the `theme` option to one of the following:

* daux-blue
* daux-green
* daux-navy
* daux-red

```json
{
	"theme": "daux-blue"
}
```

###Custom Theme:
To use a custom theme, just copy over the theme folder as well as the `.thm` file for that theme into the `themes` directory and set its value in the `theme` param in config.json

```json
{
	"theme": "new-theme",
}
```

###Code Floating:
By default your code blocks will be floated to a column on the right side of your content. To disable this feature, set the `float` property to `false`.

```json
{
	"float": false
}
```

###Toggling Code Blocks
Some users might wish to hide the code blocks & view just the documentation. By setting the `toggle_code` property to `true`, you can offer a toggle button on the page.

```json
{
	"toggle_code": true
}
```


###GitHub Repo:
Add a 'Fork me on GitHub' ribbon.

```json
{
	"repo": "justinwalsh/daux.io"
}
```

###Twitter:
Include twitter follow buttons in the sidebar.

```json
{
	"twitter": ["justin_walsh", "todaymade"]
}
```

###Links:
Include custom links in the sidebar.

```json
{
	"links": {
		"GitHub Repo": "https://github.com/justinwalsh/daux.io",
		"Help/Support/Bugs": "https://github.com/justinwalsh/daux.io/issues",
		"Made by Todaymade": "http://todaymade.com"
	}
}
```

###Google Analytics:
This will embed the google analytics tracking code.

```json
{
	"google_analytics": "UA-XXXXXXXXX-XX"
}
```

###Piwik Analytics:
This will embed the piwik tracking code.

```json
{
	"piwik_analytics": "my-url-for-piwik.com"
}
```

You can Also give a specific Piwik ID as well.

```json
{
	"piwik_analytics_id": "43"
}
```

###Ignore:
Set custom files and entire folders to ignore within your `/docs` folder. For files make sure to include the file extension in the name. For both files and folders, names are case-sensitive.

```json
	{
		"ignore": {
			"files": ["Work_In_Progress.md"],
			"folders": ["99_Not_Ready"]
		}
	}
```

###Breadcrumb titles
Daux.io provides the option to present page titles as breadcrumb navigation. You can *optionally* specify the separator used for breadcrumbs.

```json
{
		"breadcrumbs": true,
		"breadcrumb_separator" : " > "
}
```

###Date Modified
By default, daux.io will display the last modified time as reported by the system underneath the title for each document. To disable this, change the option in your config.json to false.

```json
{
	"date_modified": false
}
```

###Timezone
If your server does not have a default timezone set in php.ini, it may return errors when it tries to generate the last modified date/time for docs. To fix these errors, specify a timezone in your config file. Valid options are available in the [PHP Manual](http://php.net/manual/en/timezones.php).

```json
{
        "timezone": "America/Los_Angeles"
}
```

###Inherit Index
This feature will insruct the router to seek the first available file to use when a request to a folder is made and the index is not found.

```json
{
        "live": [
        	"inherit_index": true
        ]
}
```

###Multi-language
Enables multi-language support which needs seperate directories for each language in `docs/` folder.

```json
{
        "languages": { "en": "English", "de": "German" }
}
```

Directory structure:
```
├── docs/
│   ├── index.md
│   ├── en
│   │   ├── 00_Getting_Started.md
│   │   ├── 01_Examples
│   │   │   ├── 01_GitHub_Flavored_Markdown.md
│   │   │   ├── 05_Code_Highlighting.md
│   │   ├── 05_More_Examples
│   │   │   ├── Hello_World.md
│   │   │   ├── 05_Code_Highlighting.md
│   ├── de
│   │   ├── 00_Getting_Started.md
│   │   ├── 01_Examples
│   │   │   ├── 01_GitHub_Flavored_Markdown.md
│   │   │   ├── 05_Code_Highlighting.md
│   │   ├── 05_More_Examples
│   │   │   ├── Hello_World.md
│   │   │   ├── 05_Code_Highlighting.md
```

## Running Remotely

Copy the files from the repo to a web server that can run PHP 5.4 or greater.

## Running Locally

There are several ways to run the docs locally. You can use something like <a href="http://www.mamp.info/en/index.html" target="_blank">MAMP</a> or <a href="http://www.wampserver.com/en/" target="_blank">WAMP</a>. If you are like me and use alot of Node.js and <a href="http://gruntjs.com/" target="_blank">Grunt.js</a>, then you can use the optional grunt command I have packaged with the project which will start a PHP web server for you in the project folder.

The Grunt.js task uses the built in web server in PHP 5.4 to host the docs on your local machine. This is really only intended be used when you are writing/updating a ton of docs and want to preview the changes locally.

**To use the optional Grunt command you will need:**

* Node.js
* npm
* Grunt.js
* PHP 5.4 or greater (This is because of the built-in web server packaged in 5.4)

This project contains a package.json file, so once you have the requirements installed, you can simply run a `npm install` and then `grunt` in the projects folder to start the local web server. By default the server will run at: <a href="http://localhost:8085" target="_blank">http://localhost:8085</a>

## Generating a set of static files

These can be uploaded to a static site hosting service such as pages.github.com

Generating a complete set of pages, with navigation

```bash
php generate.php [global.json Relative Location] [Output Directory Relative Direction]
```

## Running on IIS

If you have set up a local or remote IIS web site, you may need a `web.config` with:

* A rewrite configuration, for handling clean urls.
* A mime type handler for less files, if using a custom theme.

### Clean URLs

The `web.config` needs an entry for `<rewrite>` under `<system.webServer>`:

```xml
<configuration>
	<system.webServer>
		<rewrite>
			<rules>
				<rule name="Main Rule" stopProcessing="true">
					<match url=".*" />
					<conditions logicalGrouping="MatchAll">
						<add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />
						<add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" />
					</conditions>
					<action type="Rewrite" url="index.php" appendQueryString="false" />
				</rule>
			</rules>
		</rewrite>
	</system.webServer>
</configuration>
```

To use clean URLs on IIS 6, you will need to use a custom URL rewrite module, such as [URL Rewriter](http://urlrewriter.net/).

### Less Mime Type

The `web.config` needs a new `<mimeMap>` entry, under `<staticContent>` in `<system.webServer>`:

```xml
<staticContent>
	<mimeMap fileExtension=".less" mimeType="text/css" />
</staticContent>
```

You will only need the mime map entry if you are using a custom theme and receive 404s for `.less` files.

If you have a global mime map entry for `.less` files set for the server, you will receive an internal server (500) error for having duplicate mime map entries.

## Support

If you need help using Daux.io, or have found a bug, please create an issue on the <a href="https://github.com/justinwalsh/daux.io/issues" target="_blank">GitHub repo</a>.
