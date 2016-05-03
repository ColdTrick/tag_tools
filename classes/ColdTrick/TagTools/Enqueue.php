<?php

namespace ColdTrick\TagTools;

class Enqueue {
	
	/** @var \ElggPlugin the Tag Tools plugin entity */
	protected static $plugin;
	
	/**
	 * Listen to the create event of metadata
	 *
	 * @param string        $event    the name of the event
	 * @param string        $type     the type of the event
	 * @param \ElggMetadata $metadata supplied param
	 *
	 * @return void
	 */
	public static function createMetadata($event, $type, $metadata) {
		
		if (!($metadata instanceof \ElggMetadata)) {
			return;
		}
		
		if ($metadata->name !== 'tags') {
			// not a tags metadata
			return;
		}
		
		if (!self::validateEntity($metadata->entity_guid)) {
			return;
		}
		
		self::enqueueEntity($metadata->entity_guid);
	}
	
	/**
	 * After an entity is done with ->save() check if we need to enqueue it
	 *
	 * @param string      $event  the name of the event
	 * @param string      $type   the type of the event
	 * @param \ElggEntity $entity supplied param
	 *
	 * @return void
	 */
	public static function afterEntityUpdate($event, $type, $entity) {
		
		if (!($entity instanceof \ElggEntity)) {
			// not an entity, since we listen to 'all'
			return;
		}
		
		if (!isset($entity->tags)) {
			// no tags
			return;
		}
		
		if (!self::validateEntity($entity->getGUID())) {
			return;
		}
		
		self::enqueueEntity($entity->getGUID());
	}
	
	/**
	 * Check if an entity_guid is valid for sending tag notifications
	 *
	 * @param int $entity_guid the entity GUID
	 *
	 * @return bool
	 */
	protected static function validateEntity($entity_guid) {
		
		$entity_guid = sanitize_int($entity_guid, false);
		if (empty($entity_guid)) {
			return false;
		}
		
		// cache plugin
		self::cachePlugin();
		
		if (check_entity_relationship(self::$plugin->getGUID(), 'tag_tools:notification', $entity_guid)) {
			// already enqueued
			return false;
		}
		
		// can't use elgg get entity because cache is not correctly updated
		$entity_row = get_entity_as_row($entity_guid);
		if ($entity_row === false) {
			// invalid entity
			return false;
		}
		
		$entity_access = sanitise_int($entity_row->access_id);
		if ($entity_access === ACCESS_PRIVATE) {
			// private entity
			return false;
		}
		
		if (!tag_tools_is_notification_entity($entity_guid)) {
			// not supported entity type/subtype
			return false;
		}
		
		return true;
	}
	
	/**
	 * Add an entity to the notification queue
	 *
	 * @param int $entity_guid the entity to enqueue
	 *
	 * @return void
	 */
	protected static function enqueueEntity($entity_guid) {
		
		$entity_guid = sanitise_int($entity_guid, false);
		if (empty($entity_guid)) {
			return;
		}
		
		// cache plugin
		self::cachePlugin();
		
		if (check_entity_relationship(self::$plugin->getGUID(), 'tag_tools:notification', $entity_guid)) {
			// already queued
			return;
		}
		
		add_entity_relationship(self::$plugin->getGUID(), 'tag_tools:notification', $entity_guid);
	}
	
	/**
	 * Cache the plugin for later use
	 *
	 * @return void
	 */
	protected function cachePlugin() {
		
		if (isset(self::$plugin)) {
			return;
		}
		
		self::$plugin = elgg_get_plugin_from_id('tag_tools');
	}
}
