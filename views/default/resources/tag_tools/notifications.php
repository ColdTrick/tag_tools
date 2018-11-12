<?php

use Elgg\EntityPermissionsException;

$user = elgg_get_page_owner_entity();
if (!$user instanceof ElggUser || !$user->canEdit()) {
	throw new EntityPermissionsException();
}

// Set the context to settings
elgg_set_context('settings');

$title = elgg_echo('tag_tools:notifications:menu');

// build breadcrumb
elgg_push_breadcrumb(elgg_echo('settings'), elgg_generate_url('settings:account', [
	'username' => $user->username,
]));
if (elgg_is_active_plugin('notifications')) {
	elgg_push_breadcrumb(elgg_echo('notifications:subscriptions:changesettings'), elgg_generate_url('settings:notification:personal', [
		'username' => $user->username,
	]));
}
elgg_push_breadcrumb($title);

// build page elements
$form = elgg_view_form('tag_tools/notifications/edit', [], ['entity' => $user]);

// build page
$body = elgg_view_layout('one_sidebar', [
	'title' => $title,
	'content' => $form,
	'show_owner_block_menu' => false,
]);

// draw page
echo elgg_view_page($title, $body);
