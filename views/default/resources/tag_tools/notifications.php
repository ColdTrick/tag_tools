<?php

$user = elgg_get_page_owner_entity();

elgg_set_context('settings');

$content = elgg_view_form('tag_tools/notifications/follow', [
	'action' => elgg_generate_action_url('tag_tools/follow_tag', [], false),
], ['entity' => $user]);

$notifications = elgg_view_form('tag_tools/notifications/edit', [], ['entity' => $user]);
$content .= $notifications ?: elgg_view('page/components/no_results', [
	'no_results' => elgg_echo('tag_tools:notifications:empty'),
]);

echo elgg_view_page(elgg_echo('tag_tools:notifications:menu'),  [
	'content' => $content,
	'show_owner_block_menu' => false,
	'filter_id' => 'settings/notifications',
	'filter_value' => 'tags',
]);
