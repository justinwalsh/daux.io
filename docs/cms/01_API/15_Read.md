To read all objects from a collection, issue a HTTP GET call

Entire Collection:

	GET /collections/:collection

Single Object:

	GET /collections/:collection/:id


### URL Parameters

`:collection` is the key of the collection in the config.
`:id` [optional]: is the id of an object in the collection.

### Query Parameters

All query parameters are optional.

| Parameter | Type | Description |
| --- | --- | --- |
| `slug` | String | Find record with the matching slug |
| `filter` | JSON Object | Filters the results - [Read More about Filtering](/API/Filtering) |
| `sort` | JSON Object | Sorts the results - [Read More about Sorting](/API/Sorting) |
| `limit` | Integer | Limit the number of results |
| `offset` | Integer | Offset the results |
| `count` | Boolean | Returns the count of matching records |

### Paging

To page the results, use the `limit` and `offset` parameters together. For example, if you wanted to page your results 20 at a time and you want the 3rd page of results, you would make the following call:

	GET /collections/:collection?limit=20&offset=40

This would return results 41-60 from your collection

### Count

To only recieve the number of results that match the get request, you can send the optional `count` paramenter. This is helpful for finding the number of matches for a filter, or other types of linear data.

Example Call:

	GET /collections/:collection?count=true

Returns:

	{
	    "count": 47
	}

### Returns

An array of objects from the collection.