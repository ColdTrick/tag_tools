<?php

namespace ColdTrick\TagTools;

use Elgg\DefaultPluginBootstrap;

class Bootstrap extends DefaultPluginBootstrap {
	
	/**
	 * {@inheritDoc}
	 */
	public function init() {
		
		// register js/ss lib
		elgg_extend_view('css/elgg', 'css/tag_tools/jquery.tagit.css');
		elgg_extend_view('css/elgg', 'css/tag_tools/follow.css');
		elgg_extend_view('css/admin', 'css/tag_tools/admin.css');
		
		elgg_extend_view('js/elgg', 'js/tag_tools/follow.js');
	
		// ajax views
		elgg_register_ajax_view('tag_tools/tag/view');
				
		// extend views
		elgg_extend_view('input/tags', 'tag_tools/extend_tags');
		elgg_extend_view('output/tag', 'tag_tools/output/tag');
		
		// register events
		elgg_register_event_handler('create', 'metadata', '\ColdTrick\TagTools\Rules::applyRules', 1);
		elgg_register_event_handler('create', 'metadata', '\ColdTrick\TagTools\Enqueue::createMetadata');
		elgg_register_event_handler('update:after', 'all', '\ColdTrick\TagTools\Enqueue::afterEntityUpdate');
		
		// plugin hooks
		elgg_register_plugin_hook_handler('register', 'menu:page', '\ColdTrick\TagTools\MenuItems::registerAdminItems');
		elgg_register_plugin_hook_handler('register', 'menu:filter', '\ColdTrick\TagTools\MenuItems::registerActivityTab');
		elgg_register_plugin_hook_handler('register', 'menu:page', '\ColdTrick\TagTools\MenuItems::registerSettingsMenuItem');
		elgg_register_plugin_hook_handler('register', 'menu:follow_tag', '\ColdTrick\TagTools\MenuItems::registerFollowTag');
		
		// notifications
		elgg_register_notification_event('relationship', 'tag_tools:notification');
		elgg_register_plugin_hook_handler('get', 'subscriptions', '\ColdTrick\TagTools\Notifications::getSubscribers', 9999);
		elgg_register_plugin_hook_handler('prepare', 'notification:create:relationship:tag_tools:notification', '\ColdTrick\TagTools\Notifications::prepareMessage');
		elgg_register_plugin_hook_handler('send:after', 'notifications', '\ColdTrick\TagTools\Notifications::afterCleanup');
		elgg_register_plugin_hook_handler('relationship:url', 'relationship', '\ColdTrick\TagTools\Notifications::getNotificationURL');
	}
}
