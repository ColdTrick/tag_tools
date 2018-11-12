<?php

namespace ColdTrick\TagTools;

class Views {
	
	/**
	 * Sets some output tag vars
	 *
	 * @param \Elgg\Hook $hook 'view_vars', 'output/tag'
	 *
	 * @return []
	 */
	public static function setOutputTagVars(\Elgg\Hook $hook) {

		$vars = $hook->getValue();
		
		$value = elgg_extract('value', $vars);
		if (elgg_is_empty($value)) {
			return;
		}
		
		$url = elgg_generate_url('collection:tag', [
			'tag' => strtolower($value),
		]);
		if (!$url) {
			return;
		}
		
		$vars['href'] = $url;
		
		// set name for colors
		$vars['class'] = elgg_extract_class($vars, [elgg_get_friendly_title("tag-color-{$value}")]);
		
		return $vars;
	}
	
	/**
	 * Changes view vars for output/tags
	 *
	 * @param \Elgg\Hook $hook 'view_vars', 'output/tags'
	 *
	 * @return []
	 */
	public static function setOutputTagsVars(\Elgg\Hook $hook) {

		$vars = $hook->getValue();
		
		$value = elgg_extract('value', $vars);
		
		$vars['separator'] = '';
		return $vars;
	}
}