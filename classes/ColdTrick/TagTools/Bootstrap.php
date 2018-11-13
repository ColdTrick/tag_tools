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
		
		elgg_register_css('tagcolors', elgg_get_simplecache_url('tag_tools/tagcolors.css'));
		elgg_load_css('tagcolors');
		
		$this->extendViews();
		$this->registerEvents();
		$this->registerHooks();
	}
	
	protected function extendViews() {
		elgg_extend_view('elgg.css', 'css/tag_tools/jquery.tagit.css');
		elgg_extend_view('admin.css', 'css/tag_tools/jquery.tagit.css');
		elgg_extend_view('elgg.css', 'tag_tools/site.css');
		elgg_extend_view('admin.css', 'tag_tools/site.css');
		elgg_extend_view('input/tags', 'tag_tools/extend_tags.js');
		
		elgg_extend_view('tag_tools/tag/content', 'tag_tools/tag/content/recent_content', 100);
		elgg_extend_view('tag_tools/tag/content', 'tag_tools/tag/content/groups', 200);
		elgg_extend_view('tag_tools/tag/content', 'tag_tools/tag/content/users', 300);
		elgg_extend_view('tag_tools/tag/content', 'tag_tools/tag/content/related_tags', 400);
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
		$hooks->registerHandler('view_vars', 'output/tag', __NAMESPACE__ . '\Views::setOutputTagVars');
		$hooks->registerHandler('view_vars', 'output/tags', __NAMESPACE__ . '\Views::setOutputTagsVars');
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
