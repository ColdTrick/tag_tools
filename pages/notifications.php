<?php

gatekeeper();

$user = elgg_get_page_owner_entity();

if (elgg_instanceof($user, "user") && $user->canEdit()) {

	// Set the context to settings
	elgg_set_context('settings');
	
	$title = elgg_echo("tag_tools:notifications:menu");
	$description = elgg_echo("tag_tools:notifications:description");
	
	$form = elgg_view_form("tag_tools/notifications/edit");
	
	elgg_push_breadcrumb(elgg_echo("settings"), "settings/user/$user->username");
	elgg_push_breadcrumb(elgg_echo("notifications:subscriptions:changesettings"), "notifications/personal/$user->username");
	elgg_push_breadcrumb($title);
	
	$params = array(
		'content' => elgg_view_module("info", "", $description) . $form,
		'title' => $title,
	);
	$body = elgg_view_layout('one_sidebar', $params);
	
	echo elgg_view_page($title, $body);
	
} else {
	forward();
}
