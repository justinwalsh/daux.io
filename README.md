# Daux.io


[![Latest Version](https://img.shields.io/github/release/justinwalsh/daux.io.svg?style=flat-square)](https://github.com/justinwalsh/daux.io/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://github.com/justinwalsh/daux.io/blob/master/LICENSE.md)
[![Build Status](https://img.shields.io/travis/onigoetz/daux.io/master.svg?style=flat-square)](https://travis-ci.org/onigoetz/daux.io)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/onigoetz/daux.io.svg?style=flat-square)](https://scrutinizer-ci.com/g/onigoetz/daux.io/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/onigoetz/daux.io.svg?style=flat-square)](https://scrutinizer-ci.com/g/onigoetz/daux.io)
[![Total Downloads](https://img.shields.io/packagist/dt/justinwalsh/daux.io.svg?style=flat-square)](https://packagist.org/packages/justinwalsh/daux.io)


**Daux.io** is a documentation generator that uses a simple folder structure and Markdown files to create custom documentation on the fly. It helps you create great looking documentation in a developer friendly way.

## Features

* 100% Mobile Responsive
* CommonMark compliant (a Markdown specification)
* Supports Markdown tables
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

- With a custom theme:
    * [Vulkan Tutorial](https://vulkan-tutorial.com)
    * [TrackJs](http://docs.trackjs.com)
- With the default Theme
    * [Daux.io](https://daux.io/)
    * [Gltn - An open-source word processor webapp](http://felkerdigitalmedia.com/gltn/docs/)
    * [Invade & Annex 3 - An Arma 3 Co-operative Mission](http://ia3.ahoyworld.co.uk/)
    * [Munee: Standalone PHP 5.3 Asset Optimisation & Manipulation](http://mun.ee)
    * [ICADMIN: An admin panel powered by CodeIgniter.](http://istocode.com/shared/ic-admin/)
    * [Cumulus TV: Android TV app that turns any stream/page into a Live Channel](http://cumulustv.herokuapp.com)
    * [Nuntius: A PHP framework for bots](https://roysegall.github.io/nuntius-bot/)

Do you use Daux.io? Send me a pull request or open an [issue](https://github.com/justinwalsh/daux.io/issues) and I will add you to the list.

## Install

```
composer global require justinwalsh/daux.io
```

You can then use the `daux` command line to generate your documentation.

If the command isn't found, ensure your `$PATH` contains `~/.composer/vendor/bin`

## Run on a server

Download this repository as a zip, unpack, and put your documentation in the `docs` folder, you can then serve it with Apache or Nginx.

## `daux`

The command line tool has two commands: `generate` and `serve`, running Daux.io without an argument will automatically run the `generate` command.

You can run `daux --help` to get more details about each command.

## Folders

By default, the generator will look for folders in the `docs` folder. Add your folders inside the `docs` folder. This project contains some example folders and files to get you started.

You can nest folders any number of levels to get the exact structure you want. The folder structure will be converted to the nested navigation.

If you'd prefer to keep your docs somewhere else (like outside of the daux.io root directory) you can specify your docs path in the `global.json` file.

If you need change folder's title in document, you could create a `_index.md` at target folder, and change title property by [Front Matter](http://daux.io/Features/Front_Matter).

```
---
title: You Custom Title
---
```

## Files

The generator will look for Markdown files (`*.md` and `*.markdown`) inside the `docs` folder and any of the subfolders within `docs`.

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

To customize the look and feel of your documentation, you can create a `config.json` file in the of the `/docs` folder.
The `config.json` file is a simple JSON object that you can use to change some of the basic settings of the documentation.

### Title
Change the title bar in the docs

```json
{
	"title": "Daux.io"
}
```

### Themes
We have 4 built-in Bootstrap themes. To use one of the themes, just set the `theme` option to one of the following:

* daux-blue
* daux-green
* daux-navy
* daux-red

```json
{
  "html": { "theme": "daux-green" }
}
```

### More options
Many other options are available:
- [Global options](http://daux.io/Configuration/index)
- [HTML Options](http://daux.io/Configuration/Html_export)
- [Confluence options](http://daux.io/Configuration/Confluence_upload)

## Running Remotely

Copy the files from the repo to a web server that can run PHP 5.4 or greater.

## Running Locally

There are several ways to run the docs locally. You can use something like <a href="http://www.mamp.info/en/index.html" target="_blank">MAMP</a> or <a href="http://www.wampserver.com/en/" target="_blank">WAMP</a>. If you are like me and use alot of Node.js and <a href="http://gruntjs.com/" target="_blank">Grunt.js</a>, then you can use the optional grunt command I have packaged with the project which will start a PHP web server for you in the project folder.

The Grunt.js task uses the built in web server in PHP 5.4 to host the docs on your local machine. This is really only intended be used when you are writing/updating a ton of docs and want to preview the changes locally.

**To use the optional Grunt command you will need:**

* Node.js
* npm
* Grunt.js
* PHP 5.6 or greater

This project contains a package.json file, so once you have the requirements installed, you can simply run a `npm install` and then `grunt` in the projects folder to start the local web server. By default the server will run at: <a href="http://localhost:8085" target="_blank">http://localhost:8085</a>

## Generating a set of static files

These can be uploaded to a static site hosting service such as pages.github.com

Generating a complete set of pages, with navigation

```bash
daux --source=docs --destination=static
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

## Docker

A docker configuration is also provided to run daux within a container, you can either run daux with php5 or php7.

```
cd docker
docker-compose -f docker-compose.7.yml up -d
```

You can then point your browser to http://localhost:8086

## PHP Requirements

Daux.io is compatible with PHP 5.6 and up.

The reason is because some dependencies we have (mainly Symfony and Guzzle) do not support php 5.4 anymore.

### Extensions

PHP Needs the following extension to work : `php-mbstring` and `php-xml`.

If you encounter an error similar to `utf8_decode() not found` this means that you're missing the `php-xml` package. (We've seen it happen only on PHP 7)

## Support

If you need help using Daux.io, or have found a bug, please create an issue on the <a href="https://github.com/justinwalsh/daux.io/issues" target="_blank">GitHub repo</a>.
