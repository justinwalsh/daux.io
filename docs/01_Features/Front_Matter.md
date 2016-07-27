To customize your pages even further, you can add a Front Matter to your files.

Front Matter is a block you add at the top of your file and looks like this:

    ---
    title: Hallo Welt
    date: 12th December 1984
    ---

## Changing the title

The only implemented customization right now is the override of the title.

If your file is named "Hello_World_de.md" and your front matter is the one displayed above, you will get a page named "Hallo Welt"

## For Developers

You can then access this information in each `Content` with `$content->getAttributes()`
