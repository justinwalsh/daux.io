To delete an object in a collection, issue a HTTP `DELETE` call with the id of the object you wish to delete in the url.

	DELETE /collections/:collection/:id

**URL Parameters**

`:collection` is the key of the collection in the config.

`:id` is the id of the object you want to delete.

**Returns**

The object you just deleted.