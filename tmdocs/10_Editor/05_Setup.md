There are several customizations that can be made to the 'cms_setup' function.

##id
Used to update an existing record.

	<script type='text/javascript'>
		cms_setup({
			apikey: '7DdPrtGp9ZhKmk',
			collection: "listings",
			id: 12345
		});
	</script>

##config
Overrides the collections configuration for only this editor.

In this example we are changing the visibility of the account field:

	<script type='text/javascript'>
		cms_setup({
			apikey: '7DdPrtGp9ZhKmk',
			collection: "listings",
			config: {
				fields: {
					account: {
						hidden:true
					}
				}
			}
		});
	</script>

##data
Overrides the data for this object

	<script type='text/javascript'>
		cms_setup({
			apikey: '7DdPrtGp9ZhKmk',
			collection: "listings",
			data: {
				account: 12345
			}
		});
	</script>

##before_save
Function that is called before the object is saved to the api. It recieves a complete json copy of the object. Use this to make any modifications to the object before it is saved. If you return false, the save will be canceled, this is usefully for valdation errors.

	<script type='text/javascript'>
		cms_setup({
			apikey: '7DdPrtGp9ZhKmk',
			collection: "listings",
			before_save: function(object) {
				if (object.first_name === '') {
					alert('Please enter a first name.');
					return false;
				} else {
					return object;
				}
			}
		});
	</script>

##after_save
Function that is called after the object has been saved. It recieves a complete json copy of the object that was just saved to the database.

	<script type='text/javascript'>
		cms_setup({
			apikey: '7DdPrtGp9ZhKmk',
			collection: "listings",
			after_save: function(object) {
				window.location = "/account/listings/" + object.id;
			}
		});
	</script>

##bootstrap
Enable or disable the bootstrap css. Useful if bootstrap has already been loaded on the page.

	<script type='text/javascript'>
		cms_setup({
			apikey: '7DdPrtGp9ZhKmk',
			collection: "listings",
			bootstrap: false
		});
	</script>

##theme
Sets the theme. Availible themes: light or dark. The light theme is the default. Use false to disable the theme css.

	<script type='text/javascript'>
		cms_setup({
			apikey: '7DdPrtGp9ZhKmk',
			collection: "listings",
			theme: false,
			// or
			// theme: 'dark'
		});
	</script>
