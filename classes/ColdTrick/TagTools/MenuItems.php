<?php

namespace ColdTrick\TagTools;

class MenuItems {
	
	/**
	 * Add a menu item to the page menu
	 *
	 * @param string          $hook         the name of the hook
	 * @param string          $type         the type of the hook
	 * @param \ElggMenuItem[] $return_value current return value
	 * @param mixed           $params       supplied params
	 *
	 * @return void|\ElggMenuItem[]
	 */
	public static function registerSettingsMenuItem($hook, $type, $return_value, $params) {
		
		if (!elgg_is_logged_in() || !elgg_in_context('settings')) {
			return;
		}
		
		$user = elgg_get_page_owner_entity();
		if (!($user instanceof \ElggUser)) {
			$user = elgg_get_logged_in_user_entity();
		}
		
		$return_value[] = \ElggMenuItem::factory([
			'name' => 'tag_notifications',
			'text' => elgg_echo('tag_tools:notifications:menu'),
			'href' => "notifications/tag/{$user->username}",
			'section' => 'notifications',
		]);
		
		return $return_value;
	}
}
