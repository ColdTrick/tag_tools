<?php

namespace ColdTrick\TagTools\Forms;

/**
 * Prepare the fields for the tag_tools/rules/edit form
 *
 * @since 5.0
 */
class PrepareFields {
	
	/**
	 * Prepare fields
	 *
	 * @param \Elgg\Event $event 'form:prepare:fields', 'tag_tools/rules/edit'
	 *
	 * @return array
	 */
	public function __invoke(\Elgg\Event $event): array {
		$vars = $event->getValue();
		
		// input names => defaults
		$values = [
			'from_tag' => get_input('from_tag'),
			'to_tag' => get_input('to_tag'),
			'tag_action' => get_input('tag_action', 'replace'),
			'notify_user' => (bool) get_input('notify_user'),
		];
		
		$rule = elgg_extract('entity', $vars);
		if ($rule instanceof \TagToolsRule) {
			foreach (array_keys($values) as $field) {
				if (isset($rule->$field)) {
					$values[$field] = $rule->$field;
				}
			}
		}
		
		return array_merge($values, $vars);
	}
}
