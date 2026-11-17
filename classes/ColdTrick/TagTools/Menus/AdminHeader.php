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
		
		/** @var MenuItems $result */
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
			'href' => elgg_generate_url('admin', [
				'segments' => 'tags/search',
			]),
			'parent_name' => 'tags',
		]);
		
		$result[] = \ElggMenuItem::factory([
			'name' => 'tags:suggest',
			'text' => elgg_echo('admin:tags:suggest'),
			'href' => elgg_generate_url('admin', [
				'segments' => 'tags/suggest',
			]),
			'parent_name' => 'tags',
		]);
		
		$result[] = \ElggMenuItem::factory([
			'name' => 'tags:rules',
			'text' => elgg_echo('admin:tags:rules'),
			'href' => elgg_generate_url('admin', [
				'segments' => 'tags/rules',
			]),
			'parent_name' => 'tags',
		]);
		
		$result[] = \ElggMenuItem::factory([
			'name' => 'tags:followers',
			'text' => elgg_echo('admin:tags:followers'),
			'href' => elgg_generate_url('admin', [
				'segments' => 'tags/followers',
			]),
			'parent_name' => 'tags',
		]);
		
		return $result;
	}
}
