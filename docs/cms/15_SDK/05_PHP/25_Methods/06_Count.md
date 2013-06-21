Count() will return the number of objects in a collection that match your query. 

To call the API, use the $cms variable and provide the count() method with a collection name:

	$count = $cms->count('listings');

This can be used in conjuction with filters:

	$count = $cms->filter(array('account' => '25'))->count('listings');