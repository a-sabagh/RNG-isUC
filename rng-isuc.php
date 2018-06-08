<?php

/*
  Plugin Name: RNG_isUc
  Description: wordpress plugin for showing last post views
  Version: 1.0
  Author: abolfazl sabagh
  Author URI: http://asabagh.ir
  License: GPLv2 or later
  Text Domain: rng-isuc
 */

namespace rng\isuc;

define(FILE, __FILE__);
define(PRU, plugin_basename(__FILE__));
define(PDU, plugin_dir_url(__FILE__));   //http://localhost:8888/rng-plugin/wp-content/plugins/rng-isuc/
define(PRT, basename(__DIR__));          //rng-isuc.php
define(PDP, plugin_dir_path(__FILE__));  //Applications/MAMP/htdocs/rng-plugin/wp-content/plugins/rng-isuc
define(TMP, PDP . "/public/");           //view OR templates directory for public 
define(ADM, PDP . "/admin/");            //view OR templates directory for admin panel

/*
 * locate_template
 */
if(!function_exists("uc_locate_template")){
	function uc_locate_template($template_name, $template_path, $default_template) {
		if (!$template_path)
			$template_path = "rng-isuc/";
		if (!$default_path)
			$default_path = PDP . "templates/";
		$template = locate_template(array($template_path . $template_name, $template_name));
		if (empty($template))
			$template = $default_path . $template_name;
		return apply_filters("uc_locate_template", $template, $template_name, $template_path, $default_path);
	}
}


/*
 * get_template
 */
if(!function_exists("uc_get_template")){
	function uc_get_template($template_name, $args="", $template_path = "", $default_path = "") {
		if (is_array($args) and isset($args))
			extract($args);
		$template_file = uc_locate_template($template_name, $template_path, $default_path);
		if (!file_exists($template_file)):
			error_log("File with name of {$template_file} is not exist");
			return;
		endif;
		include $template_file;
	}
}


require_once 'includes/class.init.php';
new init(1.0, "rng-isuc");

