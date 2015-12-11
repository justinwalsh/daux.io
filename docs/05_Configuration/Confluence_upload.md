## Configuring the connection
The connection requires three parameters `base_url`, `user` and `pass`. While `user` and `pass` don't really need an explanation, for `base_url` you need to set the path to the server without `rest/api`, this will be added automatically.

```json
{
    "confluence": {
		"base_url": "http://my_confluence_server.com/,
		"user" : "my_username",
		"pass" : "my_password",
    }
}
```

## Where to upload
Now that the connection is defined, you need to tell it where you want your documentation to be uploaded.

For that you need a `space_id` (name that appears at the beginning of the urls) and an `ancestor_id`; the id of the page that will be the parent of the documentation's homepage.

You can obtain the `ancestor_id` id by editing the page you want to define as a parent: the ID is at the end of the URL

```json
{
    "confluence": {
        "space_id": "my_space",
        "ancestor_id": 50370632
    }
}
```

## Prefix
Because confluence can't have two pages with the same name in a space, I recommend you define a prefix for your pages.

```json
{
	"confluence": { "prefix": "[DAUX]" }
}
```
