string **get_api_url** ( string *$collection* )

Get_Api_Url() is a helper method for accessing the current construction of the TodayCMS API call url. It returns the full path to the API along with the api key and all parameters that have been attached with Param(), Sort(), Filter(), Slug(), and Id().
	
	$listing = $cms->param('overwrite', true)->id(25));

	$url = $cms->get_api_url('listings');
	// 'http://path.to.api/collections/listings/25?_tokens&overwrite=true'

