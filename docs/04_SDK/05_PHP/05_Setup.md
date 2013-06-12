Create (or add) the 'config.json' and 'connector.php' files to the root of your website.

The connector.php file is the root of the CMS's power. To create a connection to the API server, include the connector.php file and create an instance of the Todaycms class using php.

```php
<?php include($_SERVER["DOCUMENT_ROOT"]."/connector.php"); 
$cms = new Todaycms('API_KEY_HERE'); ?>
```

The $cms variable is now availible to start making calls to the api