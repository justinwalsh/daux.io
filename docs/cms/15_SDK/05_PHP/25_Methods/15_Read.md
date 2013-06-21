Read() provides a means for Single() and Multiple() to interface with the TodayCMS API and query the API for objects from a specified collection.  query the API for objects from a specified collection.

	$listings = $cms->read('listings');
	// returns all objects from the 'listings' collection

It can be used in conjunction with Sort() and Filter() to provide fine control over query results.

	$listings1 = $cms->sort(array('name':'asc'))->multiple('listings');
	// returns all objects from the 'listings' collection, sorted in ascending order by 'name'

	$listings2 = $cms->filter(array('account_id':'25'))->multiple('listings');
	// returns all objects from the 'listings' collection with an 'account_id' matching '25'

It can be used in conjuction with 'limit' and 'offset' parameters to determine the maximum number of results and paging.

	$listings = $cms->param('limit', 15)->param('offset', 2)->multiple('listings');
	// returns 15 objects from the second page of results, or objects 16-30