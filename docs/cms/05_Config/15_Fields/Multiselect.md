Creates a form field type that allows the selection of one or more items from a dropdown list.

![multi select static](http://space.todaymade.com/todaycms/multi-select-static.jpg)

### Static Options
Displays a static list of options.

    "multiselect": {
        "title": "Multi Select - Static",
        "type": "multiselect",
        "options": {
            "value": "Display Name",
            "value2": "Display Name 2"
        }
    }

### Reference Options
Displays the records from another object in the config. This is commonly used to create multiple relational data associations.

    "referenced-object": {
        "title": "Referenced Object",
        "type": "multiple",
        "fields": {
            "name": {
            "title": "Record Name",
            "type": "text"
            }
        }
    }

    "multiselect": {
        "title": "Multi Select - Reference",
        "type": "multiselect",
        "options": "referenced-object",
        "display": "name"
    }

**Parameters**

| Name | Default | Options | Description |
| ------------- | ------------- | ------------- | ------------- |
| options | '' |  | A list of values (object), or reference name for another object in the config. |
| display | '' | | Field name to use as the identifier in the dropdown select |

See also [[Select]].