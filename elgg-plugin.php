<?php

use ColdTrick\TagTools\Bootstrap;
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
			'class' => TagToolsRule::class,
		],
	],
	'views' => [
		'default' => [
			'jquery/tag-it.js' => $composer_path . 'vendor/bower-asset/tag-it/js/tag-it.js',
		],
	],
	'routes' => [
		'edit:object:tag_tools_rule' => [
			'path' => '/tag_tools/rules/add',
			'resource' => 'tag_tools/rules/add',
			'middleware' => [
				AdminGatekeeper::class,
			],
		],
		'add:object:tag_tools_rule' => [
			'path' => '/tag_tools/rules/edit/{guid}',
			'resource' => 'tag_tools/rules/edit',
			'middleware' => [
				AdminGatekeeper::class,
			],
		],
		'collection:activity:tags' => [
			'path' => '/activity/all',
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
	],
	'actions' => [
		'tag_tools/follow_tag' => [],
		'tag_tools/notifications/edit' => [],
		'tag_tools/rules/edit' => ['access' => 'admin'],
		'tag_tools/rules/delete' => ['access' => 'admin'],
		'tag_tools/suggestions/ignore' => ['access' => 'admin'],
	],
	'widgets' => [
		'follow_tags' => [
			'context' => ['profile', 'dashboard'],
		],
		'tagcloud' => [
			'name' => elgg_echo('tagcloud'),
			'context' => ['profile', 'dashboard', 'index', 'groups'],
			'required_plugin' => 'tagcloud',
		],
	],
];
