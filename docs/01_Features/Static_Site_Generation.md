
If you  don't want to serve the live version of your site, you can also generate files, these can be one of the three supported formats :

- HTML output
- Single page HTML output
- Atlassian Confluence upload

Generating a complete set of pages, with navigation

```bash
./generate --destination=[Output Directory Relative Direction]
```

## Options

For more options, run 

```bash
./generate --help
```

### Specify the configuration file

### Specify the format

Valid options are `html`, `confluence` or `html-file`.
You can also add your own formats through Processors

```bash
./generate --format=html
```

### Specify a processor

A processor can be specified through the  `--processor` option, this should be the name of a class inside the `Todaymade\Daux\Extension` namespace.

By running :

```bash
./generate --processor=Processor
```

Daux will be looking for `Todaymade\Daux\Extension\Processor` inside the `daux` folder.

You can try to run this command, we added a small example Processor.

### Specify the source

By default, the source is taken from the `docs_directory` configuration value in `global.json` but you can override it here.

```bash
./generate --source=docs_to_generate
```

The path can be absolute or relative

### Specify the destination

By default the destination is `static`

```bash
./generate --destination=generated_docs
```

The path can be absolute or relative
