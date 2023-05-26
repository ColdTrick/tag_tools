<?php

namespace ColdTrick\TagTools\Menus;

use Elgg\Menu\MenuItems;

/**
 * Add menu items to the admin_header menu
 */
class AdminHeader {
	
	/**
	 * Adds admin menu items
	 *
	 * @param \Elgg\Event $event 'register', 'menu:admin_header'
	 *
	 * @return null|MenuItems
	 */
	public static function register(\Elgg\Event $event): ?MenuItems {
		if (!elgg_is_admin_logged_in()) {
			return null;
		}
		
		/* @var $result MenuItems */
		$result = $event->getValue();
		
		$result[] = \ElggMenuItem::factory([
			'name' => 'tags',
			'text' => elgg_echo('admin:tags'),
			'href' => false,
			'parent_name' => 'configure',
		]);
		
		$result[] = \ElggMenuItem::factory([
			'name' => 'tags:search',
			'text' => elgg_echo('admin:tags:search'),
			'href' => 'admin/tags/search',
			'parent_name' => 'tags',
		]);
		
		$result[] = \ElggMenuItem::factory([
			'name' => 'tags:suggest',
			'text' => elgg_echo('admin:tags:suggest'),
			'href' => 'admin/tags/suggest',
			'parent_name' => 'tags',
		]);
		
		$result[] = \ElggMenuItem::factory([
			'name' => 'tags:rules',
			'text' => elgg_echo('admin:tags:rules'),
			'href' => 'admin/tags/rules',
			'parent_name' => 'tags',
		]);
		
		$result[] = \ElggMenuItem::factory([
			'name' => 'tags:followers',
			'text' => elgg_echo('admin:tags:followers'),
			'href' => 'admin/tags/followers',
			'parent_name' => 'tags',
		]);
		
		return $result;
	}
}
