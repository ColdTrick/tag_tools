<?php

namespace ColdTrick\TagTools;

class HtmlFormatter {

	/**
	 * Replace #tag with a link to the tag page
	 *
	 * @param \Elgg\Hook $hook 'prepare', 'html'
	 *
	 * @return void|array
	 */
	public static function replaceHashTags(\Elgg\Hook $hook) {
		
		$result = $hook->getValue();
		
		$options = elgg_extract('options', $result);
		$html = elgg_extract('html', $result);
		if (empty($html)) {
			return;
		}
		
		$transform_hashtag = (bool) elgg_get_plugin_setting('transform_hashtag', 'tag_tools');
		$transform_hashtag = (bool) elgg_extract('transform_hashtag', $options, $transform_hashtag);
		if (!$transform_hashtag) {
			return;
		}
		
		$result['html'] = preg_replace_callback('/(^|[^\w])#(\w*[^\s\d!-\/:-@]+\w*)/', function($matches) {
			$match = trim($matches[0]);
			$tag = $matches[2];
			
			return ' ' . elgg_view('output/url', [
				'text' => $match,
				'href' => elgg_generate_url('collection:tag', [
					'tag' => $tag,
				]),
				'is_trusted' => true,
			]);
		}, $html);
		
		return $result;
	}
}
