Sort allows a Multiple() query to return objects from a collection in a desired order. 

The sort() method accepts an array or JSON string of field types and order direction ("asc" or "desc"). 

	$listings = $cms->sort(array('state' => 'asc', 'name' => 'asc'))->multiple('listings');

	$listings = $cms->sort("{'state':'asc', 'name':'asc'}")->multiple('listings');

Sort() can also be used in conjuction with filters to finely control query output.

	$listings = $cms->sort(array('state' => 'asc', 'name' => 'asc'))->filter(array('account_id' => '25'))->multiple('listings');

	$listings = $cms->sort("{'state':'asc', 'name':'asc'}")->filter("{'account_id':'25')}")->multiple('listings');