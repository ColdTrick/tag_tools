define(["jquery", "elgg", "jquery/tag-it"], function ($, elgg) {
	
	elgg.provide("elgg.tag_tools.autocomplete");
		
	elgg.tag_tools.autocomplete.initialize = function (elem) {
		
		$(elem).parent().addClass("ui-front");

		$(elem)
		// don't navigate away from the field on tab when selecting an item
		.bind("keydown", function(event) {
			if ((event.keyCode === $.ui.keyCode.TAB) && $(this).data("autocomplete").menu.active) {
				event.preventDefault();
			}
		})
		.tagit({
			caseSensitive: false,
			allowSpaces: true,
			placeholderText: $(elem).data('tagitPlaceholder'),
			tagSource: function(search, response) {
				elgg.getJSON('tags/autocomplete', {
					data: {
						q: search.term.toLowerCase(),
					},
					success: response
				});
			}
		});
		
		$(elem).insertAfter($(elem).next('.tagit')).on('invalid', function() {
			$(this).prev('.tagit').addClass('elgg-input-required');
		}).on('change', function() {
			$(this).prev('.tagit').removeClass('elgg-input-required');
		});

	};
	
	return function() {
		$(".elgg-input-tags").each(function() {
			if (!$(this).data().tagToolsInitialized) {
				elgg.tag_tools.autocomplete.initialize(this);
			}
			$(this).data("tagToolsInitialized", true);
		});
	};
});
