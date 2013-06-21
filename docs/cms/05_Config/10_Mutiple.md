The Multiple object is created in the 'config.json' file. This will create a group of many pages (records) that can be edited in the admin panel and displayed on the front end.

	{
	    "multiple-name": {
			"title": "multiple-title",
			"parent": "sample_parent",
			"type": "multiple",
			"fields": {
				
			}
		}
	}

**Parameters**

| Name | Default | Options | Description |
| :------------- | :------------- | :------------- | :------------- |
| title | required | | Descriptive title of the object. This value will be used to identify the object in the admin panel sidebar navigation. Required field. |
| type | multiple | | Object identifier. Required, must be set to: 'multiple.' |
| parent | '' | | Defines parent container object. Allows you to group multiple objects together in the admin panel (like a folder). |
| nav | true | true,false | Allows you to hide or show object elements in the admin panel. |
| filters  | '' | | Creates a select menu in the admin panel that will filter returned records. |
| sort  | '' | asc, desc | Sorts records on call. |
| display  | '' | | Defines values to display in the admin output table for each record. |
| download | true | true,false | Enables raw data download from admin panel. (csv file) |
| redirects | true | true,false | Enables option to create link (url) based records. |
| publish | true | true,false | Removes ability to save individual record as draft. |

## Filters Example

	{
    	"filters": ["value"]
    }

## Sort Example
	{
	    "sort":{
	        "value":"asc"
	    }
	}

## Display Example
	{
	    "display":["fname", "lname", "phone", "email"]
	}