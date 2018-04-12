<?php
return [

	'admin:upgrades:set_tag_notifications_sent' => "Tag tools - set all tags as sent",
	'admin:upgrades:set_tag_notifications_sent:description' => "Mark all tags on supported content as sent. This is usefull when coming for an older version of the plugin or is this is a new plugin.",

	'admin:tags' => "Tags",
	'admin:tags:search' => "Search",
	'admin:tags:suggest' => "Suggest",
	'admin:tags:rules' => "Rules",
	
	'item:object:tag_tools_rule' => "Tag Tools rule",
	
	'tag_tools:admin:tags:suggest:info' => "The list below shows suggestions for autocorrection rules.",
	'tag_tools:admin:tags:suggest:results:title' => "Suggestions",
	'tag_tools:admin:tags:suggest:item' => "Tag <b>%s</b> could be autocorrected to:",
	
	'tag_tools:rule:title:delete' => "Delete tag: %s",
	'tag_tools:rule:title:replace' => "Replace tag '%s' with '%s'",
	
	'tag_tools:rule:notify:replace' => "The tag '%s' has been replaced with '%s'",
	'tag_tools:rule:notify:delete' => "The tag '%s' has been removed",
	
	'tag_tools:search:count' => "Count",
	'tag_tools:search:rules' => "Rules",
	'tag_tools:search:replace' => "Replace",
	'tag_tools:search:min_count' => "Minimal occurance",
	'tag_tools:search:content_type' => "Filter on content type",
	'tag_tools:search:order' => "Sorting order",
	
	'tag_tools:rules:add' => "Create a new tag rule",
	'tag_tools:rules:edit' => "Edit tag rule: %s",
	'tag_tools:rules:from_tag' => "From tag",
	'tag_tools:rules:from_tag:help' => "This tag will be matched case insensitive",
	'tag_tools:rules:to_tag' => "To tag",
	'tag_tools:rules:tag_action' => "Tag action",
	'tag_tools:rules:notify_user' => "Show system message to user when rule is applied",
	'tag_tools:rules:tag_action:replace' => "Replace",
	'tag_tools:rules:tag_action:delete' => "Delete",
	'tag_tools:rules:save_execute' => "Save & execute",
	'tag_tools:rules:execute' => "Execute",
	
	'tag_tools:follow_tag:menu:on' => "Track new content with this tag",
	'tag_tools:follow_tag:menu:off' => "Stop tracking new content with this tag",
	'tag_tools:js:autocomplete:required' => "Please fill in the required tags",
	'tag_tools:notifications:menu' => "Tag notifications",
	'tag_tools:notifications:description' => "Configure the tags you wish to monitor and if and how you will be notified when new content is created with one of these tags.",
	'tag_tools:notifications:empty' => "You currently follow no tags. If you see a tag you would like to follow. Click the follow tag icon next to the tag.",
	'tag_tools:notification:follow:subject' => "New content with the tag(s): %s",
	'tag_tools:notification:follow:summary' => "New content with the tag(s): %s",
	'tag_tools:notification:follow:message' => "Hi,

there is new content with the tag(s): %s.

You can see it here: %s",
	'tag_tools:notification:follow:update:subject' => "Updated content with the tag(s): %s",
	'tag_tools:notification:follow:update:summary' => "Updated content with the tag(s): %s",
	'tag_tools:notification:follow:update:message' => "Hi,

a content item was updated with the tag(s): %s.

You can see it here: %s",
	'tag_tools:activity:tags' => "Activity based on your tags",
	'tag_tools:widgets:follow_tags:title' => "Following tags",
	'tag_tools:widgets:follow_tags:description' => "Shows the tags that are being followed",
	'tag_tools:widgets:follow_tags:empty' => "This user is currently not following a tag",
	'tag_tools:widgets:tagcloud:description' => "Shows a tagcloud based on all the content on the site, in the group or from the user",
	'tag_tools:widgets:tagcloud:no_data' => "No data available to display a tagcloud",
	
	'tag_tools:actions:follow_tag:success:follow' => "You are now following the tag: %s",
	'tag_tools:actions:follow_tag:success:unfollow' => "You are no longer following the tag: %s",
	
];
