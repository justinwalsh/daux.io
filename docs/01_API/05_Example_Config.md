The following collection config will be used as the config throughout the example calls in this guide.

	{
		"team": {
			"title": "Team Members",
			"type": "multiple",
			"fields": {
				"name": {
					"title": "Name",
					"type": "text"
				},
				"employment": {
					"title": "Employment Status",
					"type": "select",
					"options": {
						"half": "Part Time",
						"full": "Full Time",
						"retired": "Retired"
					}
				},
				"bio": {
					"title": "Bio",
					"type": "textarea"
				},
				"address": {
					"title": "Home Address",
					"type": "location"
				}
			}
		}
	}