define(['jquery', 'elgg/Ajax'], function($, Ajax) {
	
	var ajax = new Ajax();

	$(document).on('click', '.tag-tools-unfollow-tag', function(event) {
		event.preventDefault();
		
		$row = $(this).closest('tr');
		$row.hide();
	
		ajax.path($(this).prop('href'), {
			success: function(json) {
				$row.remove();
			},
			error: function() {
				// Something went wrong, so undo the optimistic changes
				$row.show();
			}
		}); 
		 
		return false;
	});
});
