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
		$this->attributes['owner_guid'] = $site->getGUID();
		$this->attributes['container_guid'] = $site->getGUID();
		$this->attributes['access_id'] = ACCESS_PUBLIC;
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
		
		// ignore access/disabled entities
		$ia = elgg_set_ignore_access(true);
		$hidden = access_get_show_hidden_status();
		access_show_hidden_entities(true);
		
		// this could take a bit
		set_time_limit(0);
		
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
		
		// restore access/disabled entities
		elgg_set_ignore_access($ia);
		access_show_hidden_entities($hidden);
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
		
		if (empty($entity_types) || empty($tag_names) || is_null($to_tag) || $to_tag === '') {
			return;
		}
		
		$batch = elgg_get_entities_from_metadata([
			'type_subtype_pairs' => $entity_types,
			'metadata_names' => $tag_names,
			'metadata_value' => $this->from_tag,
			'limit' => false,
			'batch' => true,
			'batch_inc_offset' => false,
		]);
		/* @var $entity \ElggEntity */
		foreach ($batch as $entity) {
			
			// check all tag fields
			foreach ($tag_names as $tag_name) {
				$value = $entity->$tag_name;
				if (is_null($value) || ($value === '')) {
					continue;
				}
				
				if (!is_array($value)) {
					$value = [$value];
				}
				
				if (!in_array($this->from_tag, $value)) {
					// this field doesn't contain the original value
					continue;
				}
				
				$key = array_search($this->from_tag, $value);
				unset($value[$key]);
				
				// only add new value if doesn't already contain this
				if (!in_array($this->to_tag, $value)) {
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
		
		$this->entity_types = [];
		
		$entity_types = get_registered_entity_types();
		foreach ($entity_types as $type => $subtypes) {
			if (empty($subtypes) || !is_array($subtypes)) {
				$this->entity_types[$type] = ELGG_ENTITIES_ANY_VALUE;
				continue;
			}
			
			$this->entity_types[$type] = $subtypes;
		}
		
		return $this->entity_types;
	}
}
