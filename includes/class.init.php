<?php

namespace rng\isuc;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class init {

    public $version;
    public $slug;

    public function __construct($version, $slug) {
        $this->version = $version;
        $this->slug = $slug;
        add_action('plugins_loaded', array($this, 'add_text_domain'));
        add_action('wp_enqueue_scripts', array($this, 'public_enqueue_scripts'));
        $this->load_modules();
    }

    /**
     * add text domain for translate files
     */
    public function add_text_domain() {
        load_plugin_textdomain($this->slug, FALSE, UC_PRT . "/languages");
    }

    public function public_enqueue_scripts() {
        wp_enqueue_style("uc-last-post-viewed", UC_PDU . "assets/css/style.css");
        if(isuc::check_sidenav_postviewed()){
            wp_enqueue_script("uc-last-post-viewed-sidenav", UC_PDU . "assets/js/script.js", array('jquery'), $this->version, TRUE);
        }
    }

    /**
     * load modules
     */
    public function load_modules() {
        require_once 'class.controller.settings.php';
        require_once 'class.controller.isuc.php';
        require_once 'widgets/init.php';
    }

}
