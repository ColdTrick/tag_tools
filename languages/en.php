<?php
return [

	'admin:tags' => "Tags",
	'admin:tags:search' => "Search",
	'admin:tags:suggest' => "Suggestions",
	'admin:tags:rules' => "Rules",
	'admin:tags:followers' => "Followers",
	
	'item:object:tag_definition' => "Tag definition",
	'item:object:tag_tools_rule' => "Tag Tools rule",
	
	'tag_tools:settings:transform_hashtag' => "Replace #tag in text with a link to the tag page",
	'tag_tools:settings:transform_hashtag:help' => "Replace hashtags in texts with a link to the tag page of that tag",

	'tag_tools:settings:whitelist' => "Enable tags whitelist",
	'tag_tools:settings:whitelist:help' => "Shows a list of frequently used tags when entering new tags",
	
	'tag_tools:settings:separate_notifications' => "How to send out tag notifications",
	'tag_tools:settings:separate_notifications:enabled' => "Send a separate notification for the tag notifications",
	'tag_tools:settings:separate_notifications:disabled' => "Extend the content create notification to include the tag subscribers",
	
	'tag_tools:suggest:ignore' => "Ignore suggestion",
	
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
	
	'tag_tools:admin:followers:created_since' => "Following since",
	'tag_tools:admin:followers:created_until' => "Following until",
	
	'tag_tools:follow_tag:menu:on' => "Track new content with this tag",
	'tag_tools:follow_tag:menu:on:text' => "Follow tag",
	'tag_tools:follow_tag:menu:off' => "Stop tracking new content with this tag",
	'tag_tools:follow_tag:menu:off:text' => "Unfollow tag",
	
	'tag_tools:notifications:menu' => "Tag notifications",
	'tag_tools:notifications:description' => "Configure the tags you wish to monitor and if and how you will be notified when new content is created with one of these tags.",
	'tag_tools:notifications:empty' => "You are currently not following any tags",
	'tag_tools:notifications:follow:search' => "Enter a new tag to follow",
	'tag_tools:notifications:follow:search:help' => "When entering a new tag you will get suggestions for the tag to follow. You can select a tag from the list.",
	'tag_tools:notifications:follow:search:placeholder' => "Search for a tag",
	
	'tag_tools:notification:follow:subject' => "New content with the tag(s): %s",
	'tag_tools:notification:follow:summary' => "New content with the tag(s): %s",
	'tag_tools:notification:follow:message' => "There is new content with the tag(s): %s.

You can see it here: %s",
	'tag_tools:notification:follow:update:subject' => "Updated content with the tag(s): %s",
	'tag_tools:notification:follow:update:summary' => "Updated content with the tag(s): %s",
	'tag_tools:notification:follow:update:message' => "A content item was updated with the tag(s): %s.

You can see it here: %s",
	
	'tag_tools:notification:extended:content_tags' => "The content contains tags you follow: %s",
	
	'widgets:follow_tags:name' => "Following tags",
	'widgets:follow_tags:description' => "Shows the tags that are being followed",
	'widgets:follow_tags:empty' => "This user is currently not following a tag",
	'widgets:tagcloud:name' => "Tagcloud",
	'widgets:tagcloud:description' => "Shows a tagcloud based on all the content on the site, in the group or from the user",
	'widgets:tagcloud:no_data' => "No data available to display a tagcloud",
	
	'tag_tools:actions:follow_tag:success:follow' => "You are now following the tag: %s",
	'tag_tools:actions:follow_tag:success:unfollow' => "You are no longer following the tag: %s",
	
	// tag view page
	'tag_tools:tag:view:title' => "Tag: %s",
	'tag_tools:tag:view:no_results' => "No content related to the tag '%s' could be found.",
	'tag_tools:tag:view:more' => "View more content",
	
	'tag_tools:tag:content:content' => "Recent content",
	'tag_tools:tag:content:groups' => "Groups",
	'tag_tools:tag:content:related_tags' => "Related tags",
	'tag_tools:tag:content:users' => "Users",
	
	// tag definitions
	'tag_tools:tag_definition:manage' => "Manage tag",
	'tag_tools:tag_definition:exists' => "Definition already exists. Forwarding to edit page.",
	
	// add
	'tag_tools:tag_definition:add:title' => "Create a new Tag page",
	
	// edit
	'tag_tools:tag_definition:edit:title' => "Edit a Tag page",
	'tag_tools:tag_definition:edit:field:title' => "Tag",
	'tag_tools:tag_definition:edit:colors:help' => "Setting a custom color will show when displaying the tag. Setting it to black will use the default colors.",
	'tag_tools:tag_definition:edit:field:bgcolor' => "Background color",
	'tag_tools:tag_definition:edit:field:textcolor' => "Text color",
	
	// livesearch
	'tag_tools:livesearch:tags:count' => " - %d items",
];
