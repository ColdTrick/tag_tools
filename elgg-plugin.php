<?php

use ColdTrick\TagTools\Bootstrap;

require_once(dirname(__FILE__) . '/lib/functions.php');

return [
	'bootstrap' => Bootstrap::class,
	'entities' => [
		[
			'type' => 'object',
			'subtype' => TagToolsRule::SUBTYPE,
			'class' => TagToolsRule::class,
		],
	],
	'actions' => [
		'tag_tools/follow_tag' => [],
		'tag_tools/notifications/edit' => [],
		'tag_tools/upgrades/set_tag_notifications_sent' => ['access' => 'admin'],
		'tag_tools/rules/edit' => ['access' => 'admin'],
		'tag_tools/rules/delete' => ['access' => 'admin'],
		'tag_tools/suggestions/ignore' => ['access' => 'admin'],
	],
];
