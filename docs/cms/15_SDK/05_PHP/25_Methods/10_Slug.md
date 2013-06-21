object **slug** ( string *$slug* )

Slug() provides shorthand filtering in conjunction with Single(). It will match the collection object with the specified slug parameter.

	$listing = $cms->slug('listing-5')->single('listings');

