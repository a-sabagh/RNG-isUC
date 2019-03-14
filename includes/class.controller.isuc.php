<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class isuc {

    function __construct() {
        add_shortcode('isuc_posts_viewed', array($this, 'shortcode_posts_viewed'));
        add_action("template_redirect", array($this, "set_post_view"));
        if ($this->check_sidenav_postviewed()) {
            add_action("wp_footer", array($this, "show_sidenav_postviewed"));
        }
    }

    public function get_uc_settings() {
        $uc_settings_array = array(
            'legal_pt' => array('post'),
            'side_nav' => false,
            'post_count' => 10,
            'flag' => true
        );
        $uc_settings = get_option("uc_settings");
        if (empty($uc_settings)) {
            return $uc_settings_array;
        }

        $uc_settings_array['legal_pt'] = (array) $uc_settings['legal_pt'];
        $uc_settings_array['side_nav'] = ((string) $uc_settings['side_nav'] == 'yes') ? true : false;
        $uc_settings_array['post_count'] = (int) $uc_settings['post_count'];
        $uc_settings_array['flag'] = ((string) $uc_settings['flag'] == 'yes') ? true : false;
        return $uc_settings_array;
    }

    public function check_sidenav_postviewed() {
        $uc_settings = $this->get_uc_settings();
        return $uc_settings['side_nav'];
    }

    public function get_legal_post_type() {
        $uc_settings = $this->get_uc_settings();
        return $uc_settings['legal_pt'];
    }

    public function get_post_view_count() {
        $uc_settings = $this->get_uc_settings();
        return $uc_settings['post_count'];
    }

    public function get_post_view_flag() {
        $uc_settings = $this->get_uc_settings();
        return $uc_settings['flag'];
    }

    function is_legal_post_views($post_type) {
        extract($this->get_uc_settings());
        return (in_array($post_type, $legal_pt) and $flag);
    }

    public function get_postviewed_cookie() {
        $posts_viewed = $_COOKIE['uc_posts_viewed'];
        $post_viewed_array = (array) unserialize($posts_viewed);
        $post_viewed_array_integer = array_map("intval", $post_viewed_array);
        $post_viewed_array_unique = array_unique($post_viewed_array_integer);
        return array_filter($post_viewed_array_unique);
    }

    public function remove_sigular_id(&$posts_viewed) {
        $queried_object = get_queried_object();
        $current_id = (int) $queried_object->ID;

        if (!is_singular() or !in_array($current_id, $posts_viewed)) {
            return;
        }

        $index = array_search($current_id, $posts_viewed);
        unset($posts_viewed[$index]);
    }

    public function show_sidenav_postviewed() {
        ob_start();
        wp_enqueue_script("uc-last-post-viewed-sidenav");
        $posts_viewed = $this->get_postviewed_cookie();
        if (empty($posts_viewed)) {
            $params = array('query_args' => '', 'has_posts' => FALSE);
            uc_get_template("sidenav-postviewed.php", $params);
        } else {
            $this->remove_sigular_id($posts_viewed);
            $this->check_post_view_count($posts_viewed);
            $legal_pt = $this->get_legal_post_type();
            $query_args = array(
                'order' => 'DESC',
                'post__in' => $posts_viewed,
                'post_type' => $legal_pt,
            );
            $params = array('query_args' => $query_args, 'has_posts' => TRUE);
            uc_get_template("sidenav-postviewed.php", $params);
        }
        $output = ob_get_clean();
        echo $output;
    }

    public function set_post_view_permissin($post_type) {
        $is_legal_post_views = $this->is_legal_post_views($post_type);
        return (is_singular() and ! is_admin() and ! current_user_can("edit_posts") and $is_legal_post_views);
    }

    function set_post_view() {
        
        global $post;
        $post_id = $post->ID;
        $post_type = $post->post_type;
        $posts_viewed = $this->get_postviewed_cookie();

        if (!$this->set_post_view_permissin($post_type) or in_array($post_id, $posts_viewed)) {
            return;
        }
        
        $cookie_name = 'uc_posts_viewed';
        $this->update_post_views($post_id, $cookie_name, $posts_viewed);
        
    }

    function update_post_views($post_id, $cookie_name, $posts_viewed) {
        
        if (empty($posts_viewed)) {
            $this->remove_cookie($cookie_name);
            setcookie($cookie_name, serialize(array($post_id)), time() + YEAR_IN_SECONDS, "/");
            return;
        }
        
        array_unshift($posts_viewed, $post_id);
        $this->check_post_view_count($posts_viewed);
        

        setcookie($cookie_name, serialize($posts_viewed), time() + YEAR_IN_SECONDS, "/");
    }

    function check_post_view_count(&$posts_viewed) {
        $post_count = $this->get_post_view_count();
        while (count($posts_viewed) > $post_count) {
            array_pop($posts_viewed);
        }
    }

    function remove_cookie($cookie_name) {
        unset($_COOKIE[$cookie_name]);
        setcookie($cookie_name, '', time() - 3600, '/');
    }

    function shortcode_posts_viewed() {
        extract($this->get_uc_settings());
        $posts_viewed = $this->get_postviewed_cookie();
        if (empty($posts_viewed) and ! isset($flag)) {
            return;
        }

        $this->remove_singular_id($posts_viewed);
        $this->check_post_view_count($posts_viewed);
        $posts = get_posts(
                array(
                    'post__in' => $posts_viewed,
                    'post_type' => $legal_pt,
                    'posts_per_page' => $post_count
                )
        );
        ob_start();
        uc_get_template("product-viewed.php", array('posts' => $posts));
        $outpout = ob_get_clean();
        return $outpout;
    }

}

new isuc();
