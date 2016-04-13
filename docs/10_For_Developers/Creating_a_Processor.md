The recommended way to extend Daux is through Processors.

The main advantage, is that you can run it with the source or with `daux.phar` independently. You don't need to hack in the core.

## Adding classes

At the same level as your `daux.phar` file, you will see a `daux` directory, you can create all your classes here.

The classes must respect the PSR-4 Naming convention. And have `\Todaymade\Daux\Extension` as a base namespace.
 
By default, we created a `daux/Processor.php` file to get you started.

## A quick test ?

For the example we're just going to dump the tree and exit.

```php
    public function manipulateTree(Root $root)
    {
        print_r($root->dump());
        exit;
    }
```

also, add this at the beginning of the file:

```php
use Todaymade\Daux\Tree\Root;
```

Let's just try if it works by running `./generate --processor=Processor`

Yes, you get a big array dump! You're good to go.

## What can I achieve ?

There are a few methods that you can override to add some

### Change the parsed tree.

By default, Daux.io parses your directory to find pages. but, for a reason or another, you might want to programmatically add some pages.

This can be done with: 

```php
    public function manipulateTree(Root $root)
    {
    }
```

Two helpers from the class `Todaymade\Daux\Tree\Builder` will greatly help you doing that:

```php
    $new = Builder::getOrCreateDir($root, 'New Pages');

    $page = Builder::getOrCreatePage($new, 'index');
    $page->setContent('The index page for the new folder');

    $page = Builder::getOrCreatePage($new, 'A New Hope');
    $page->setContent('A long time ago in a galaxy far away');
```

Both methods `getOrCreateDir` and `getOrCreatePage` take two parameters : `parent` and `title`

The page will automatically be treated as markdown and converted like a normal page.

If you create a new ContentType, like let's say LaTeX, you would set the title `My Page.tex` it will keep the title `My Page` and use your renderer.
  
If the extension is not mapped to a Generator, it will simply create the file as-is without manipulation.

### Extend the Markdown Generator

You can extend the Markdown Parser in any way wou want with this method.

```php
    public function extendCommonMarkEnvironment(Environment $environment)
    {
    }
```

See the details on [CommonMark's website](http://commonmark.thephpleague.com/customization/overview/).

### Add new generators

You can add new generators to Daux.io and use them right away, they must implement the 
`\Todaymade\Daux\Format\Base\Generator` interface and if you want to use the live mode with your generator 
you have to implement `\Todaymade\Daux\Format\Base\LiveGenerator`. 

```php
    public function addGenerators()
    {
        return ['custom_generator' => '\Todaymade\Daux\Extension\MyNewGenerator'];
    }
```

