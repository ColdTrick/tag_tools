<?php

namespace ColdTrick\TagTools\Notifications;

use Elgg\Notifications\NotificationEventHandler;

/**
 * Notification Event Handler for 'relationship' 'tag_tools:notification' 'create' action
 */
class CreateNotificationRelationshipEventHandler extends NotificationEventHandler {

	/**
	 * @var \ElggEntity
	 */
	protected $entity;
	
	/**
	 * @var string[]
	 */
	protected $unsent_tags;
	
	/**
	 * @var int[]|false
	 */
	protected $acl_members;
	
	/**
	 * {@inheritDoc}
	 */
	public static function isConfigurableByUser(): bool {
		return false;
	}
	
	/**
	 * Cleanup some stuff
	 *
	 * @param \Elgg\Hook $hook 'send:after', 'notifications'
	 *
	 * @return void
	 */
	public static function afterCleanup(\Elgg\Hook $hook): void {
		/* @var $handler CreateNotificationRelationshipEventHandler */
		$handler = $hook->getParam('handler');
		$class_name = self::class;
		if (!$handler instanceof $class_name) {
			// not the correct notification event
			return;
		}
		
		$entity = $handler->getNotificationEntity();
		
		// cleanup the relationship
		remove_entity_relationships($entity->guid, 'tag_tools:notification', true);
		
		// save the newly sent tags
		$sending_tags = $handler->getUnsentTagsForEntity();
		if (empty($sending_tags)) {
			return;
		}
		
		tag_tools_add_sent_tags($entity, $sending_tags);
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function getSubscriptions(): array {
		$entity = $this->getNotificationEntity();
		
		$sending_tags = $this->getUnsentTagsForEntity();
		if (empty($sending_tags)) {
			return [];
		}
		
		$tag_subscribers = [];
		
		// get interested users
		$users_batch = elgg_get_entities([
			'type' => 'user',
			'annotation_name_value_pairs' => [
				'name' => 'follow_tag',
				'value' => $sending_tags,
				'case_sensitive' => false,
			],
			'limit' => false,
			'batch' => true,
		]);
		
		/* @var $user \ElggUser */
		foreach ($users_batch as $user) {
			if ($entity->owner_guid === $user->guid) {
				continue;
			}
			
			// check user access
			if (!$this->validateEntityAccess($user)) {
				continue;
			}
			
			// get the notification settings of the user for one of the sending tags
			// this will prevent duplicate notifications,
			foreach ($sending_tags as $tag) {
				if (!tag_tools_is_user_following_tag($tag, $user->guid)) {
					// user is not following this tag, check the next tag
					continue;
				}
				
				$notifiction_settings = tag_tools_get_user_tag_notification_settings($tag, $user->guid);
				if (empty($notifiction_settings)) {
					// no notification settings for this tag
					continue;
				}
				
				if (isset($tag_subscribers[$user->guid])) {
					$tag_subscribers[$user->guid] = array_merge($tag_subscribers[$user->guid], $notifiction_settings);
					$tag_subscribers[$user->guid] = array_unique($tag_subscribers[$user->guid]);
				} else {
					$tag_subscribers[$user->guid] = $notifiction_settings;
				}
			}
		}
		
		return $tag_subscribers;
	}
	
	/**
	 * Get the actual entity for this tag notification
	 *
	 * @return \ElggEntity|null
	 */
	public function getNotificationEntity(): ?\ElggEntity {
		if (isset($this->entity)) {
			return ($this->entity instanceof \ElggEntity) ? $this->entity : null;
		}
		
		/* @var $relationship \ElggRelationship */
		$relationship = $this->event->getObject();
		
		$this->entity = get_entity($relationship->guid_two);
		
		return ($this->entity instanceof \ElggEntity) ? $this->entity : null;
	}
	
	/**
	 * Get the unsent tags of the notification entity
	 *
	 * @return string[]
	 * @internal
	 */
	public function getUnsentTagsForEntity(): array {
		if (isset($this->unsent_tags)) {
			return $this->unsent_tags;
		}
		
		$this->unsent_tags = [];
		
		$entity = $this->getNotificationEntity();
		$entity_tags = $entity->tags;
		
		// Cannot use empty() because it would evaluate
		// the string "0" as an empty value.
		if (is_null($entity_tags)) {
			// shouldn't happen
			return [];
		} elseif (!is_array($entity_tags)) {
			$entity_tags = [$entity_tags];
		}
		
		$sent_tags = $entity->getPrivateSetting('tag_tools:sent_tags');
		if (!empty($sent_tags)) {
			$sent_tags = json_decode($sent_tags, true);
		} else {
			$sent_tags = [];
		}
		
		$this->unsent_tags = array_diff($entity_tags, $sent_tags);
		return $this->unsent_tags;
	}
	
	/**
	 * {@inheritDoc}
	 */
	protected function getNotificationSubject(\ElggUser $recipient, string $method): string {
		$entity = $this->getNotificationEntity();
		$tag = implode(', ', $this->getUnsentTagsForRecipient($recipient, $method));
		
		// is this a new entity of an update on an existing
		$time_diff = (int) $entity->time_updated - (int) $entity->time_created;
		if ($time_diff < 60) {
			return elgg_echo('tag_tools:notification:follow:subject', [$tag], $recipient->getLanguage());
		}
		
		return elgg_echo('tag_tools:notification:follow:update:subject', [$tag], $recipient->getLanguage());
	}
	
	/**
	 * {@inheritDoc}
	 */
	protected function getNotificationSummary(\ElggUser $recipient, string $method): string {
		$entity = $this->getNotificationEntity();
		$tag = implode(', ', $this->getUnsentTagsForRecipient($recipient, $method));
		
		// is this a new entity of an update on an existing
		$time_diff = (int) $entity->time_updated - (int) $entity->time_created;
		if ($time_diff < 60) {
			return elgg_echo('tag_tools:notification:follow:summary', [$tag], $recipient->getLanguage());
		}
		
		return elgg_echo('tag_tools:notification:follow:update:summary', [$tag], $recipient->getLanguage());
	}
	
	/**
	 * {@inheritDoc}
	 */
	protected function getNotificationBody(\ElggUser $recipient, string $method): string {
		$entity = $this->getNotificationEntity();
		$tag = implode(', ', $this->getUnsentTagsForRecipient($recipient, $method));
		
		// is this a new entity of an update on an existing
		$time_diff = (int) $entity->time_updated - (int) $entity->time_created;
		if ($time_diff < 60) {
			return elgg_echo('tag_tools:notification:follow:message', [$tag, $entity->getURL()], $recipient->getLanguage());
		}
		
		return elgg_echo('tag_tools:notification:follow:update:message', [$tag, $entity->getURL()], $recipient->getLanguage());
	}
	
	/**
	 * {@inheritDoc}
	 */
	protected function getNotificationURL(\ElggUser $recipient, string $method): string {
		$entity = $this->getNotificationEntity();
		
		return $entity ? $entity->getUrl() : '';
	}
	
	/**
	 * Filter all the unsent tags for the entity to those for the recipient
	 *
	 * @param \ElggUser $recipient notification recipient
	 * @param string    $method    notification method
	 *
	 * @return string[]
	 */
	protected function getUnsentTagsForRecipient(\ElggUser $recipient, string $method): array {
		$unsent_tags = $this->getUnsentTagsForEntity();
		$result = [];
		
		foreach ($unsent_tags as $tag) {
			if (!tag_tools_is_user_following_tag($tag, $recipient->guid)) {
				// user is not following this tag
				continue;
			}
			
			if (!tag_tools_check_user_tag_notification_method($tag, $method, $recipient->guid)) {
				// user is not following this tag by the current method
				continue;
			}
			
			$result[] = $tag;
		}
		
		return $result;
	}
	
	/**
	 * Validate that the user has access to the notification entity
	 *
	 * @param \ElggUser $user the user to check
	 *
	 * @return bool
	 */
	protected function validateEntityAccess(\ElggUser $user): bool {
		$entity = $this->getNotificationEntity();
		if ($entity->access_id === ACCESS_PRIVATE) {
			return false;
		}
		
		if (!has_access_to_entity($entity, $user)) {
			return false;
		}
		
		// this is needed for admins, since they have access to everything we need to check if
		// the content was shared with an acl (mostly groups) which they are a member of
		if (!isset($this->acl_members)) {
			$this->acl_members = false;
			
			if (get_access_collection($entity->access_id) !== false) {
				// this is an acl
				$this->acl_members = get_members_of_access_collection($entity->access_id, true);
			}
		}
		
		if ($this->acl_members === false) {
			// not an acl
			return true;
		}
		
		return in_array($user->guid, $this->acl_members);
	}
}
