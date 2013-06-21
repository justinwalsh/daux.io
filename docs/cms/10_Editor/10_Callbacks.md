There are several callbacks availible to hook into the editors processing.

## Structure

All callbacks use the same basic structure:

	<script type='text/javascript'>
		cms_setup({
			apikey: '7DdPrtGp9ZhKmk',
			collection: "listings",
			before_save: function(object, next) {
				if (object.first_name === '' || object.first_name === undefined) {
					alert('Please enter a first name.');
					next(false);
				} else {
					next(object);
				}
			}
		});
	</script>

## Parameters

A callback must accept these two parameters:

`object` - a copy of the data that is about to be saved, or was just saved.
`next` - a function that must be called after your code is finished to continue processing the request.

You must with call `next(object);` to continue or `next(false);` to cancel the request.

## Before Callbacks

Before callbacks are fired before anything is sent to the server to be saved. Use these callbacks to valid user imput, or load remote data and merge it into the object before saving. You can cancel the save completly by calling `next(false);`

| Name | On | Description |
| :--- | :--- | :--- |
| before_create | create | Called before creating a new object in a collection. |
| before_update | update | Called before updating an existing object in a collection. |
| before_save | create or update | Called before saving any object in a collection. |

## After Callbacks

Once the data has been saved to the server, use after callbacks to trigger any other actions you need to happen. An example might be sending a welcome email when someone creates a new object. You can no longer cancel the save, but you can stop further callbacks from executing by calling `next(false);`

| Name | On | Description |
| :--- | :--- | :--- |
| after_create | create | Called after creating a new object in a collection. |
| after_update | update | Called after updating an existing object in a collection. |
| after_save | create or update | Called after saving any object in a collection. |

## Multiple Callbacks

You can use several callbacks together, just remember to call `next(object);` to continue the chain.

## Firing Order

Callbacks are fired in a specific order. Here is a breakdown of each possible chain.

**Creating New Object:**

1. before_create
1. before_save
1. [saves]
1. after_create
1. after_save

**Saving Existing Object:**

1. before_update
1. before_save
1. [saves]
1. after_update
1. after_save