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
	
	/**
	 * Add a menu item to the filter menu
	 *
	 * @param string          $hook         the name of the hook
	 * @param string          $type         the type of the hook
	 * @param \ElggMenuItem[] $return_value current return value
	 * @param mixed           $params       supplied params
	 *
	 * @return void|\ElggMenuItem[]
	 */
	public static function registerActivityTab($hook, $type, $return_value, $params) {
		
		if (!elgg_is_logged_in() || !elgg_in_context('activity')) {
			return;
		}
		
		$tags = tag_tools_get_user_following_tags();
		if (empty($tags)) {
			return;
		}
		
		$selected = false;
		if (strpos(current_page_url(), elgg_normalize_url('activity/tags')) === 0) {
			$selected = true;
		}
		
		$return_value[] = \ElggMenuItem::factory([
			'name' => 'tags',
			'text' => elgg_echo('tags'),
			'href' => 'activity/tags',
			'selected' => $selected,
			'priority' => 9999,
		]);
		
		return $return_value;
	}
	
	/**
	 * Add a menu item to the follow_tag
	 *
	 * @param string          $hook         the name of the hook
	 * @param string          $type         the type of the hook
	 * @param \ElggMenuItem[] $return_value current return value
	 * @param mixed           $params       supplied params
	 *
	 * @return void|\ElggMenuItem[]
	 */
	public static function registerFollowTag($hook, $type, $return_value, $params) {
		
		if (!elgg_is_logged_in()) {
			return;
		}
		
		$tag = elgg_extract('tag', $params);
		if (is_null($tag) || ($tag === '')) {
			return;
		}
		$encoded_tag = htmlspecialchars($tag, ENT_QUOTES, 'UTF-8', false);
		
		$following = tag_tools_is_user_following_tag($tag);
		$action_url = elgg_http_add_url_query_elements('action/tag_tools/follow_tag', [
			'tag' => $encoded_tag,
		]);
		
		$return_value[] = \ElggMenuItem::factory([
			'name' => 'follow_tag_on',
			'text' => elgg_view_icon('refresh'),
			'title' => elgg_echo('tag_tools:follow_tag:menu:on'),
			'href' => $action_url,
			'is_action' => true,
			'item_class' => $following ? 'hidden' : '',
		]);
		
		$return_value[] = \ElggMenuItem::factory([
			'name' => 'follow_tag_off',
			'text' => elgg_view_icon('refresh-hover'),
			'title' => elgg_echo('tag_tools:follow_tag:menu:off'),
			'href' => $action_url,
			'is_action' => true,
			'item_class' => $following ? '' : 'hidden',
		]);
		
		return $return_value;
	}
}
