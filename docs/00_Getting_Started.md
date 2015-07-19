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
* Internal documentation links
* Multiple Output Formats
* Extend Daux.io with Processors

## Demos

This is a list of sites using Daux.io:

* [Daux.io](http://daux.io)
* [Gltn - An open-source word processor webapp](http://felkerdigitalmedia.com/gltn/docs/)
* [Invade & Annex 3 - An Arma 3 Co-operative Mission](http://ia3.ahoyworld.co.uk/)
* [Munee: Standalone PHP 5.3 Asset Optimisation & Manipulation](http://mun.ee)
* [ICADMIN: An admin panel powered by CodeIgniter.](http://istocode.com/shared/ic-admin/)
* [TrackJs](http://docs.trackjs.com) (uses a customized theme)
* [wallabag](http://doc.wallabag.org/index)

Do you use Daux.io? Send me a pull request or open an [issue](https://github.com/justinwalsh/daux.io/issues) and I will add you to the list.

## Download

Download this repository as a zip, and unpack. Copy the files to a web server that can run PHP 5.3 or greater. You can also run the documentation locally using Grunt.js, which is covered at the end of this readme.

## Generating a set of static files

These can be uploaded to a static site hosting service such as pages.github.com

Generating a complete set of pages, with navigation

```bash
./generate --destination=[Output Directory Relative Direction]
```

For more options, run 

```bash
./generate --help
```

## Formats

Daux.io is extendable and comes by default with two export formats:

- Export to HTML
- Upload to your Atlassian Confluence server

## Feature Matrix

<table>
  <tr>
    <th>Feature</th><th>HTML</th><th>Confluence</th>
  </tr>
  <tr>
    <td>Landing Pages</td><td>√</td><td>X</td>
  </tr>
  <tr>
    <td>Index Pages</td><td>√</td><td>√</td>
  </tr>
  <tr>
    <td>Internal Links</td><td>√</td><td>√</td>
  </tr>
  <tr>
    <td>Code Highlight</td><td>√</td><td>√ (Using macros)</td>
  </tr>
  <tr>
    <td>Live Mode</td><td>√</td><td>X</td>
  </tr>
  <tr>
    <td>Ordering Pages</td><td>√</td><td>X (API Limitation)</td>
  </tr>
  <tr>
    <td>Google / Piwik analytics</td><td>√</td><td>X</td>
  </tr>
</table>

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

You might also wish to stick certain links to the bottom of a page. You can do so by appending a '-' to the start of the filename, e.g. a new file `/docs/-Contact_Us.md` will always appear at the bottom of the current list. Weights can also be added to further sort the bottom entries. e.g. `/docs/-01_Coming.md` will appear before `/docs/-02_Soon.md` but both will only appear after all positive or non-weighted files. 

## Landing page

If you want to create a beautiful landing page for your project, create a `_index.md` file in the root of the `/docs` folder. This file will then be used to create a landing page. You can also add a tagline and image to this page using the config file like this:

```json
{
	"title": "Daux.io",
	"tagline": "The Easiest Way To Document Your Project",
	"image": "app.png"
}
```

Note: The image can be a local or remote image. By default, the path is relative to the root of the documentation

## Section Index page

By default, a folder will have no index page. if you wish to have one defined all you need to do is add an `index.md` file to the folder. For example, `/docs/01_Examples` has a landing page for that section since there exists a `/docs/01_Examples/index.md` file.

## Internal links

You can create links from a page to an other, the link is then resolved to the real page.

Creating a link to another page is done exactly like a normal markdown link. In the url part, start with `!` and set the absolute path to the file, omitting the numbering and file extension

A link to `01_Examples/05_Code_Highlighting.md` Would be written like this: `[Code Highlight Examples](!Examples/Code_Highlighting)`

The page generation will fail if a link is wrong.


## Configuration

To customize the look and feel of your documentation, you can create a `config.json` file in the of the `/docs` folder. The `config.json` file is a JSON object that you can use to change some of the basic settings of the documentation.

### Title
Change the title bar in the docs

```json
{
	"title": "Daux.io"
}
```

### Tagline
Change the tagline bar in the docs

```json
{
	"tagline": "The Easiest Way To Document Your Project"
}
```

### Author
Change the documentation's author

```json
{
	"author": "Stéphane Goetz"
}
```

### Ignore
Set custom files and entire folders to ignore within your `/docs` folder. For files make sure to include the file extension in the name. For both files and folders, names are case-sensitive.

```json
	{
		"ignore": {
			"files": ["Work_In_Progress.md"],
			"folders": ["99_Not_Ready"]
		}
	}
```

### Timezone
If your server does not have a default timezone set in php.ini, it may return errors when it tries to generate the last modified date/time for docs. To fix these errors, specify a timezone in your config file. Valid options are available in the [PHP Manual](http://php.net/manual/en/timezones.php).

```json
{
    "timezone": "America/Los_Angeles"
}
```

### Multi-language
Enables multi-language support which needs seperate directories for each language in `docs/` folder.

```json
{
    "languages": { "en": "English", "de": "German" }
}
```

Directory structure:
```
├── docs/
│   ├── _index.md
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

### HTML Export Configuration

#### Themes
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

#### Custom Theme
To use a custom theme, just copy over the theme folder into the `themes` directory and set its value in the `theme` param in config.json

```json
{
	"html": { "theme": "new-theme" }
}
```

#### Code Floating
By default your code blocks will be floated to a column on the right side of your content. To disable this feature, set the `float` property to `false`.

```json
{
	"html": { "float": false }
}
```

#### Toggling Code Blocks
Some users might wish to hide the code blocks & view just the documentation. By setting the `toggle_code` property to `true`, you can offer a toggle button on the page.

```json
{
	"html": { "toggle_code": true }
}
```


#### GitHub Repo
Add a 'Fork me on GitHub' ribbon.

```json
{
	"html": { "repo": "justinwalsh/daux.io" }
}
```

#### Twitter
Include twitter follow buttons in the sidebar.

```json
{
	"html": { "twitter": ["justin_walsh", "todaymade"] }
}
```

#### Links
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

#### Google Analytics
This will embed the google analytics tracking code.

```json
{
	"html": { "google_analytics": "UA-XXXXXXXXX-XX" }
}
```

#### Piwik Analytics
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

#### Breadcrumb titles
Daux.io provides the option to present page titles as breadcrumb navigation. You can *optionally* specify the separator used for breadcrumbs.

```json
{
    "html": {
		"breadcrumbs": true,
		"breadcrumb_separator" : " > "
    }
}
```

#### Date Modified
By default, daux.io will display the last modified time as reported by the system underneath the title for each document. To disable this, change the option in your config.json to false.

```json
{
	"html": { "date_modified": false }
}
```

### Confluence Upload Configuration

#### Configuring the connection
The connection requires three parameters `base_url`, `user` and `pass`. While `user` and `pass` don't really need an explanation, for `base_url` you need to set the path to the server without `rest/api`, this will be added automatically.

```json
{
    "confluence": {
		"base_url": "http://my_confluence_server.com/,
		"user" : "my_username",
		"pass" : "my_password",
    }
}
```

#### Where to upload
Now that the connection is defined, you need to tell it where you want your documentation to be uploaded.

For that you need a `space_id` (name that appears at the beginning of the urls) and an `ancestor_id`; the id of the page that will be the parent of the documentation's homepage.

You can obtain the `ancestor_id` id by editing the page you want to define as a parent: the ID is at the end of the URL

```json
{
    "confluence": {
        "space_id": "my_space",
        "ancestor_id": 50370632
    }
}
```

#### Prefix
Because confluence can't have two pages with the same name in a space, I recommend you define a prefix for your pages.

```json
{
	"confluence": { "prefix": "[DAUX]" }
}
```



## Live mode

Keep in mind, this mode can be used for production, but it is not recommended.

The whole directory must be scanned on each request. This might not make a big impact on small documentations but can be a bottleneck on bigger ones.

### Running Locally

There are several ways to run the docs locally. You can use something like <a href="http://www.mamp.info/en/index.html" target="_blank">MAMP</a> or <a href="http://www.wampserver.com/en/" target="_blank">WAMP</a>.

The easiest is to use PHP 5.4's built-in server.

For that i've included a short command, run `./serve` in the projects folder to start the local web server. By default the server will run at: <a href="http://localhost:8085" target="_blank">http://localhost:8085</a>


### Running Remotely

Copy the files from the repo to a web server that can run PHP 5.3 or greater.

There is an included `.htaccess` for Apache web server.

### Clean URLs configuration

Daux provides native support for Clean URLs provided the webserver has its URL Rewrite module enabled. To enable the same, set the toggle in the `config.json` file in the `/docs` folder.

```json
{
    "live": {
	    "clean_urls": true
	}
}
```

### Running on IIS

If you have set up a local or remote IIS web site, you may need a `web.config` with:

* A rewrite configuration, for handling clean urls.
* A mime type handler for less files, if using a custom theme.

#### Clean URLs

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

## Support

If you need help using Daux.io, or have found a bug, please create an issue on the <a href="https://github.com/justinwalsh/daux.io/issues" target="_blank">GitHub repo</a>.
