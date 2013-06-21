Filter can be used in conjuction with a Single() or Multiple() query to find specific records in a collection. Applying a filter will allow a query to return records in the collection with matching field values.

An associative array or JSON string can be passed to the filter:

	$listing = $cms->filter(array('account_id' => '25'))->multiple('listings');

or 
	
	$listing = $cms->filter("{'account_id':'25'}")->multiple('listings');