$(document).ready(function() {
	if ($('input#secret').length > 0) {
		if ($('input#secret').val() != '') {
			secret_change();
		}
	}
});

function password_field() {
	if ($('input#generate:checked').length > 0) {
		$('input#password').val('');
		$('input#password').prop('disabled', true);
	} else {
		$('input#password').prop('disabled', false);
	}
}

function use_secret() {
	if ($('input#set_secret:checked').length == 0) {
		$('input#secret').val('');
	}
}

function secret_change() {
	$('input#set_secret').prop('checked', true);
}

function set_authenticator_code() {
	$.get('/cms/user/authenticator', function(data) {
		$('input#secret').val($(data).find('secret').text());
		secret_change();
	});
}
