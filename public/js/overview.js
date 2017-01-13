var counter = 0;

function apply_filter() {
	counter++;
	setTimeout(filter_lists, 500);
}

function strtolower(str) {
	return (str + '').toLowerCase();
}

function strpos(haystack, needle, offset) {
	var i = (haystack + '').indexOf(needle, (offset || 0));
	return i === -1 ? false : i;
}

function filter_lists() {
	if (--counter > 0) {
		return;
	}

	var filter = strtolower($('input#filter').val());

	var sections = [ 'applications', 'business', 'hardware' ];
	sections.forEach(function(section) {
		$('div#'+section+' a').each(function() {
			var text = strtolower($(this).text());

			if (strpos(text, filter) !== false) {
				$(this).show(250);
			} else {
				$(this).hide(250);
			}
		});
	});
}

function clear_filter() {
	$('input#filter').val('');
	filter_lists();
}

$(document).ready(function() {
	filter_lists();
});
