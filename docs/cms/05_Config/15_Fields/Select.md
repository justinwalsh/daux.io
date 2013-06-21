Creates a form field type that allows the selection of a single item from a dropdown list.

![static field](http://space.todaymade.com/todaycms/select.jpg)

### Static Options
Displays a static list of options.

    "select": {
        "title": "Select Field - Static Options",
        "type": "select",
        "options": {
            "value1": "Display Name One",
            "value2": "Display Name Two",
            "value3": "Display Name Three"
        }
    }

### Reference Options
Displays a list of the records from another object in the config. This is commonly used to create relational data association. For example, a list of categories or a grouping.

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

    "select-reference": {
        "title": "Select Field - Reference",
        "type": "select",
        "options": "referenced-object",
        "display": "name"
    }

**Parameters**

| Name | Default | Options | Description |
| ------------- | ------------- | ------------- | ------------- |
| options | '' |  | A list of values (object), or reference name for another object in the config. |
| display | '' | | Field name to use as the identifier in the dropdown select |
| blank | '' | string | String uses for the default dropdown select option. Value is set to null. |
| hidden | false | true, false | Hides the select menu and passes a value through as a hidden field. |
| value | | | Sets a default value. |

See also [[Multiselect]].
