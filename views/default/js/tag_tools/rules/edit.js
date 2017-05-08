define(function() {
	var $ = require('jquery');
	
	$(document).on('change', '#tag-tools-rules-edit-action', function() {
		
		if ($(this).val() === 'delete') {
			$('#tag-tools-rules-edit-to').prop('disabled', true).closest('.elgg-field').hide();
		} else {
			$('#tag-tools-rules-edit-to').prop('disabled', false).closest('.elgg-field').show();
		}
	});
});
