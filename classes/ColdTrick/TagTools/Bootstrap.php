<?php

namespace ColdTrick\TagTools;

use Elgg\DefaultPluginBootstrap;

class Bootstrap extends DefaultPluginBootstrap {
	
	/**
	 * {@inheritDoc}
	 */
	public function init() {
		
		// ajax views
		elgg_register_ajax_view('tag_tools/tag/view');
				
		// notifications
		elgg_register_notification_event('relationship', 'tag_tools:notification');
		
		$this->extendViews();
		$this->registerEvents();
		$this->registerHooks();
	}
	
	protected function extendViews() {
		elgg_extend_view('elgg.css', 'css/tag_tools/jquery.tagit.css');
		elgg_extend_view('elgg.css', 'tag_tools/site.css');
		elgg_extend_view('input/tags', 'tag_tools/extend_tags.js');
	}
	
	/**
	 * Register plugin hook handlers
	 *
	 * @return void
	 */
	protected function registerHooks() {
		$hooks = $this->elgg()->hooks;
		
		$hooks->registerHandler('filter_tabs', 'activity', __NAMESPACE__ . '\MenuItems::registerActivityTab');
		$hooks->registerHandler('get', 'subscriptions', __NAMESPACE__ . '\Notifications::getSubscribers', 9999);
		$hooks->registerHandler('prepare', 'notification:create:relationship:tag_tools:notification', __NAMESPACE__ . '\Notifications::prepareMessage');
		$hooks->registerHandler('register', 'menu:title', __NAMESPACE__ . '\MenuItems::registerFollowTag');
		$hooks->registerHandler('register', 'menu:title', __NAMESPACE__ . '\MenuItems::registerTagDefinition');
		$hooks->registerHandler('register', 'menu:page', __NAMESPACE__ . '\MenuItems::registerAdminItems');
		$hooks->registerHandler('register', 'menu:page', __NAMESPACE__ . '\MenuItems::registerSettingsMenuItem');
		$hooks->registerHandler('relationship:url', 'relationship', __NAMESPACE__ . '\Notifications::getNotificationURL');
		$hooks->registerHandler('send:after', 'notifications', __NAMESPACE__ . '\Notifications::afterCleanup');
		$hooks->registerHandler('view_vars', 'output/tag', __NAMESPACE__ . '\Views::setTagHref');
	}
	
	/**
	 * Register event handlers
	 *
	 * @return void
	 */
	protected function registerEvents() {
		$events = $this->elgg()->events;
		
		$events->registerHandler('create', 'metadata', __NAMESPACE__ . '\Rules::applyRules', 1);
		$events->registerHandler('create', 'metadata', __NAMESPACE__ . '\Enqueue::createMetadata');
		$events->registerHandler('update:after', 'all', __NAMESPACE__ . '\Enqueue::afterEntityUpdate');
	}
}
