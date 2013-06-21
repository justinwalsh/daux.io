array **delete** ( string *$collection* )

Delete() provides a means of interfacing with the TodayCMS API to delete an object or group of objects in a specified collection.

Delete() must be used in conjunction with Filter(). An empty filter object will allow Delete() to remove all objects in the collection; otherwise, all objects matching the filter object parameters will be removed from the datastore.

To delete all objects in a collection:

	$listing = $cms->filter()->delete('listings');

To delete a subset of objects in a collection:
	
	$listing = $cms->filter(array('state' => 'ND'))->delete('listings');

Returns an associative array of the new object or false.