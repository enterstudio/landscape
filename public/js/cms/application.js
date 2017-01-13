$(document).ready(function() {
	set_owner_type();
});

function set_owner_type() {
	var type = $('input[name=owner_type]:checked').val();
	if (type == 'new') {
		$('input#owner_name').show();
		$('select#owner_id').hide();
	} else if (type == 'existing') {
		$('input#owner_name').hide();
		$('select#owner_id').show();
	} else {
		alert('Internal error!');
	}
}
