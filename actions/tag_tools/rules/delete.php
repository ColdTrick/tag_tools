<?php

$guid = (int) get_input('guid');

elgg_entity_gatekeeper($guid, 'object', TagToolsRule::SUBTYPE);
/* @var $entity TagToolsRule */
$entity = get_entity($guid);

if (!$entity->canDelete()) {
	return elgg_error_response(elgg_echo('entity:delete:permission_denied'));
}

$title = $entity->getDisplayName();
if ($entity->delete()) {
	return elgg_ok_response('', elgg_echo('entity:delete:success', [$title]));
}

return elgg_error_response(elgg_echo('entity:delete:fail', [$title]));

