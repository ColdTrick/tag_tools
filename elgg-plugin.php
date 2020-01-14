<?php

namespace ColdTrick\TagTools;

use Elgg\Router\Middleware\AdminGatekeeper;
use Elgg\Router\Middleware\Gatekeeper;

$composer_path = '';
if (is_dir(__DIR__ . '/vendor')) {
	$composer_path = __DIR__ . '/';
}

require_once(dirname(__FILE__) . '/lib/functions.php');

return [
	'bootstrap' => Bootstrap::class,
	'entities' => [
		[
			'type' => 'object',
			'subtype' => 'tag_tools_rule',
			'class' => \TagToolsRule::class,
		],
		[
			'type' => 'object',
			'subtype' => 'tag_definition',
			'class' => \TagDefinition::class,
		],
	],
	'settings' => [
		'activity_tab' => 1,
		'transform_hashtag' => 1,
	],
	'views' => [
		'default' => [
			'jquery/tag-it.js' => $composer_path . 'vendor/bower-asset/tag-it/js/tag-it.js',
		],
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
		'collection:activity:tags' => [
			'path' => '/activity/tags',
			'resource' => 'tag_tools/activity',
			'middleware' => [
				Gatekeeper::class,
			],
		],
		'collection:tags:autocomplete' => [
			'path' => '/tags/autocomplete',
			'resource' => 'tag_tools/autocomplete',
			'middleware' => [
				Gatekeeper::class,
			],
		],
		'settings:notification:tags' => [
			'path' => '/notifications/tag/{username?}',
			'resource' => 'tag_tools/notifications',
			'middleware' => [
				Gatekeeper::class,
			],
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
	'actions' => [
		'tag_definition/edit' => ['access' => 'admin'],
		'tag_tools/follow_tag' => [],
		'tag_tools/notifications/edit' => [],
		'tag_tools/rules/edit' => ['access' => 'admin'],
		'tag_tools/suggestions/ignore' => ['access' => 'admin'],
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
	'hooks' => [
		'filter_tabs' => [
			'activity' => [
				__NAMESPACE__ . '\MenuItems::registerActivityTab' => [],
			],
		],
		'get' => [
			'subscriptions' => [
				__NAMESPACE__ . '\Notifications::getSubscribers' => ['priority' => 9999],
			],
		],
		'prepare' => [
			'html' => [
				__NAMESPACE__ . '\HtmlFormatter::replaceHashTags' => [],
			],
			'notification:create:relationship:tag_tools:notification' => [
				__NAMESPACE__ . '\Notifications::prepareMessage' => [],
			],
		],
		'register' => [
			'menu:title' => [
				__NAMESPACE__ . '\MenuItems::registerFollowTag' => [],
				__NAMESPACE__ . '\MenuItems::registerTagDefinition' => [],
			],
			'menu:page' => [
				__NAMESPACE__ . '\MenuItems::registerAdminItems' => [],
				__NAMESPACE__ . '\MenuItems::registerSettingsMenuItem' => [],
			],
		],
		'relationship:url' => [
			'relationship' => [
				__NAMESPACE__ . '\Notifications::getNotificationURL' => [],
			],
		],
		'send:after' => [
			'notifications' => [
				__NAMESPACE__ . '\Notifications::afterCleanup' => [],
			],
		],
		'view_vars' => [
			'output/tag' => [
				__NAMESPACE__ . '\Views::setOutputTagVars' => [],
			],
			'output/tags' => [
				__NAMESPACE__ . '\Views::setOutputTagsVars' => [],
			],
		],
	],
	'events' => [
		'create' => [
			'metadata' => [
				 __NAMESPACE__ . '\Rules::applyRules' => ['priority' => 1],
				 __NAMESPACE__ . '\Enqueue::createMetadata' => [],
			],
		],
		'update:after' => [
			'all' => [
				 __NAMESPACE__ . '\Enqueue::afterEntityUpdate' => [],
			],
		],
	],
];
