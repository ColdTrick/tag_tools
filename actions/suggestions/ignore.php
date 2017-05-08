<?php

$from_tag = get_input('from_tag');
$ignores = get_input('ignores');

$ignore_config = elgg_get_plugin_setting('ignored_suggestions', 'tag_tools');

if (empty($ignore_config)) {
	$ignore_config = [];
} else {
	$ignore_config = json_decode($ignore_config, true);
}

$current_ignores = (array) elgg_extract($from_tag, $ignore_config, []);
$current_ignores = array_merge($current_ignores, $ignores);

$ignore_config[$from_tag] = array_unique($current_ignores);

$setting_value = json_encode($ignore_config);

elgg_set_plugin_setting('ignored_suggestions', $setting_value, 'tag_tools');
