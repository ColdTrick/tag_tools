elgg.provide("elgg.tag_tools");

elgg.tag_tools.init = function() {
	$(".tag-tools-unfollow-tag").click(function() {
		return elgg.tag_tools.unfollow(this);
	});
};

elgg.tag_tools.unfollow = function(elem) {
	$row = $(elem).parents("tr");
	$row.hide();

	elgg.action($(elem).attr('href'), {
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

elgg.register_hook_handler('init', 'system', elgg.tag_tools.init);