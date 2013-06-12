Creates a hidden form field.

    "hidden": {
        "title": "Hidden Field",
        "type": "hidden",
    }

    "hidden-forced-value": {
        "title": "Hidden Forced Value Field",
        "type": "hidden",
        "value": "Forced Value"
    }

    "hidden-default-value": {
        "title": "Hidden Default Value Field",
        "type": "hidden",
        "default": "Default Value"
    }

    "hidden-timestamp-value": {
        "title": "Hidden Timestamp Value Field",
        "type": "hidden",
        "default": "timestamp"
    }

    "hidden-display-value": {
        "title": "Hidden Display Field",
        "type": "hidden",
        "value": "You should see this",
        "display": true
    }


**Parameters**

| Name | Default | Options | Description |
| ------------- | ------------- | ------------- | ------------- |
| default | '' | string, timestamp | Sets a default value for the hidden field. Timestamp will display current date/time. |
| display | false | true,false | Can see the value but can't change it |
| auto_increment | false | true, false | Creates unique id starting with 1. |
