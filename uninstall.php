<?php
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;
//delte options
$options = array(
    //settings
    "uc_settings",
    "uc_configration_dissmiss",
    //widget
    "widget_uc-post-viewed"
);
foreach ($options as $option) {
    if (get_option($option)) {
        delete_option($option);
    }
}