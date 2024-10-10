<?php

use ColdTrick\TagTools\Forms\PrepareFields;
use ColdTrick\TagTools\Notifications\CreateNotificationRelationshipEventHandler;
use Elgg\Router\Middleware\AdminGatekeeper;
use Elgg\Router\Middleware\Gatekeeper;
use Elgg\Router\Middleware\UserPageOwnerCanEditGatekeeper;

require_once(dirname(__FILE__) . '/lib/functions.php');

return [
	'plugin' => [
		'version' => '12.0.1',
		'dependencies' => [
			'tagcloud' => [
				'position' => 'after',
				'must_be_active' => false,
			],
		],
	],
	'entities' => [
		[
			'type' => 'object',
			'subtype' => 'tag_tools_rule',
			'class' => \TagToolsRule::class,
			'capabilities' => [
				'commentable' => false,
			],
		],
		[
			'type' => 'object',
			'subtype' => 'tag_definition',
			'class' => \TagDefinition::class,
			'capabilities' => [
				'commentable' => false,
			],
		],
	],
	'settings' => [
		'transform_hashtag' => 1,
		'whitelist' => 1,
		'separate_notifications' => 1,
	],
	'actions' => [
		'tag_definition/edit' => ['access' => 'admin'],
		'tag_tools/follow_tag' => [],
		'tag_tools/followers/export' => ['access' => 'admin'],
		'tag_tools/notifications/edit' => [],
		'tag_tools/rules/edit' => ['access' => 'admin'],
		'tag_tools/suggestions/ignore' => ['access' => 'admin'],
	],
	'routes' => [
		'add:object:tag_tools_rule' => [
			'path' => '/tag_tools/rules/add',
			'resource' => 'tag_tools/rules/add',
			'middleware' => [
				AdminGatekeeper::class,
			],
		],
		'edit:object:tag_tools_rule' => [
			'path' => '/tag_tools/rules/edit/{guid}',
			'resource' => 'tag_tools/rules/edit',
			'middleware' => [
				AdminGatekeeper::class,
			],
		],
		'settings:notification:tags' => [
			'path' => '/notifications/tag/{username}',
			'resource' => 'tag_tools/notifications',
			'middleware' => [
				Gatekeeper::class,
				UserPageOwnerCanEditGatekeeper::class,
			],
			'detect_page_owner' => true,
		],
		'collection:tag' => [
			'path' => 'tag/{tag}',
			'resource' => 'tag/view',
		],
		'add:object:tag_definition' => [
			'path' => 'tag_definition/add/{tag}',
			'resource' => 'tag_definition/add',
			'middleware' => [
				AdminGatekeeper::class,
			],
		],
		'edit:object:tag_definition' => [
			'path' => 'tag_definition/edit/{guid}',
			'resource' => 'tag_definition/edit',
			'middleware' => [
				AdminGatekeeper::class,
			],
		],
		'view:object:tag_definition' => [
			'path' => 'tag_definition/view/{guid}/{title?}',
			'controller' => [\TagDefinition::class, 'forwarder'],
		],
	],
	'events' => [
		'create' => [
			'metadata' => [
				'\ColdTrick\TagTools\Rules::applyRules' => ['priority' => 1],
				'\ColdTrick\TagTools\Enqueue::createMetadata' => [],
			],
		],
		'cron' => [
			'daily' => [
				'\ColdTrick\TagTools\Views::resetTagsWhitelist' => [],
			],
		],
		'form:prepare:fields' => [
			'tag_tools/rules/edit' => [
				PrepareFields::class => [],
			],
		],
		'get' => [
			'subscriptions' => [
				'\ColdTrick\TagTools\Notifications\ExtendedContentNotification::getSubscribers' => [],
			],
		],
		'prepare' => [
			'html' => [
				'\ColdTrick\TagTools\HtmlFormatter::replaceHashTags' => [],
			],
			'notification' => [
				'\ColdTrick\TagTools\Notifications\ExtendedContentNotification::extendNotificationBody' => ['priority' => 999],
			],
		],
		'register' => [
			'menu:title' => [
				'\ColdTrick\TagTools\Menus\Title::registerFollowTag' => [],
				'\ColdTrick\TagTools\Menus\Title::registerTagDefinition' => [],
			],
			'menu:admin_header' => [
				'\ColdTrick\TagTools\Menus\AdminHeader::register' => [],
			],
			'menu:filter:settings/notifications' => [
				'\ColdTrick\TagTools\Menus\Filter::registerNotificationSettings' => [],
			],
		],
		'send:after' => [
			'notifications' => [
				'\ColdTrick\TagTools\Notifications\CreateNotificationRelationshipEventHandler::afterCleanup' => [],
			],
		],
		'update:after' => [
			'all' => [
				'\ColdTrick\TagTools\Enqueue::afterEntityUpdate' => [],
			],
		],
		'view_vars' => [
			'input/tags' => [
				'\ColdTrick\TagTools\Views::setInputTagsWhitelist' => [],
			],
			'output/tag' => [
				'\ColdTrick\TagTools\Views::setOutputTagVars' => [],
			],
			'output/tags' => [
				'\ColdTrick\TagTools\Views::setOutputTagsVars' => [],
			],
		],
	],
	'notifications' => [
		'relationship' => [
			'tag_tools:notification' => [
				'create' => CreateNotificationRelationshipEventHandler::class,
			],
		],
	],
	'view_extensions' => [
		'elgg.css' => [
			'tag_tools/site.css' => [],
		],
		'admin.css' => [
			'tag_tools/site.css' => [],
		],
		'tag_tools/tag/content' => [
			'tag_tools/tag/content/recent_content' => ['priority' => 100],
			'tag_tools/tag/content/groups' => ['priority' => 200],
			'tag_tools/tag/content/users' => ['priority' => 300],
			'tag_tools/tag/content/related_tags' => ['priority' => 400],
		],
	],
	'view_options' => [
		'tag_tools/tag/view' => ['ajax' => true],
		'tag_tools/tagcolors.css' => ['simplecache' => true],
	],
	'widgets' => [
		'follow_tags' => [
			'context' => ['profile', 'dashboard'],
		],
		'tagcloud' => [
			'context' => ['profile', 'dashboard', 'index', 'groups'],
			'required_plugin' => 'tagcloud',
		],
	],
];
