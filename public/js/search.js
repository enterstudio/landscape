$(document).ready(function() {
	$("ul.pagination").quickPagination({ pageSize:"10" });

	var input = document.getElementById("query");
	input.selectionStart = input.selectionEnd = input.value.length;
});
