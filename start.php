<?php
/**
 * Start file for the plugin, is loaded when all active plugins are loaded
 *
 * @package tag_tools
 */

require_once(dirname(__FILE__) . "/lib/hooks.php");

// register default Elgg events
elgg_register_event_handler("init", "system", "tag_tools_init");

/**
 * This function is called during the "init" event
 *
 * @return void
 */
function tag_tools_init() {
	
	// register js/ss lib
	elgg_define_js("jquery.tag-it", array("src" => "mod/tag_tools/vendors/jquery/tag_it/js/tag-it.min.js"));
	elgg_extend_view("css/elgg", "css/tag_tools/jquery.tagit.css");
	
	// extend views
	elgg_extend_view("input/tags", "tag_tools/extend_tags");
	
	// plugin hooks
	elgg_register_plugin_hook_handler("route", "tags", "tag_tools_route_tags_hook");
	
}
