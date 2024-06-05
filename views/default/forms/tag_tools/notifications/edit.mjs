import 'jquery';
import Ajax from 'elgg/Ajax';

var ajax = new Ajax();

$(document).on('click', '.tag-tools-unfollow-tag', function(event) {
	event.preventDefault();
	
	var $row = $(this).closest('tr');
	$row.hide();

	ajax.path($(this).prop('href'), {
		success: function() {
			$row.remove();
		},
		error: function() {
			// Something went wrong, so undo the optimistic changes
			$row.show();
		}
	});
	
	return false;
});
