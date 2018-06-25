<?php

namespace rng\isuc;

if (!defined('ABSPATH')) {
    exit;
}

class settings {

    public function __construct() {
        if (is_admin()) {
            add_action("admin_menu", array($this, "admin_menu"));
            add_action("admin_init", array($this, "general_settings_init"));
            add_action("admin_notices", array($this, "configure_notices"));
            add_action("admin_init", array($this, "dismiss_configuration"));
            add_filter('plugin_action_links_' . UC_PRU, array($this, 'add_setting_link'));
        }
    }

    public function admin_menu() {
        add_submenu_page("options-general.php", __("isUc Settings", "rng-isuc"), __("isUc", "rng-isuc"), "administrator", "isuc-settings", array($this, "isuc_settings"));
    }

    public function isuc_settings() {
        include UC_ADM . "settings-panel.php";
    }

    public function general_settings_init() {
        register_setting("uc-settings", "uc_settings");
        add_settings_section("uc-settings-top", __("General settings", "rng-isuc"), array($this, "general_settings"), "uc-settings");
        add_settings_field("uc-pv-cookie", __("Is Set user post viewed?", "rng-isuc"), array($this, "general_settings_flag"), "uc-settings", "uc-settings-top", array('id' => 'uc-flag', 'name' => 'flag'));
        add_settings_field("uc-settings-side-view", __("show Side Nav post viewed"), array($this, "general_settings_side_nav"), "uc-settings", "uc-settings-top", array("id" => "uc-side-nav", "name" => "side_nav"));
        add_settings_field("uc-settings-legal-pt", __("Permission", "rng-isuc"), array($this, "general_settings_legal_pt"), "uc-settings", "uc-settings-top", array("id" => "uc-legal-pt", "name" => "legal_pt"));
        add_settings_field("uc-settings-post-count", __("Post Count", "rng-isuc"), array($this, "general_settings_post_count"), "uc-settings", "uc-settings-top", array("id" => "uc-post-count", "name" => "post_count"));
    }

    public function general_settings() {
        _e("General Settings of rng-isuc WP Plugin. At The First Please select Post types.", "rng-isuc");
    }

    public function general_settings_flag($args) {
        $uc_settings = get_option("uc_settings");
        $flag = $uc_settings['flag'];
        ?>
        <select id='<?php echo $args['id']; ?>' name='uc_settings[<?php echo $args['name']; ?>]'>
            <option <?php echo ($flag == 'yes' or empty($flag)) ? "selected" : ""; ?> value='yes'><?php _e("Yes", "rng-isuc"); ?></option>
            <option <?php echo ($flag == 'no') ? "selected" : ""; ?> value='no'><?php _e("No", "rng-isuc"); ?></option>
        </select>
        <?php
    }

    public function general_settings_legal_pt($args) {
        $active_post_type = get_option("uc_settings");
        if ($active_post_type == FALSE) {
            $active_post_type = array("post");
        } else {
            $active_post_type = $active_post_type['legal_pt'];
        }
        $pt_args = array('public' => TRUE);
        $post_types = get_post_types($pt_args, 'names');
        foreach ($post_types as $post_type):
            if (is_array($active_post_type)) {
                $checked = (in_array($post_type, $active_post_type)) ? "checked" : "";
            } else {
                $checked = '';
            }
            ?>
            <label>
                <?php echo $post_type ?>&nbsp;<input id="<?php echo $args['id']; ?>" type="checkbox" name="uc_settings[<?php echo $args['name']; ?>][]" <?php echo $checked; ?> value="<?php echo $post_type; ?>" >
            </label>
            <br>
            <?php
        endforeach;
    }

    public function general_settings_side_nav($args){
        $uc_settings = get_option("uc_settings");
        $flag = $uc_settings['side_nav'];
        ?>
        <select id='<?php echo $args['id']; ?>' name='uc_settings[<?php echo $args['name']; ?>]'>
            <option <?php echo ($flag == 'yes' or empty($flag)) ? "selected" : ""; ?> value='yes'><?php _e("Yes", "rng-isuc"); ?></option>
            <option <?php echo ($flag == 'no') ? "selected" : ""; ?> value='no'><?php _e("No", "rng-isuc"); ?></option>
        </select>
        <?php
    }
    
    public function general_settings_post_count($args) {
        $uc_settings = get_option("uc_settings");
        $post_count = $uc_settings['post_count'];
        if (empty($post_count)) {
            $post_count = 10;
        }
        ?>
        <input type="number" id="<?php echo $args['id']; ?>" name="uc_settings[<?php echo $args['name']; ?>]" value="<?php echo $post_count; ?>" min="1" max="20" >
        <?php
    }

    public function configure_notices() {
        $dismiss = get_option("uc_configration_dissmiss");
        if (!$dismiss) {
            $notice = '<div class="updated"><p>' . __('RNG_isUc is activated, you may need to configure it to work properly.', 'rng-isuc') . ' <a href="' . admin_url('admin.php?page=isuc-settings') . '">' . __('Go to Settings page', 'rng-isuc') . '</a> &ndash; <a href="' . add_query_arg(array('uc_dismiss_notice' => 'true', 'uc_nonce' => wp_create_nonce("uc_dismiss_nonce"))) . '">' . __('Dismiss', 'rng-isuc') . '</a></p></div>';
            echo $notice;
        }
    }

    public function dismiss_configuration() {
        if (isset($_GET['uc_dismiss_notice']) and $_GET['uc_dismiss_notice'] = 'true' and ( isset($_GET['uc_nonce']))) {
            $verify_nonce = wp_verify_nonce($_GET['uc_nonce'], 'uc_dismiss_nonce');
            if ($verify_nonce) {
                update_option("uc_configration_dissmiss", 1);
            }
        } elseif (isset($_GET['page']) and $_GET['page'] == "isuc-settings") {
            update_option("uc_configration_dissmiss", 1);
        }
    }

    public function add_setting_link($links) {
        $mylinks = array(
            '<a href="' . admin_url('options-general.php?page=isuc-settings') . '">' . __("Settings", "rng-isuc") . '</a>',
        );
        return array_merge($links, $mylinks);
    }

}

new settings();

