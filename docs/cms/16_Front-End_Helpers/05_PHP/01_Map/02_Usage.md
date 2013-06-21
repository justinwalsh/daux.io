	
The Map object can be initialized with a latitude and longitude:	

	$map1 = new Map(40.11, -100.20);
	$map1->render();

Alternatively, you can add properties to the Map with various setter Methods.

	$map2 = new Map();
	$map2->latlng(40.11, -100.20)->width('640px')->height('480px')->infoWindow('Hello World!')->render();