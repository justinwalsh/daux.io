
As you can see on the top of this page, you can add "Edit on Github" links to your pages, this feature can be enabled with a single parameter.

The value has to be the path to the root of your documentation folder in your repository.

In the value you see below, Daux's documentation is in the `docs` folder in the `master` branch.

Daux.io will handle the rest


```json
{
  "html": {
    "edit_on_github": "justinwalsh/daux.io/blob/master/docs"
  }
}
```

## Edit on other VCS

While GitHub is the most popular, it isn't the only, collaborative VCS out there.

As long as you can refer your files by a URL, you can create an edit link for your VCS with the following configuration:


```json
{
  "html": {
    "edit_on": {
      "name": "Bitbucket",
      "basepath": "https://bitbucket.org/onigoetz/daux.io/src/master/docs"
    }
  }
}
```
