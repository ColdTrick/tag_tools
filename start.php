<?php
/**
 * Start file for the plugin, is loaded when all active plugins are loaded
 *
 * @package tag_tools
 */

require_once(dirname(__FILE__) . '/lib/hooks.php');
require_once(dirname(__FILE__) . '/lib/functions.php');
require_once(dirname(__FILE__) . '/lib/events.php');

// register default Elgg events
elgg_register_event_handler('init', 'system', 'tag_tools_init');

/**
 * This function is called during the 'init' event
 *
 * @return void
 */
function tag_tools_init() {
	
	// register js/ss lib
	elgg_define_js('jquery.tag-it', ['src' => 'mod/tag_tools/vendors/jquery/tag_it/js/tag-it.min.js']);
	elgg_extend_view('css/elgg', 'css/tag_tools/jquery.tagit.css');
	elgg_extend_view('css/elgg', 'css/tag_tools/follow.css');
	
	elgg_extend_view('js/elgg', 'js/tag_tools/follow.js');
	
	// extend views
	elgg_extend_view('input/tags', 'tag_tools/extend_tags');
	
	// register events
	elgg_register_event_handler('create', 'metadata', 'tag_tools_create_metadata_event_handler');
	
	// plugin hooks
	elgg_register_plugin_hook_handler('route', 'tags', '\ColdTrick\TagTools\Router::tags');
	elgg_register_plugin_hook_handler('route', 'activity', '\ColdTrick\TagTools\Router::activity');
	elgg_register_plugin_hook_handler('route', 'notifications', '\ColdTrick\TagTools\Router::notifications');
	elgg_register_plugin_hook_handler('register', 'menu:filter', 'tag_tools_activity_filter_menu_hook_handler');
	elgg_register_plugin_hook_handler('register', 'menu:page', '\ColdTrick\TagTools\MenuItems::registerSettingsMenuItem');
	
	// widgets
	elgg_register_widget_type('follow_tags', elgg_echo('tag_tools:widgets:follow_tags:title'), elgg_echo('tag_tools:widgets:follow_tags:description'), ['profile', 'dashboard']);
	if (elgg_is_active_plugin('tagcloud')) {
		elgg_register_widget_type('tagcloud', elgg_echo('tagcloud'), elgg_echo('tag_tools:widgets:tagcloud:description'), ['profile', 'dashboard', 'index', 'groups'], false);
	}
	
	// actions
	elgg_register_action('tag_tools/follow_tag', dirname(__FILE__) . '/actions/follow_tag.php');
	elgg_register_action('tag_tools/notifications/edit', dirname(__FILE__) . '/actions/notifications/edit.php');
}
