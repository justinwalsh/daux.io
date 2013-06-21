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
* Google Analytics
* Optional code float layout

## Demos

This is a list of sites using Daux.io:

* [Daux.io](http://daux.io)

Do you use Daux.io? Send me a pull request or open an [issue](https://github.com/justinwalsh/daux.io/issues) and I will add you to the list.

## Download

Download this repository as a zip, and unpack. Copy the files to a web server that can run PHP. You can also run the documentation locally using Grunt.js, which is covered at the end of this readme.

## Folders

The generator will look for folders in the `/docs` folder. Add your folders inside the `/docs` folder. This project contains some example folders and files to get you started.

You can nest folders any number of levels to get the exact structure you want. The folder structure will be converted to the nested navigation.

## Files

The generator will look for Markdown `*.md` files inside the `/docs` folder and any of the subfolders within /docs.

You must use the `.md` file extension for your files. Also, you must use underscores instead of spaces. Here are some example file names and what they will be converted to:

**Good:**

* 01_Getting_Started.md = Getting Started
* API_Calls.md = API Calls
* 200_Something_Else-Cool.md = Something Else-Cool

**Bad:**

* File Name With Space.md = FAIL

## Sorting

To sort your files and folders in a specific way, you can prefix them with a number and underscore, e.g. `/docs/01_Hello_World.md` and `/docs/05_Features.md` This will list *Hello World* before *Features*, overriding the deafult alpha-numeric sorting. The numbers will be stripped out of the navigation and urls.

## Landing page

If you want to create a beautiful landing page for your project, simply create a `index.md` file in the root of the `/docs` folder. This file will then be used to create a landing page. You can also add a tagline and image to this page using the config file like this:

	{
		"title": "Daux.io",
		"tagline": "The Easiest Way To Document Your Project",
		"image": "/img/app.png"
	}

Note: The image can be a local or remote image.

## Configuration

To customize the look and feel of your documentation, you can create a `config.json` file in the of the `/docs` folder. The `config.json` file is a simple JSON object that you can use to change some of the basic settings of the documentation.

###Title:
Change the title bar in the docs

	{
		"title": "Daux.io"
	}

###Themes:
We have 4 built-in Bootstrap themes. To use one of the themes, just set the `theme` option to one of the following:

* blue
* green
* navy
* red

```
{
	"theme": "blue"
}
```

###Custom Theme:
To create a custom color scheme, set the `theme` property to `custom` and then define the required colors. Copy the following configuration to get started:

	{
		"theme": "custom",
		"colors": {
			"sidebar-background": "#f7f7f7",
			"sidebar-hover": "#c5c5cb",
			"lines": "#e7e7e9",
			"dark": "#3f4657",
			"light": "#82becd",
			"text": "#2d2d2d",
			"syntax-string": "#022e99",
			"syntax-comment": "#84989b",
			"syntax-number": "#2f9b92",
			"syntax-label": "#840d7a"
		}
	}

###Code Floating:
By deafult your code blocks will be floated to a column on the right side of your content. To disable this feature, set the `float` property to `false`.

	{
		"float": false
	}


###Github Repo:
Add a 'Fork me on Github' ribbon.

	{
		"repo": "justinwalsh/daux.io"
	}

###Twitter:
Include twitter follow buttons in the sidebar.

	{
		"twitter": ["justin_walsh", "todaymade"]
	}

###Links:
Include custom links in the sidebar.

	{
		"links": {
			"Github Repo": "https://github.com/justinwalsh/daux.io",
			"Help/Support/Bugs": "https://github.com/justinwalsh/daux.io/issues",
			"Made by Todaymade": "http://todaymade.com"
		}
	}

###Google Analytics:
This will embed the google analytics tracking code.

	{
		"google_analytics": "UA-XXXXXXXXX-XX"
	}

## Running Locally

You can run the docs locally using <a href="http://gruntjs.com/" target="_blank">Grunt.js</a> I assume you are familiar with how to use Grunt and have the latest version of PHP 5.4 installed which is able to run a webserver.

**Requirements:**

* Node.js
* npm
* Grunt.js
* PHP 5.4 or greater

This project contains a package.json file, so once you have the requirements installed, you can simply run a `npm install` and then `grunt` in the projects folder to start the local web server. By default the server will run at: <a href="http://localhost:8085" target="_blank">http://localhost:8085</a>

## Support

If you need help using Daux.io, or have found a bug, please create an issue on the <a href="https://github.com/justinwalsh/daux.io/issues" target="_blank">Github repo</a>.