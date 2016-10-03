<?php
/**
 * Start file for the plugin, is loaded when all active plugins are loaded
 *
 * @package tag_tools
 */

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
	elgg_extend_view('output/tag', 'tag_tools/output/tag');
	
	// register events
	elgg_register_event_handler('create', 'metadata', '\ColdTrick\TagTools\Enqueue::createMetadata');
	elgg_register_event_handler('update:after', 'all', '\ColdTrick\TagTools\Enqueue::afterEntityUpdate');
	elgg_register_event_handler('upgrade', 'system', '\ColdTrick\TagTools\Upgrade::markOldTagsAsSent');
	
	// plugin hooks
	elgg_register_plugin_hook_handler('route', 'tags', '\ColdTrick\TagTools\Router::tags');
	elgg_register_plugin_hook_handler('route', 'activity', '\ColdTrick\TagTools\Router::activity');
	elgg_register_plugin_hook_handler('route', 'notifications', '\ColdTrick\TagTools\Router::notifications');
	elgg_register_plugin_hook_handler('register', 'menu:filter', '\ColdTrick\TagTools\MenuItems::registerActivityTab');
	elgg_register_plugin_hook_handler('register', 'menu:page', '\ColdTrick\TagTools\MenuItems::registerSettingsMenuItem');
	elgg_register_plugin_hook_handler('register', 'menu:follow_tag', '\ColdTrick\TagTools\MenuItems::registerFollowTag');
	
	// notifications
	elgg_register_notification_event('relationship', 'tag_tools:notification');
	elgg_register_plugin_hook_handler('get', 'subscriptions', '\ColdTrick\TagTools\Notifications::getSubscribers', 9999);
	elgg_register_plugin_hook_handler('prepare', 'notification:create:relationship:tag_tools:notification', '\ColdTrick\TagTools\Notifications::prepareMessage');
	elgg_register_plugin_hook_handler('send:after', 'notifications', '\ColdTrick\TagTools\Notifications::afterCleanup');
	elgg_register_plugin_hook_handler('relationship:url', 'relationship', '\ColdTrick\TagTools\Notifications::getNotificationURL');
	
	// widgets
	elgg_register_widget_type('follow_tags', elgg_echo('tag_tools:widgets:follow_tags:title'), elgg_echo('tag_tools:widgets:follow_tags:description'), ['profile', 'dashboard']);
	if (elgg_is_active_plugin('tagcloud')) {
		elgg_register_widget_type('tagcloud', elgg_echo('tagcloud'), elgg_echo('tag_tools:widgets:tagcloud:description'), ['profile', 'dashboard', 'index', 'groups'], false);
	}
	
	// actions
	elgg_register_action('tag_tools/follow_tag', dirname(__FILE__) . '/actions/follow_tag.php');
	elgg_register_action('tag_tools/notifications/edit', dirname(__FILE__) . '/actions/notifications/edit.php');
	
	elgg_register_action('tag_tools/upgrades/set_tag_notifications_sent', dirname(__FILE__) . '/actions/upgrades/set_tag_notifications_sent.php', 'admin');
}
