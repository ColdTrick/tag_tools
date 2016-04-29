<?php

namespace ColdTrick\TagTools;

class Notifications {
	
	/**
	 * Prevent subscribers for the tag notifications
	 *
	 * @param string $hook         the name of the hook
	 * @param string $type         the type of the hook
	 * @param array  $return_value current return value
	 * @param array  $params       supplied params
	 *
	 * @return void|array
	 */
	public static function getSubscribers($hook, $type, $return_value, $params) {
		
		if (!self::validateNotificationEvent($params)) {
			// not the correct notification event
			return;
		}
		
		/* @var $event \Elgg\Notifications\Event */
		$event = elgg_extract('event', $params);
		
		/* @var $relationship \ElggRelationship */
		$relationship = $event->getObject();
		
		$entity = get_entity($relationship->guid_two);
		
		$sending_tags = self::getUnsendTags($entity);
		if (empty($sending_tags)) {
			return [];
		}
		
		$tag_subscribers = [];
		// get interested users
		$user_options = [
			'type' => 'user',
			'annotation_name_value_pairs' => [
				'name' => 'follow_tag',
				'value' => $sending_tags,
			],
			'limit' => false,
		];
		$users_batch = new \ElggBatch('elgg_get_entities_from_annotations', $user_options);
		/* @var $user \ElggUser */
		foreach ($users_batch as $user) {
			
			// check user access
			if (!has_access_to_entity($entity, $user)) {
				continue;
			}
			
			// get the notification settings of the user for one of the sending tags
			// this will prevent duplicate notifications,
			// but also maybe less control over the method
			foreach ($sending_tags as $tag) {
				$notifiction_settings = tag_tools_get_user_tag_notification_settings($tag, $user->getGUID());
				if (empty($notifiction_settings)) {
					continue;
				}
				
				$tag_subscribers[$user->getGUID()] = $notifiction_settings;
				continue(2);
			}
		}
		
		if (!empty($tag_subscribers)) {
			return $tag_subscribers;
		}
		
		return [];
	}
	
	/**
	 * Make the tag tools notification
	 *
	 * @param string                           $hook         the name of the hook
	 * @param string                           $type         the type of the hook
	 * @param \Elgg\Notifications\Notification $return_value current return value
	 * @param array                            $params       supplied params
	 *
	 * @return void|\Elgg\Notifications\Notification
	 */
	public static function prepareMessage($hook, $type, $return_value, $params) {
		
		if (!self::validateNotificationEvent($params)) {
			return;
		}
		
		$recipient = $return_value->getRecipient();
		$method = elgg_extract('method', $params);
		$relationship = elgg_extract('object', $params);
		$language = elgg_extract('language', $params);
		
		$entity = get_entity($relationship->guid_two);
		
		$sending_tags = self::getUnsendTags($entity);
		$tag = false;
		foreach ($sending_tags as $sending_tag) {
			
			if (!tag_tools_check_user_tag_notification_method($sending_tag, $method, $recipient->getGUID())) {
				continue;
			}
			
			$tag = $sending_tag;
			break;
		}
		
		$return_value->subject = elgg_echo('tag_tools:notification:follow:subject', [$tag], $language);
		$return_value->summary = elgg_echo('tag_tools:notification:follow:summary', [$tag], $language);
		$return_value->body = elgg_echo('tag_tools:notification:follow:message', [$tag, $entity->getURL()], $language);
		
		return $return_value;
	}
	
	/**
	 * Cleanup some stuff
	 *
	 * @param string $hook         the name of the hook
	 * @param string $type         the type of the hook
	 * @param null   $return_value current return value
	 * @param array  $params       supplied params
	 *
	 * @return void
	 */
	public static function afterCleanup($hook, $type, $return_value, $params) {
		
		if (!self::validateNotificationEvent($params)) {
			// not the correct notification event
			return;
		}
		
		/* @var $event \Elgg\Notifications\Event */
		$event = elgg_extract('event', $params);
		
		/* @var $relationship \ElggRelationship */
		$relationship = $event->getObject();
		
		$entity = get_entity($relationship->guid_two);
		
		// cleanup the relationship
		remove_entity_relationships($entity->getGUID(), 'tag_tools:notification', true);
		
		// save the newly sent tags
		$sending_tags = self::getUnsendTags($entity);
		if (empty($sending_tags)) {
			return;
		}
		
		$sent_tags = $entity->getPrivateSetting('tag_tools:sent_tags');
		if (!empty($sent_tags)) {
			$sent_tags = json_decode($sent_tags, true);
		} else {
			$sent_tags = [];
		}
		
		// store all processed tags
		$processed_tags = array_merge($sent_tags, $sending_tags);
		$entity->setPrivateSetting('tag_tools:sent_tags', json_encode($processed_tags));
	}
	
	/**
	 * Validate that we have a tag_tools notification event
	 *
	 * @param array $params the hook params to check
	 *
	 * @return bool
	 */
	protected static function validateNotificationEvent($params) {
		
		if (empty($params) || !is_array($params)) {
			return false;
		}
		
		$event = elgg_extract('event', $params);
		if (!($event instanceof \Elgg\Notifications\Event)) {
			return false;
		}
		
		$relationship = $event->getObject();
		if (!($relationship instanceof \ElggRelationship)) {
			return false;
		}
		
		if ($relationship->relationship !== 'tag_tools:notification') {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Get the unsend tags
	 *
	 * @param \ElggEntity $entity the entity to get for
	 *
	 * @return false|string[]
	 */
	protected static function getUnsendTags(\ElggEntity $entity) {
		
		if (!($entity instanceof \ElggEntity)) {
			return false;
		}
		
		$entity_tags = $entity->tags;
		if (empty($entity_tags)) {
			// shouldn't happen
			return false;
		} elseif (!is_array($entity_tags)) {
			$entity_tags = [$entity_tags];
		}
		
		$sent_tags = $entity->getPrivateSetting('tag_tools:sent_tags');
		if (!empty($sent_tags)) {
			$sent_tags = json_decode($sent_tags, true);
		} else {
			$sent_tags = [];
		}
		
		$sending_tags = array_diff($entity_tags, $sent_tags);
		if (empty($sending_tags)) {
			return false;
		}
		
		return $sending_tags;
	}
}
