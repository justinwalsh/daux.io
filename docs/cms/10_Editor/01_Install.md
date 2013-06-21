To create an editor on a page use the following code

	<div id="cms-editor"></div>
	<script type="text/javascript" src="dist/js/todaycms.js"></script>
	<script type='text/javascript'>
		cms_setup({
			apikey: '7DdPrtGp9ZhKmk',
			collection: "listings"
		});
	</script>

***Required***: 'apikey' and 'collection' are required parameters for the editor to load.