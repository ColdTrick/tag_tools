define(function(require) {
	
	var $ = require('jquery');
	
	var unfollow = function(event) {
		event.preventDefault();
		
		$row = $(this).closest('tr');
		$row.hide();
	
		elgg.action($(this).prop('href'), {
			success: function(json) {
				if (json.system_messages.error.length) {
					// Something went wrong, so undo the optimistic changes
					$row.show();
				} else {
					$row.remove();
				}
			},
			error: function() {
				// Something went wrong, so undo the optimistic changes
				$row.show();
			}
		}); 
		 
		return false;
	};
	
	$(document).on('click', '.tag-tools-unfollow-tag', unfollow);
});
