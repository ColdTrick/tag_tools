define(function(require) {
	
	var $ = require('jquery');
	var Ajax = require('elgg/Ajax');
	
	$(document).on('click', '#tag-tools-search-results .tag-tools-search-result-tag', function(event) {
		event.preventDefault();
		
		var $link = $(this);
		
		if ($link.siblings().length) {
			// result already present
			$link.siblings().toggleClass('hidden');
		} else {
			// load new data
			var tag = $link.data('tag');
			
			var ajax = new Ajax();
			ajax.view('tag_tools/tag/view', {
				data: {
					tag: tag
				},
				success: function(data) {
					$link.after(data);
				}
			});
		}
		
		// don't act on click
		return false;
	});
});
