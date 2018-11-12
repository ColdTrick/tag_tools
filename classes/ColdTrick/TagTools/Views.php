<?php

namespace ColdTrick\TagTools;

class Views {
	
	/**
	 * Points tag links to tag landingpage
	 *
	 * @param \Elgg\Hook $hook 'view_vars', 'output/tag'
	 *
	 * @return []
	 */
	public static function setTagHref(\Elgg\Hook $hook) {

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
		return $vars;
	}
}