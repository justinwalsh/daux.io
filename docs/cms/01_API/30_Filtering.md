To filter an api call, send the optional `filter` parameter. The filter parameter is a structured json object. Examples use the following terms:

* field key - Is the key used to define the field in the config.
* operator - Is the comparison to preform.
* value - Is what we will compare the object value to.

## Basic Structure

This is the most basic filter that can be preformed.

	{
		"field key":  "value"
	}

*Example Call:*

	GET /collections/team?filter={"employment":"full"}

This will find all full time team members in the collection.

## Advanced Structure

This structure allows more advanced control over operators used when filtering.

	{
		"field key": {
			"operator": "value"
		}
	}

*Example Call:*

	GET /collections/team?filter={"employment": {"!=": "retired"}}

This will find all team members who are not retired in the collection.

## Multiple Operators Structure

You can preform multiple comparision operations on the same field

	{
		"field key": {
			"operator": "value",
			"operator2": "value2",
			"operatorx..": "valuex.."
		}
	}

*Example Call:*

	GET /collections/team?filter={"name": {"LIKE":"Justin%", "!=":"Justin Walsh"}}

This will find all team members who's names start with 'Justin' but are not 'Justin Walsh'.

## Multiple Fields Structure

You can filter multiple fields by adding each field key to the object.

	{
		"field key 1": "value",
		"field key 2": {
			"operator": "value"
		},
		"field key 3": {
			"operator": "value"
		}
	}

## Multiple Values Structure

When filtering, you can use an array instead of a string to match any of the values in the array.

	{
		"employment": ["full", "part"],
		"name": {
			"LIKE": ["Justin%", "Brian%"]
		}
	}

This will match all team members who are full or part time, and whose names starts with 'Justin' or 'Brian'.

Note: `"employment": ["full", "part"]` is equvalient to

	"employment": {
		"=": ["full", "part"]
	}

## Nested Fields Structure

You can filter by nested fields in the object using a dot notation syntax as the field key in the filter. This syntax can be used to query deep into nested objects and arrays, including fields like the 'multi'.

	{
		"address.zip": "58501"
	}

This will filter the location field type by zip code, which is a nested field. This example is based on the sample config at the top of this guide.

## AND/OR Fields Structure (Not Supported Yet)

This structure allows a very customized query including both `-and` and `-or` statments.

	{
		"field key 3": {
			"operator": "value"
		},
		"-or": {
			"field key 1": "value",
			"-and": {
				"field key 2": {
					"operator": "value"
				},
				"field key 4": {
					"operator": "value"
				}
			}
		}
	}


## Supported Operators

| Operator | Description |
| :--- | :--- |
| = | equal to |
| != | not equal to |
| > | greater than |
| < | less than |
| >= | greater than or equal to |
| <= | less than or equal to |
| LIKE | case sensitive search, use '%' for wildcard |
| ILIKE | case insensitive search, use '%' for wildcard |
| NOT LIKE | inverse of 'LIKE' |
| NOT ILIKE | inverse of 'ILIKE' |


## Notes

**JSON Filtering**

The JSON object used for the `filter` parameter must be url encoded when calling the api. Here are examples of how to url encode a json object or string in different languages.

***php***

	$escaped_json = urlencode('{"name": {"LIKE":"Justin%", "!=":"Justin Walsh"}}');

***javascript string***

	var escaped_json_from_string = escape('{"name": {"LIKE":"Justin%", "!=":"Justin Walsh"}}');

***javascript object***

	var escaped_json_from_object = escape(JSON.stringify({"name": {"LIKE":"Justin%", "!=":"Justin Walsh"}}));
