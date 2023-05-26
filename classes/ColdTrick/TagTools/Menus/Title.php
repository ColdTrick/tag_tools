<?php

namespace ColdTrick\TagTools\Menus;

use Elgg\Menu\MenuItems;

/**
 * Add menu items to the title menu
 */
class Title {
	
	/**
	 * Add a menu item to the title menu of a tag page
	 *
	 * @param \Elgg\Event $event 'register', 'menu:title'
	 *
	 * @return null|MenuItems
	 */
	public static function registerFollowTag(\Elgg\Event $event): ?MenuItems {
		if (!elgg_is_logged_in() || !elgg_in_context('tag')) {
			return null;
		}
		
		$tag = $event->getParam('tag');
		if (elgg_is_empty($tag)) {
			return null;
		}
		
		/* @var $result MenuItems */
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
	 * Add a menu item to the title menu tag definition management
	 *
	 * @param \Elgg\Event $event 'register', 'menu:title'
	 *
	 * @return null|MenuItems
	 */
	public static function registerTagDefinition(\Elgg\Event $event): ?MenuItems {
		if (!elgg_is_admin_logged_in() || !elgg_in_context('tag')) {
			return null;
		}
		
		$tag = $event->getParam('tag');
		if (elgg_is_empty($tag)) {
			return null;
		}
		
		/* @var $result MenuItems */
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
}
