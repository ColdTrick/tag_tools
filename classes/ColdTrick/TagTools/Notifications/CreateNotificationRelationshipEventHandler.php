<?php

namespace ColdTrick\TagTools\Notifications;

use Elgg\Notifications\NotificationEventHandler;

/**
 * Notification Event Handler for 'relationship' 'tag_tools:notification' 'create' action
 */
class CreateNotificationRelationshipEventHandler extends NotificationEventHandler {

	/**
	 * {@inheritDoc}
	 */
	public static function isConfigurableByUser(): bool {
		return false;
	}
}
