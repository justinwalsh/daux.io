Create() provides a means of interfacing with the TodayCMS API to create a new object in a specified collection.

	$listing = $cms->create('listings', array('name' => 'Listing Name', 'account_id' => '25'));

Returns an associative array of the new object or false.