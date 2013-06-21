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

##callbacks
[Learn more](/Editor/Callbacks) about callbacks you can use to hook into the processing of the editor.

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
