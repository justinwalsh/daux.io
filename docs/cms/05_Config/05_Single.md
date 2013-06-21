The Single object is created in the config.json file. This will create a single page that can be edited in the admin panel and displayed on the front end.

	{
	    "single-name": {
			"title": "single-title",
			"parent": "sample_parent",
			"type": "single",
			"fields": {
				
			}
		}
	}

### Parameters 
| Name | Default | Description |
| :------------- | :------------- | :------------- |
| title | required | Descriptive title of the page. This value will be used to identify the object in the admin panel sidebar navigation. Required field.  |
| type | single | Object identifier. Required, must be set to: 'single.' |
| parent | - | Defines parent container object. Allows you to group multiple objects together in the admin panel (like a folder).  |
| nav | true | Allows you to hide or show object elements in the admin panel. |