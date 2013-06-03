To update an object in a collection, issue a HTTP POST call with the object in the body of the POST.

	POST /collections/:collection/:id

	name = Justin Walsh
	employment = part
	bio = Documention writer
	...

**URL Parameters**

`:collection` is the key of the collection in the config.

`:id` is the id of the object you want to update.

**Returns**

The object you just updated.