You can handle multiple languages in your documentation, each with it's own navigation.

Add this to your config.json :

```json
{
    "languages": { "en": "English", "de": "German" }
}
```

You will the need separate directories for each language in `docs/` folder.
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
