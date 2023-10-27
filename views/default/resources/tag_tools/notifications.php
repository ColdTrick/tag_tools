<?php

$user = elgg_get_page_owner_entity();

elgg_set_context('settings');

echo elgg_view_page(elgg_echo('tag_tools:notifications:menu'),  [
	'content' => elgg_view_form('tag_tools/notifications/edit', [], ['entity' => $user]),
	'show_owner_block_menu' => false,
	'filter_id' => 'settings/notifications',
	'filter_value' => 'tags',
]);
