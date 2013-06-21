To filter an api call, send the optional `sort` parameter. The sort parameter is a structured json object.

## Structure

The sort object is a simple `{"key": "direction"}` json object. The key is the field you wish to sort by. The direction is which order to sort the field by. The only two values you can use for the direction are `asc` and `desc`.

	{
		"field_key":  "direction"
	}

*Example Call:*

	GET /collections/team?sort={"name":"asc"}

## Multiple Fields

This structure allows you to sort by more then one field at a time.

	{
		"field key":  "direction",
		"field key 2":  "direction",
		"field key 2":  "direction"
		...
	}

*Example Call:*

	GET /collections/team?sort={"age":"desc", "name":"asc"}