<?php
/**
 * Called when the plugin gets deactivated
 */

// remove class handler
update_subtype('object', TagToolsRule::SUBTYPE);
