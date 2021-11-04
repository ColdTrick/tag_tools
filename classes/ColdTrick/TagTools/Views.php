<?php

namespace ColdTrick\TagTools;

class Views {
	
	/**
	 * Sets some output tag vars
	 *
	 * @param \Elgg\Hook $hook 'view_vars', 'output/tag'
	 *
	 * @return array
	 */
	public static function setOutputTagVars(\Elgg\Hook $hook) {

		$vars = $hook->getValue();
		
		$value = elgg_extract('value', $vars);
		if (elgg_is_empty($value)) {
			return;
		}
		
		elgg_require_css('tag_tools/tagcolors');
		
		$vars['href'] = elgg_generate_url('collection:tag', [
			'tag' => strtolower($value),
		]);
		
		// set name for colors
		$vars['class'] = elgg_extract_class($vars, [elgg_get_friendly_title("tag-color-{$value}")]);
		
		return $vars;
	}
	
	/**
	 * Changes view vars for output/tags
	 *
	 * @param \Elgg\Hook $hook 'view_vars', 'output/tags'
	 *
	 * @return array
	 */
	public static function setOutputTagsVars(\Elgg\Hook $hook) {

		$vars = $hook->getValue();
		
		$value = elgg_extract('value', $vars);
		
		$vars['separator'] = '';
		return $vars;
	}
	
	/**
	 * Adds tagify whitelist to tags input
	 *
	 * @param \Elgg\Hook $hook 'view_vars', 'input/tags'
	 *
	 * @return array
	 */
	public static function setInputTagsWhitelist(\Elgg\Hook $hook) {
		if (!(bool) elgg_get_plugin_setting('whitelist', 'tag_tools')) {
			return;
		}
		
		$whitelist = self::getTagsWhitelist();
		if (empty($whitelist)) {
			return;
		}
		
		$vars = $hook->getValue();
		
		$options = elgg_extract('tagify_options', $vars, []);
		
		$options['whitelist'] = $whitelist;
		$options['dropdown'] = [
			'maxItems' => 20, // <- maximum allowed rendered suggestions
			'closeOnSelect' => true, // <- hide the suggestions dropdown once an item has been selected
			'classname' => 'tags-look', // <- custom classname for this dropdown, so it could be targeted
		];
		
		$vars['tagify_options'] = $options;
		return $vars;
	}
	
	/**
	 * Returns the most frequent used tags within the last 6 months
	 *
	 * @return array
	 */
	protected static function getTagsWhitelist(): array {
		$result = elgg_load_system_cache('tag_tools_whitelist');
		if (!isset($result)) {
			$result = [];
			
			$tags = elgg_call(ELGG_IGNORE_ACCESS, function() {
				return elgg_get_tags([
					'created_after' => '-6 months',
					'threshold' => 5,
					'limit' => 100,
				]);
			});
			
			foreach ($tags as $tag) {
				$result[] = $tag->tag;
			}
			
			sort($result, SORT_NATURAL | SORT_FLAG_CASE);

			elgg_save_system_cache('tag_tools_whitelist', $result);
		}
		
		return $result;
	}
	
	/**
	 * Resets the tags whitelist every day
	 *
	 * @param \Elgg\Hook $hook 'cron', 'daily'
	 *
	 * @return array
	 */
	public static function resetTagsWhitelist(\Elgg\Hook $hook) {
		if (!(bool) elgg_get_plugin_setting('whitelist', 'tag_tools') !== 1) {
			return;
		}
		
		elgg_delete_system_cache('tag_tools_whitelist');
		
		// force update of the whitelist
		self::getTagsWhitelist();
	}
}
