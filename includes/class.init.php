<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class init {

    public $version;
    public $slug;

    public function __construct($version, $slug) {
        $this->version = $version;
        $this->slug = $slug;
        $this->load_modules();
        add_action('plugins_loaded', array($this, 'add_text_domain'));
        add_action('wp_enqueue_scripts', array($this, 'public_enqueue_scripts'));
    }

    /**
     * add text domain for translate files
     */
    public function add_text_domain() {
        load_plugin_textdomain($this->slug, FALSE, RNGUC_PRT . "/languages");
    }

    public function public_enqueue_scripts() {
        wp_enqueue_style("uc-last-post-viewed", RNGUC_PDU . "assets/css/style.css");
        wp_register_script("uc-last-post-viewed-sidenav", RNGUC_PDU . "assets/js/script.js", array('jquery'), $this->version, TRUE);
    }

    /**
     * load modules
     */
    public function load_modules() {
        require_once 'class.controller.settings.php';
        require_once 'class.controller.isuc.php';
        require_once 'widgets/last-post-viewed.php';
    }

}
