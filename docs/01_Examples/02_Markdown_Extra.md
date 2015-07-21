Here are some <a href="https://michelf.ca/projects/php-markdown/extra/">Markdown Extra</a> examples.

>To view these, make sure you have turned on the Parsedown Extra support in the global.json file!

#### Markdown inside a html block

<div markdown="1">
This is *true* markdown text.
</div>

#### Header with an id attribute

#### Header 2 ##      {#header2}

#### Link to an HTML ID (#header1)

[Link back to header 1](#header1)

#### Header with a class ##    {.main}

#### Header with multiple classes and an id ##    {.main .shine #the-site}

This is a paragraph introducing:

~~~~~~~~~~~~~~~~~~~~~
a one-line code block
~~~~~~~~~~~~~~~~~~~~~

``````````````````
another code block
``````````````````

~~~

blank line before the code block

~~~

#### Simple table

First Header  | Second Header
------------- | -------------
Content Cell  | Content Cell
Content Cell  | Content Cell


#### Table with aligned values

| Item      | Value |
| --------- | -----:|
| Computer  | $1600 |
| Phone     |   $12 |
| Pipe      |    $1 |

#### Code snippet/function definition

| Function name | Description                    |
| ------------- | ------------------------------ |
| `help()`      | Display the help window.       |
| `destroy()`   | **Destroy your computer!**     |

#### Definition list

Apple
:   Pomaceous fruit of plants of the genus Malus in 
    the family Rosaceae.

Orange
:   The fruit of an evergreen tree of the genus Citrus.


#### Abbreviations

*[HTML]: Hyper Text Markup Language
*[W3C]:  World Wide Web Consortium

HTML

W3C

#### Footnotes

That's some text with a footnote.[^1]


[^1]: And that's the footnote.