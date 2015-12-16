
## Navigation

The navigation is generated automatically with all pages that end with `.md` or `.markdown`

You can have as many sub levels as you wish.

By default, a folder will have no index page.
if you wish to have one defined all you need to do is add an `index.md` file to the folder.
For example, `/docs/02_Examples` has a landing page for that section since there exists a `/docs/02_Examples/index.md` file.

## Sorting
To sort your files and folders in a specific way, you can prefix them with a number and underscore, e.g. `/docs/01_Hello_World.md` and `/docs/05_Features.md` This will list *Hello World* before *Features*, overriding the default alpha-numeric sorting. The numbers will be stripped out of the navigation and urls. For the file `6 Ways to Get Rich`, you can use `/docs/_6_Ways_to_Get_Rich.md`

You might also wish to stick certain links to the bottom of a page. You can do so by appending a '-' to the start of the filename, e.g. a new file `/docs/-Contact_Us.md` will always appear at the bottom of the current list. Weights can also be added to further sort the bottom entries. e.g. `/docs/-01_Coming.md` will appear before `/docs/-02_Soon.md` but both will only appear after all positive or non-weighted files. 
