<?php

namespace ColdTrick\TagTools;

/**
 * Html Formatting callbacks
 */
class HtmlFormatter {

	/**
	 * Replace #tag with a link to the tag page
	 *
	 * @param \Elgg\Event $event 'prepare', 'html'
	 *
	 * @return void|array
	 */
	public static function replaceHashTags(\Elgg\Event $event) {
		
		$result = $event->getValue();
		
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
		
		$ignoreTags = ['head', 'link', 'a', 'script', 'style', 'code', 'pre', 'select', 'textarea', 'button'];
		
		$chunks = preg_split('/(<.+?>)/is', $html, 0, PREG_SPLIT_DELIM_CAPTURE);
		
		$matches = [];
		$openTag = null;
		for ($i = 0; $i < count($chunks); $i++) {
			$text = $chunks[$i];
			
			if ($i % 2 === 0) { // even numbers are text
				// Only process this chunk if there are no unclosed $ignoreTags
				if ($openTag === null) {
					$text = preg_replace_callback('/(^|[^\w])#(\w+[^\s\d[:punct:]\x{2018}-\x{201F}]+\w*)/u', function($matches) {
						$match = trim($matches[0]);
						$tag = $matches[2];
						
						return ' ' . elgg_view('output/url', [
							'text' => $match,
							'href' => elgg_generate_url('collection:tag', [
								'tag' => $tag,
							]),
							'is_trusted' => true,
						]);
					}, $text) ?? $text;
				}
			} else { // odd numbers are tags
				// Only process this tag if there are no unclosed $ignoreTags
				if ($openTag === null) {
					// Check whether this tag is contained in $ignoreTags and is not self-closing
					if (preg_match('`<(' . implode('|', $ignoreTags) . ').*(?<!/)>$`is', $text, $matches)) {
						$openTag = $matches[1];
					}
				} else {
					// Otherwise, check whether this is the closing tag for $openTag.
					if (preg_match('`</\s*' . $openTag . '>`i', $text, $matches)) {
						$openTag = null;
					}
				}
			}
			
			$chunks[$i] = $text;
		}
		
		$result['html'] = implode($chunks);
		
		return $result;
	}
}
