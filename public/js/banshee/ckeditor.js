function add_ckeditor_button(button_selector, textarea_selector) {
	var button = '<input type="button" value="Start CKEditor" class="btn btn-default" onClick="javascript:start_ckeditor(\'' + textarea_selector + '\')">';

	$(button_selector).append(button);
}

function start_ckeditor(selector) {
	CKEDITOR.replace(selector, {
		toolbar: 'Basic'
	});
}
