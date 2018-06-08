<?php

namespace rng\isuc;

class init {

    public $version;
    public $style;

    public function __construct($version, $slug) {
        $this->version = $version;
        $this->slug = $slug;
        add_action('plugins_loaded', array($this, 'add_text_domain'));
        $this->load_modules();
    }

    /**
     * add text domain for translate files
     */
    public function add_text_domain() {
        load_plugin_textdomain($this->slug, FALSE, UC_PRT . "/languages");
    }

    /**
     * load modules
     */
    public function load_modules() {
        require_once 'class.controller.settings.php';
        require_once 'class.controller.isuc.php';
    }

}
