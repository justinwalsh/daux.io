**Daux.io** is an documentation generator that uses a simple folder structure and Markdown files to create custom documentation on the fly. It helps you create great looking documentation in a developer friendly way.

## Features

### For Authors

* [Auto Generated Navigation / Page sorting](!Features/Navigation_and_Sorting)
* [Internal documentation links](!Features/Internal_links)
* [Github Flavored Markdown](!Features/GitHub_Flavored_Markdown)
* [Auto created homepage/landing page](!Features/Landing_page)
* [Multiple Output Formats](!Features/Multiple_Output_Formats)
* [Multiple Languages Support](!Features/Multilanguage)
* [No Build Step](!Features/Live_mode)
* [Static Output Generation](!Features/Static_Site_Generation)

### For Developers

* [Auto Syntax Highlighting](!Features/Auto_Syntax_Highlight)
* [Extend Daux.io with Processors](!For_Developers/Creating_a_Processor)
* Full access to the internal API to create new pages programatically
* Work with pages metadata

### For Marketing

* 100% Mobile Responsive
* 4 Built-In Themes or roll your own
* Functional, Flat Design Style
* Optional code float layout
* Shareable/Linkable SEO Friendly URLs
* Supports Google Analytics and Piwik Analytics

## Demos

This is a list of sites using Daux.io:

* [Daux.io](http://daux.io)
* [jDrupal](http://jdrupal.easystreet3.com/8/docs/)
* [DrupalGap](http://docs.drupalgap.org/8/)
* [Invade & Annex 3 - An Arma 3 Co-operative Mission](http://ia3.ahoyworld.co.uk/)
* [Munee: Standalone PHP 5.3 Asset Optimisation & Manipulation](http://mun.ee)
* [ICADMIN: An admin panel powered by CodeIgniter.](http://istocode.com/shared/ic-admin/)

Do you use Daux.io? Send us a pull request or open an [issue](https://github.com/justinwalsh/daux.io/issues) and I will add you to the list.

## Getting Started

### Download

Download this repository as a zip, and unpack. Copy the files to a web server that can run PHP 5.3 or greater.
You can also run the documentation locally using Grunt.js, which is covered at the end of this readme.

If you don't intend to modify Daux.io and just want to use it, you only need to copy `resources`, `daux.phar`, `global.json`, `generate`, `serve` and `index.php` With these, you're ready to create your documentation.

If however you wish to do some advanced modifications, I recommend you use the raw version and run `composer install` to get started.

### Writing pages

Creating new pages is very easy:
1. Create a markdown file (`*.md` or `*.markdown`)
2. Start writing

By default, the generator will look for folders in the `docs` folder.
Add your folders inside the `docs` folder. This project contains some example folders and files to get you started.

You can nest folders any number of levels to get the exact structure you want.
The folder structure will be converted to the nested navigation.

You must use underscores instead of spaces. Here are some example file names and what they will be converted to:

**Good:**

* 01_Getting_Started.md = Getting Started
* API_Calls.md = API Calls
* 200_Something_Else-Cool.md = Something Else-Cool
* _5_Ways_to_Be_Happy.md = 5 Ways To Be Happy

**Bad:**

* File Name With Space.md = FAIL

### See your pages

Now you can see your pages. you have two options for that : serve them directly, or generate to various formats.

We recommend the first one while you write your documentation, you get a much quicker feedback while writing.

#### Serving files

You can use PHP's embedded web server by running the following command in the root of your documentation

```
./serve
```

Upload your files to an apache / nginx server and see your documentation

[More informations here](!Features/Live_mode)

#### Export to other formats

Daux.io is extendable and comes by default with three export formats:

- Export to HTML, same as the website, but can be hosted without PHP.
- Export all documentation in a single HTML page
- Upload to your Atlassian Confluence server.

[See a detailed feature comparison matrix](!Features/Multiple_Output_Formats)

Here's how you run an export:

```bash
./generate
```

[See here for all options](!Features/Static_Site_Generation)

## Configuration

Now that you got the basics, you can also [see what you can configure](!Configuration)


## Known Issues

- __Windows UTF-8 files support__ Files with UTF-8 characters cannot be handled on windows, this issue has no known fix yet.


## Support

If you need help using Daux.io, or have found a bug, please create an issue on the <a href="https://github.com/justinwalsh/daux.io/issues" target="_blank">GitHub repo</a>.
