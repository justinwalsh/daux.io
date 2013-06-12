This helper was added to the connector.php file, version 2.*. It is designed to display a fully-functioning AJAX web form with validation built in. 

```php
<? if (isset($page['fields']['form'])) { 
       $cms->formbuilder($page['fields']['form'], $page);
   }
?>
```

Returns: HTML/JS