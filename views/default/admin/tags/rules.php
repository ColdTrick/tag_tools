<?php

echo elgg_list_entities([
	'type' => 'object',
	'subtype' => \TagToolsRule::SUBTYPE,
	'no_results' => elgg_echo('notfound'),
]);
