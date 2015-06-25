<?php

elgg_gatekeeper();

$user = elgg_get_page_owner_entity();

if (!elgg_instanceof($user, "user") || !$user->canEdit()) {
	forward();
}

// Set the context to settings
elgg_set_context("settings");

$title = elgg_echo("tag_tools:notifications:menu");

// build breadcrumb
elgg_push_breadcrumb(elgg_echo("settings"), "settings/user/" . $user->username);
if (elgg_is_active_plugin("notifications")) {
	elgg_push_breadcrumb(elgg_echo("notifications:subscriptions:changesettings"), "notifications/personal/" . $user->username);
}
elgg_push_breadcrumb($title);

// build page elements
$form = elgg_view_form("tag_tools/notifications/edit", array(), array("entity" => $user));

// build page
$params = array(
	"title" => $title,
	"content" => $form,
);
$body = elgg_view_layout("one_sidebar", $params);

// draw page
echo elgg_view_page($title, $body);
