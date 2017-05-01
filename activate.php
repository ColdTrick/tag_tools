<?php
/**
 * Called when the plugin gets activated
 */

// set the correct class handler for the rules
if (get_subtype_id('object', TagToolsRule::SUBTYPE)) {
	update_subtype('object', TagToolsRule::SUBTYPE, TagToolsRule::class);
} else {
	add_subtype('object', TagToolsRule::SUBTYPE, TagToolsRule::class);
}
