<?php

namespace ColdTrick\TagTools;

class Rules {
	
	/**
	 * Apply a tag rule to the tag
	 *
	 * @param string        $event  the name of the event
	 * @param string        $type   the type of the event
	 * @param \ElggMetadata $object supplied object
	 *
	 * @return void|false
	 */
	public static function applyRules($event, $type, $object) {
		
		if (!($object instanceof \ElggMetadata)) {
			return;
		}
		
		$tag_names = tag_tools_rules_get_tag_names();
		if (!is_array($tag_names) || !in_array($object->name, $tag_names)) {
			// unsupported metadata name
			return;
		}
		
		if (trim($object->value) === '') {
			// don't save 'empty' strings
			return false;
		}
		
		$self_exists = (int) elgg_get_metadata([
			'guid' => $object->entity_guid,
			'metadata_name' => $object->name,
			'metadata_value' => $object->value,
			'count' => true,
			'metadata_case_sensitive' => false,
		]);
		if ($self_exists > 1) {
			// a previous replace already added this tag
			return false;
		}
		
		$entity = $object->getEntity();
		if (!self::validateEntity($entity)) {
			// unsupported entity type/subtype
			return;
		}
		
		// get rule
		$rule = tag_tools_rules_get_rule($object->value);
		if (empty($rule)) {
			return;
		}
		
		switch ($rule->tag_action) {
			case 'delete':
				$rule->notify('delete');
				return false;
				break;
			case 'replace':
				
				$new_value = $rule->to_tag;
				
				$rule->notify('replace');
				
				// check if the new value doesn't already exist with the entity
				$exists = (int) elgg_get_metadata([
					'guid' => $object->entity_guid,
					'metadata_name' => $object->name,
					'metadata_value' => $new_value,
					'count' => true,
					'metadata_case_sensitive' => false,
				]);
				if (!empty($exists)) {
					// new value already exists,
					return false;
				}
				
				$object->value = $new_value;
				$object->save();
								
				break;
		}
	}
	
	/**
	 * Validate if the provided entity can have rules applied
	 *
	 * @param \ElggEntity $entity
	 *
	 * @return bool
	 */
	protected static function validateEntity($entity) {
		
		if (!($entity instanceof \ElggEntity)) {
			// not a valid entity
			return false;
		}
		
		$type_subtypes = tag_tools_rules_get_type_subtypes();
		if (empty($type_subtypes)) {
			// no supported type/subtypes
			return false;
		}
		
		if (!array_key_exists($entity->getType(), $type_subtypes)) {
			// type is not supported
			return false;
		}
		
		if (empty($entity->getSubtype()) || in_array($entity->getSubtype(), $type_subtypes[$entity->getType()])) {
			// no subtype, or subtype in supported subtypes
			return true;
		}
		
		// not supported
		return false;
	}
}
