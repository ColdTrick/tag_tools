<?php

namespace ColdTrick\TagTools;

/**
 * Menu item callbacks
 */
class MenuItems {
	
	/**
	 * Add a menu item to the page menu
	 *
	 * @param \Elgg\Event $event 'register', 'menu:filter:settings/notifications'
	 *
	 * @return void|\ElggMenuItem[]
	 */
	public static function registerSettingsMenuItem(\Elgg\Event $event) {
		
		if (!elgg_is_logged_in() || !elgg_in_context('settings')) {
			return;
		}
		
		$user = elgg_get_page_owner_entity();
		if (!$user instanceof \ElggUser) {
			$user = elgg_get_logged_in_user_entity();
		}
		
		$return_value = $event->getValue();
		$return_value[] = \ElggMenuItem::factory([
			'name' => 'tag_notifications',
			'text' => elgg_echo('tag_tools:notifications:menu'),
			'href' => elgg_generate_url('settings:notification:tags', [
				'username' => $user->username,
			]),
			'priority' => 600,
		]);
		
		return $return_value;
	}
	
	/**
	 * Add a menu item to the title menu of a tag page
	 *
	 * @param \Elgg\Event $event 'register', 'menu:title'
	 *
	 * @return void|\ElggMenuItem[]
	 */
	public static function registerFollowTag(\Elgg\Event $event) {
		
		if (!elgg_is_logged_in() || !elgg_in_context('tag')) {
			return;
		}
		
		$tag = $event->getParam('tag');
		if (elgg_is_empty($tag)) {
			return;
		}
		
		$result = $event->getValue();
		
		$following = tag_tools_is_user_following_tag($tag);
		$action_url = elgg_generate_action_url('tag_tools/follow_tag', [
			'tag' => $tag,
		]);
		
		$result[] = \ElggMenuItem::factory([
			'name' => 'follow_tag_on',
			'icon' => 'refresh',
			'text' => elgg_echo('tag_tools:follow_tag:menu:on:text'),
			'title' => elgg_echo('tag_tools:follow_tag:menu:on'),
			'href' => $action_url,
			'item_class' => $following ? 'hidden' : '',
			'link_class' => ['elgg-button', 'elgg-button-action'],
			'data-toggle' => 'follow-tag-off',
		]);
		
		$result[] = \ElggMenuItem::factory([
			'name' => 'follow_tag_off',
			'icon' => 'refresh', // @todo make icon highlighted
			'text' => elgg_echo('tag_tools:follow_tag:menu:off:text'),
			'title' => elgg_echo('tag_tools:follow_tag:menu:off'),
			'href' => $action_url,
			'item_class' => $following ? '' : 'hidden',
			'link_class' => ['elgg-button', 'elgg-button-action'],
			'data-toggle' => 'follow-tag-on',
		]);
		
		return $result;
	}
	
	/**
	 * Add a menu item to the title menu of a tag page
	 *
	 * @param \Elgg\Event $event 'register', 'menu:title'
	 *
	 * @return void|\ElggMenuItem[]
	 */
	public static function registerTagDefinition(\Elgg\Event $event) {
		
		if (!elgg_is_admin_logged_in() || !elgg_in_context('tag')) {
			return;
		}
		
		$tag = $event->getParam('tag');
		if (elgg_is_empty($tag)) {
			return;
		}
		
		$result = $event->getValue();
		
		$colorbox_options = json_encode([
			'innerWidth' => '500px',
		]);
		
		$definition = \TagDefinition::find($tag);
		if ($definition instanceof \TagDefinition) {
			if ($definition->canEdit()) {
				$result[] = \ElggMenuItem::factory([
					'name' => 'edit',
					'icon' => 'edit',
					'text' => elgg_echo('tag_tools:tag_definition:manage'),
					'title' => elgg_echo('edit'),
					'href' => elgg_generate_entity_url($definition, 'edit'),
					'link_class' => ['elgg-button', 'elgg-button-action', 'elgg-lightbox'],
					'data-colorbox-opts' => $colorbox_options,
				]);
			}
		} else {
			$site = elgg_get_site_entity();
			if ($site->canWriteToContainer(0, 'object', \TagDefinition::SUBTYPE)) {
				$result[] = \ElggMenuItem::factory([
					'name' => 'add',
					'icon' => 'plus',
					'text' => elgg_echo('tag_tools:tag_definition:manage'),
					'title' => elgg_echo('add'),
					'href' => elgg_generate_url('add:object:tag_definition', [
						'tag' => $tag,
					]),
					'link_class' => ['elgg-button', 'elgg-button-action', 'elgg-lightbox'],
					'data-colorbox-opts' => $colorbox_options,
				]);
			}
		}
		
		return $result;
	}
	
	/**
	 * Adds admin menu items
	 *
	 * @param \Elgg\Event $event 'register', 'menu:admin_header'
	 *
	 * @return void|\ElggMenuItem[]
	 */
	public static function registerAdminItems(\Elgg\Event $event) {
		
		if (!elgg_is_admin_logged_in()) {
			return;
		}
		
		$result = $event->getValue();
		
		$result[] = \ElggMenuItem::factory([
			'name' => 'tags',
			'text' => elgg_echo('admin:tags'),
			'href' => false,
			'parent_name' => 'configure',
		]);
		
		$result[] = \ElggMenuItem::factory([
			'name' => 'tags:search',
			'href' => 'admin/tags/search',
			'text' => elgg_echo('admin:tags:search'),
			'parent_name' => 'tags',
		]);
		
		$result[] = \ElggMenuItem::factory([
			'name' => 'tags:suggest',
			'href' => 'admin/tags/suggest',
			'text' => elgg_echo('admin:tags:suggest'),
			'parent_name' => 'tags',
		]);
		$result[] = \ElggMenuItem::factory([
			'name' => 'tags:rules',
			'href' => 'admin/tags/rules',
			'text' => elgg_echo('admin:tags:rules'),
			'parent_name' => 'tags',
		]);
		$result[] = \ElggMenuItem::factory([
			'name' => 'tags:followers',
			'href' => 'admin/tags/followers',
			'text' => elgg_echo('admin:tags:followers'),
			'parent_name' => 'tags',
		]);
		
		return $result;
	}
}
