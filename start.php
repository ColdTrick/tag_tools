<?php
/**
 * Start file for the plugin, is loaded when all active plugins are loaded
 *
 * @package tag_tools
 */

require_once(dirname(__FILE__) . "/lib/hooks.php");
require_once(dirname(__FILE__) . "/lib/functions.php");
require_once(dirname(__FILE__) . "/lib/events.php");

// register default Elgg events
elgg_register_event_handler("init", "system", "tag_tools_init");

/**
 * This function is called during the "init" event
 *
 * @return void
 */
function tag_tools_init() {
	
	elgg_register_event_handler("pagesetup", "system", "tag_tools_pagesetup");
	
	// register js/ss lib
	elgg_define_js("jquery.tag-it", array("src" => "mod/tag_tools/vendors/jquery/tag_it/js/tag-it.min.js"));
	elgg_extend_view("css/elgg", "css/tag_tools/jquery.tagit.css");
	elgg_extend_view("css/elgg", "css/tag_tools/follow");
	
	elgg_extend_view("js/elgg", "js/tag_tools/follow");
	
	// extend views
	elgg_extend_view("input/tags", "tag_tools/extend_tags");
	
	// register events
	elgg_register_event_handler("create", "metadata", "tag_tools_create_metadata_event_handler");
	
	// plugin hooks
	elgg_register_plugin_hook_handler("route", "tags", "tag_tools_route_tags_hook");
	elgg_register_plugin_hook_handler("route", "activity", "tag_tools_route_activity_hook");
	elgg_register_plugin_hook_handler("route", "notifications", "tag_tools_route_notifications_hook");
	elgg_register_plugin_hook_handler("register", "menu:filter", "tag_tools_activity_filter_menu_hook_handler");
	
	// widgets
	elgg_register_widget_type("follow_tags", elgg_echo("tag_tools:widgets:follow_tags:title"), elgg_echo("tag_tools:widgets:follow_tags:description"), array("profile", "dashboard"));
	
	// actions
	elgg_register_action("tag_tools/follow_tag", dirname(__FILE__) . "/actions/follow_tag.php");
	elgg_register_action("tag_tools/notifications/edit", dirname(__FILE__) . "/actions/notifications/edit.php");
}

/**
 * This function is called during the "pagesetup" event
 *
 * @return void
 */
function tag_tools_pagesetup() {
	
	if (!elgg_is_logged_in() || !elgg_in_context("settings")) {
		return;
	}
	
	$user = elgg_get_page_owner_entity();
	if (!$user) {
		$user = elgg_get_logged_in_user_entity();
	}
	
	$params = array(
		"name" => "tag_notifications",
		"text" => elgg_echo("tag_tools:notifications:menu"),
		"href" => "notifications/tag/" . $user->username,
		"section" => "notifications",
	);
	elgg_register_menu_item("page", $params);
}
