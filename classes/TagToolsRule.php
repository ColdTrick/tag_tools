<?php

/**
 * Tag correction rule
 *
 * @property string from_tag   the original value
 * @property string to_tag     the replacement value
 * @property string tag_action which action to take on the tags [delete, replace]
 */
class TagToolsRule extends ElggObject {
	
	const SUBTYPE = 'tag_tools_rule';
	
	/**
	 * @var string[] registered tag names
	 */
	private $tag_names;
	
	/**
	 * @var array the registered entity types in Elgg
	 */
	private $entity_types;
	
	/**
	 * {@inheritDoc}
	 * @see ElggObject::initializeAttributes()
	 */
	protected function initializeAttributes() {
		
		parent::initializeAttributes();
		
		$site = elgg_get_site_entity();
		
		$this->attributes['subtype'] = self::SUBTYPE;
		$this->attributes['owner_guid'] = $site->guid;
		$this->attributes['container_guid'] = $site->guid;
		$this->attributes['access_id'] = ACCESS_PUBLIC;
	}
	
	/**
	 * {@inheritDoc}
	 * @see ElggEntity::__get()
	 */
	public function __get($name) {
		
		$result = parent::__get($name);
		
		if ($name == 'from_tag') {
			return strtolower($result);
		}
		
		return $result;
	}
	
	/**
	 * {@inheritDoc}
	 * @see ElggObject::getDisplayName()
	 */
	public function getDisplayName() {
		
		switch ($this->tag_action) {
			case 'delete':
				return elgg_echo('tag_tools:rule:title:delete', [$this->from_tag]);
				break;
			case 'replace':
				return elgg_echo('tag_tools:rule:title:replace', [$this->from_tag, $this->to_tag]);
				break;
		}
		
		return parent::getDisplayName();
	}
	
	/**
	 * Apply the current rule to all existing content
	 *
	 * @return void
	 */
	public function apply() {
		
		// can we safely execute this?
		$entity_types = $this->getRegisteredEntityTypes();
		$tag_names = $this->getTagNames();
		
		if (empty($entity_types) || empty($tag_names)) {
			// no, we could remove too much
			// quit
			return;
		}
		
		// prepare
		$this->preApply();
		
		elgg_call(ELGG_IGNORE_ACCESS | ELGG_SHOW_DISABLED_ENTITIES, function() {
			try {
				switch ($this->tag_action) {
					case 'delete':
						$this->applyDelete();
						break;
					case 'replace':
						$this->applyReplace();
						break;
				}
			} catch (\Exception $e) {
				elgg_log($e->getMessage(), 'ERROR');
			}
		});
		
		// restore
		$this->postApply();
	}
	
	/**
	 * Creates a system message if needed for the type of rule that is applied
	 *
	 * @param string $type Type of rule applied
	 *
	 * @return void
	 */
	public function notify($type) {
		if (!$this->notify_user) {
			return;
		}
		
		switch ($type) {
			case 'replace':
				system_message(elgg_echo('tag_tools:rule:notify:replace', [$this->from_tag, $this->to_tag]));
				break;
			case 'delete':
				system_message(elgg_echo('tag_tools:rule:notify:delete', [$this->from_tag]));
				break;
		}
	}
	
	/**
	 * Prepare some settings before applying the rule
	 *
	 * @return void
	 */
	protected function preApply() {
		
		// this could take a bit
		set_time_limit(0);
		
		// unregister some events
		elgg_unregister_event_handler('create', 'metadata', '\ColdTrick\TagTools\Rules::applyRules');
		elgg_unregister_event_handler('create', 'metadata', '\ColdTrick\TagTools\Enqueue::createMetadata');
	}
	
	/**
	 * Restore settings after apply is done
	 *
	 * @return void
	 */
	protected function postApply() {
		
		// reregister events
		elgg_register_event_handler('create', 'metadata', '\ColdTrick\TagTools\Rules::applyRules', 1);
		elgg_register_event_handler('create', 'metadata', '\ColdTrick\TagTools\Enqueue::createMetadata');
	}
	
	/**
	 * Apply the rule as delete
	 *
	 * @return void|bool
	 */
	protected function applyDelete() {
		
		$entity_types = $this->getRegisteredEntityTypes();
		$tag_names = $this->getTagNames();
		
		if (empty($entity_types) || empty($tag_names)) {
			return;
		}
		
		return elgg_delete_metadata([
			'type_subtype_pairs' => $entity_types,
			'metadata_names' => $tag_names,
			'metadata_value' => $this->from_tag,
			'metadata_case_sensitive' => false,
			'limit' => false,
		]);
	}
	
	/**
	 * Apply the rule as replace
	 *
	 * @return void
	 */
	protected function applyReplace() {
		
		$entity_types = $this->getRegisteredEntityTypes();
		$tag_names = $this->getTagNames();
		$to_tag = $this->to_tag;
		
		if (empty($entity_types) || empty($tag_names) || elgg_is_empty($to_tag)) {
			return;
		}
		
		$batch = elgg_get_entities([
			'type_subtype_pairs' => $entity_types,
			'metadata_names' => $tag_names,
			'metadata_value' => $this->from_tag,
			'metadata_case_sensitive' => false,
			'limit' => false,
			'batch' => true,
			'batch_inc_offset' => false,
		]);
		/* @var $entity \ElggEntity */
		foreach ($batch as $entity) {
			
			// check all tag fields
			foreach ($tag_names as $tag_name) {
				$value = $entity->$tag_name;
				if (elgg_is_empty($value)) {
					continue;
				}
				
				if (!is_array($value)) {
					$value = [$value];
				}
				
				$found = false;
				$add = true;
				
				foreach ($value as $index => $v) {
					if (strtolower($v) === $this->from_tag) {
						$found = true;
						unset($value[$index]);
						continue;
					}
					
					if ($v === $to_tag) {
						// found replacement value, no need to add
						$add = false;
					}
				}
				
				if (!$found) {
					// this field doesn't contain the original value
					continue;
				}
				
				// only add new value if doesn't already contain this
				if ($add) {
					$value[] = $to_tag;
					
					if (($tag_name === 'tags') && tag_tools_is_notification_entity($entity)) {
						// set tag as notified
						tag_tools_add_sent_tags($entity, [$to_tag]);
					}
				}
				
				// store new value
				$entity->$tag_name = $value;
			}
		}
	}
	
	/**
	 * Get the registered tag names in Elgg
	 *
	 * @return string[]
	 */
	protected function getTagNames() {
		
		if (isset($this->tag_names)) {
			return $this->tag_names;
		}
		
		$this->tag_names = tag_tools_rules_get_tag_names();
		return $this->tag_names;
	}
	
	/**
	 * Get the registered entity types in Elgg
	 *
	 * @return array
	 */
	protected function getRegisteredEntityTypes() {
		
		if (isset($this->entity_types)) {
			return $this->entity_types;
		}
		
		$this->entity_types = tag_tools_rules_get_type_subtypes();
		return $this->entity_types;
	}
}
