Update() provides a means of interfacing with the TodayCMS API to update an object or group of objects in a specified collection.

Update() must be used in conjunction with Filter(). An empty filter object will allow Update() to modify all objects in the collection; otherwise, all objects matching the filter object parameters will be modified.

To modify all objects in a collection:

	$listing = $cms->filter()->update('listings', array('name' => 'Listing Name', 'account_id' => '25'));

To modify a subset of objects in a collection:
	
	$listing = $cms->filter(array('state' => 'ND'))->update('listings', array('name' => 'Listing Name', 'account_id' => '25'));

By default, Update() will merge data with the existing data in the object. If you wish to overwrite all data in the object with the newly provided data, you must set the 'overwrite' parameter to 'true'.

	$listing = $cms->param('overwrite', true)->filter()->update('listings', array('name' => 'Listing Name', 'account_id' => '25'));

Returns an associative array of the new object or false.