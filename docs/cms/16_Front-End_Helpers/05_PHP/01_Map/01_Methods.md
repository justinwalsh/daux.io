object **latlng** ( int *$lat*, int *$lng* )

Sets the latitude and longitude of the Map object.

	$map->latlng(40.11, -100.20);

object **height** ( string *$height* )

Sets the max-height of the Map object in 'px' or '%'.

	$map1->height('400px');
	$map2->height('100%');

object **width** ( string *$width* )

Sets the max-width of the Map object in 'px' or '%'.

	$map1->width('400px');
	$map2->width('100%');

object **infoWindow** ( string *$content* )

Sets the content of the infoWindow displayed in the embeddable Map object.

	$map->infoWindow('Hello World!');

object **infoWindowMaxWidth** ( string *$maxwidth* )

Sets the max-width of the Map object in 'px' or '%'.

	$map1->width('400px');
	$map2->width('100%');

string **render** ( *void* )

	$map->render();

Renders Map object into an embeddable string that displays a Google Map on the webpage.