To update an object in a collection, issue a HTTP `PUT` call with the object in the body of the POST.

	PUT /collections/:collection/:id

	name = Justin Walsh
	employment = part
	bio = Documention writer
	...

### URL Parameters

`:collection` is the key of the collection in the config.

`:id` is the id of the object you want to update.


### Query Parameters

All query parameters are optional.

| Parameter | Type | Default | Description |
| --- | --- | --- | --- |
| `overwrite` | Boolean | `false` | Setting this to `true` will force the server to completely overwrite the object in the database instead of doing a partial update |

### Returns

The object you just updated.