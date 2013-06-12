To create an object in a collection, issue a HTTP POST call with the object in the body of the POST.

	POST /collections/:collection

**URL Parameters**

`:collection` is the key of the collection in the config.

**Example**

	POST /collections/team

	name = Justin
	employment = full
	bio = Node.js developer
	address[city] = Bismarck
	address[state] = ND
	address[zip] = 58501
	...

Note: 'team' is the key used in our example config at the top of this guide

**Returns**

The object you just created.