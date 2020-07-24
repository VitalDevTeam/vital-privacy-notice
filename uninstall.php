<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
	die();
}

foreach (wp_load_alloptions() as $option => $value) {

	if (strpos($option, 'vtlprvmsg_') === 0) {
		delete_option($option);
	}
}
