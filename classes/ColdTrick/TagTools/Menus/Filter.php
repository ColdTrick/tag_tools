<?php

namespace ColdTrick\TagTools\Menus;

use Elgg\Menu\MenuItems;

/**
 * Add menu items to the filter menus
 */
class Filter {
	
	/**
	 * Add a menu item to the page menu
	 *
	 * @param \Elgg\Event $event 'register', 'menu:filter:settings/notifications'
	 *
	 * @return null|MenuItems
	 */
	public static function registerNotificationSettings(\Elgg\Event $event): ?MenuItems {
		if (!elgg_is_logged_in() || !elgg_in_context('settings')) {
			return null;
		}
		
		$user = elgg_get_page_owner_entity();
		if (!$user instanceof \ElggUser) {
			$user = elgg_get_logged_in_user_entity();
		}
		
		/* @var $return_value MenuItems */
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
}
