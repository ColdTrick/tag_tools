<?php

$user = elgg_get_page_owner_entity();

// Set the context to settings
elgg_set_context('settings');

// build breadcrumb
elgg_push_breadcrumb(elgg_echo('settings'), elgg_generate_url('settings:account', [
	'username' => $user->username,
]));

// build page elements
$form = elgg_view_form('tag_tools/notifications/edit', [], ['entity' => $user]);

// draw page
echo elgg_view_page(elgg_echo('tag_tools:notifications:menu'),  [
	'content' => $form,
	'show_owner_block_menu' => false,
	'filter_id' => 'settings/notifications',
	'filter_value' => 'tags',
]);
