<?php

$from_tag = get_input('from_tag');
$ignores = (array) get_input('ignores', []);

$ignore_config = elgg_get_plugin_setting('ignored_suggestions', 'tag_tools');

if (empty($ignore_config)) {
	$ignore_config = [];
} else {
	$ignore_config = json_decode($ignore_config, true);
}

$current_ignores = (array) elgg_extract($from_tag, $ignore_config, []);
$current_ignores = array_merge($current_ignores, $ignores);

$ignore_config[$from_tag] = array_unique($current_ignores);

$plugin = elgg_get_plugin_from_id('tag_tools');
$plugin->setSetting('ignored_suggestions', json_encode($ignore_config));

return elgg_ok_response();
