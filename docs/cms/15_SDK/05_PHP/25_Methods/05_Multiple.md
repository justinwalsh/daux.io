Multiple objects represent a group of records in the CMS system.

To make a call to the api simply use the $cms variable:

    $pages = $cms->key("OBJECT-NAME")->multiple();

You can also filter your results base field name. 

    $pages = $cms->key("OBJECT-NAME")->filter(FIELD-NAME, FIELD-VALUE)->multiple();