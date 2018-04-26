<?php

// $user = get_user_by_username($page[1]);
// 				if (empty($user)) {
// 					forward();
// 				} else {
// 					elgg_set_page_owner_guid($user->getGUID());
// 				}


$user = elgg_get_page_owner_entity();

if (!elgg_instanceof($user, 'user') || !$user->canEdit()) {
	forward();
}

// Set the context to settings
elgg_set_context('settings');

$title = elgg_echo('tag_tools:notifications:menu');

// build breadcrumb
elgg_push_breadcrumb(elgg_echo('settings'), 'settings/user/' . $user->username);
if (elgg_is_active_plugin('notifications')) {
	elgg_push_breadcrumb(elgg_echo('notifications:subscriptions:changesettings'), 'notifications/personal/' . $user->username);
}
elgg_push_breadcrumb($title);

// build page elements
$form = elgg_view_form('tag_tools/notifications/edit', [], ['entity' => $user]);

// build page
$body = elgg_view_layout('one_sidebar', ['title' => $title, 'content' => $form]);

// draw page
echo elgg_view_page($title, $body);
