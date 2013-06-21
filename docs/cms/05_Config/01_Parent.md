A config object that contains other objects (admin sections) and displays them in a group.

Parent elements are characterized by a subhead in the admin panel navigation. They also group elements in the [[Outline (call)]].

	{
	    "sample_parent": {
	        "title": "Sample Parent",
	        "type": "parent"
	    }
	}

**Parameters**

| Name | Default | Description |
| :------------- | :------------- | :------------- |
| title | required | Descriptive title of the object. This value will be used to identify the object in the admin panel sidebar navigation. Required field. |
| type | parent | Object identifier. Required, must be set to: 'parent' |