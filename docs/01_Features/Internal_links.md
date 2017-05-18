You can create links from a page to an other, the link is then resolved to the real page.

Each relative link in your pages will be resolved to a page or content within the documentation.
If the link's destination isn't found, the page generation will fail.

Any valid markdown link is a valid Daux.io link.

If your file structure looks like this:

```
├── 00_Getting_Started.md
├── 01_Features
│   ├── CommonMark_compliant.md
├── 02_Examples
│   ├── Hello_World.md
│   ├── 05_Code_Highlighting.md
```

From the page `01_Features/CommonMark_compliant.md`, all the following links would be valid:

    [Getting Started](../00_Getting_Started.md)
    [Getting Started](../00_Getting_Started.html)
    [Getting Started](../00_Getting_Started)
    [Getting Started](../Getting_Started)

    // A link starting with / means root of the documentation, not the server it will be served from.
    [Getting Started](/Getting_Started.html)
    [Getting Started](/Getting_Started)

    // These Will first be searched for in the current directory and then start at the root of the documentation
    [Getting Started](Getting_Started)
    [Getting Started](00_Getting_Started)

    [Hello World](../02_Examples/Hello_World.md)
    [Hello World](../02_Examples/Hello_World.html)
    [Hello World](../02_Examples/Hello_World)
    [Hello World](../Examples/Hello_World)
    [Hello World](/02_Examples/Hello_World.md)
    [Hello World](Examples/Hello_World)
    [Hello World](02_Examples/Hello_World)

## Github publishing

If you plan to publish your documentation on Github along with your source code, we recommend to only use relative links with full names.

From the list of links above only these two will work both on Github and on Daux.io

    [Getting Started](../00_Getting_Started.md)
    [Hello World](../02_Examples/Hello_World.md)
