Single is the heart of any website. It represents a single record in the CMS system.

To make a call to the api simply use the $cms variable:

    $page = $cms->key("OBJECT-NAME")->single();

Call a single record by ID

    $cms->key(1234)->single();

Call a single record by slug

    $cms->parent(key)->slug(about-us)->single();