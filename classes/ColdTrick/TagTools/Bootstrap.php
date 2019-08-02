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
		
		elgg_require_css('tag_tools/tagcolors');
		
		$this->extendViews();
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
}
