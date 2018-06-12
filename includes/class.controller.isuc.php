<?php

namespace rng\isuc;
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
class isuc {

    function __construct() {
        add_shortcode('isuc_posts_viewed', array($this, 'shortcode_posts_viewed'));
        add_action("template_redirect", array($this, "set_post_view"));
        add_action('add_meta_boxes', array($this, 'metabox_init'));
        add_action('save_post', array($this, 'metabox_save'));
    }

    function set_post_view() {
        if (! is_admin()) {
            global $post;
            $post_id = $post->ID;
            $post_type = $post->post_type;
            $args = array(
                'post_type' => $post_type
            );
            $is_legal_post_views = $this->is_legal_post_views($args);
            if ($is_legal_post_views and ! current_user_can("edit_posts")) {
                $cookie_name = 'uc_posts_viewed';
                $this->update_post_views($post_id, $cookie_name);
            }
        }
    }

    function is_legal_post_views($args) {
        $uc_settings = get_option("uc_settings");
		$flag = $uc_settings['flag'];
		$flag = (empty($flag) || $flag == 'yes')? TRUE : FALSE;
		$legal_pt = $uc_settings['legal_pt'];
		if(in_array($args['post_type'],$legal_pt) and $flag){
			return TRUE;
		}else{
			return FALSE;
		}
    }

    function update_post_views($post_id, $cookie_name) {
        $product_view = $_COOKIE[$cookie_name];
        if (isset($product_view) and !empty($product_view)) {
            $product_view = unserialize($product_view);
            if (is_array($product_view) and ! in_array($post_id, $product_view)) {
                $product_view = $this->check_product_view_count($product_view);
                array_unshift($product_view, $post_id);
                $product_view = serialize($product_view);
                setcookie($cookie_name, $product_view, time() + YEAR_IN_SECONDS, "/");
            }
        } else {
            $this->remove_cookie($cookie_name);
            $product_view = serialize(array($post_id));
            setcookie($cookie_name, $product_view, time() + YEAR_IN_SECONDS, "/");
        }
    }

    function check_product_view_count($product_view) {
		$uc_settings = get_option("uc_settings");
		$post_count = $uc_settings['post_count'];
		if(empty($post_count)){
			$post_count = 10;
		}
        if (count($product_view) > $post_count) {
            while (count($product_view) > $post_count) {
                array_pop($product_view);
            }
        }
        return $product_view;
    }

    function remove_cookie($cookie_name) {
        unset($_COOKIE[$cookie_name]);
        setcookie($cookie_name, '', time() - 3600, '/');
    }

    function shortcode_posts_viewed() {
        ob_start();
        // The Query
        $product_view = $_COOKIE['uc_posts_viewed'];
        if (isset($product_view) or count($product_view) !== 0) {
            $product_view = unserialize($product_view);
            if (is_array($product_view)) {
                global $post;
                $current_post_id = $post->ID;
                if (in_array($current_post_id, $product_view)) {
                    $index = array_search($current_post_id, $product_view);
                    unset($product_view[$index]);
                }
            }
        }
        if (isset($product_view) and count($product_view) !== 0) {
			$uc_settings = get_option("uc_settings");
			$post_count = $uc_settings['post_count'];
			$active_post_type = get_option("uc_settings");
			if ($active_post_type == FALSE) {
				$active_post_type = array("post");
			} else {
				$active_post_type = $active_post_type['legal_pt'];
			}
			if(empty($post_count)){
				$post_count = 10;
			}
            $args = array('post__in' => $product_view, 'post_type' => $active_post_type, 'posts_per_page' => $post_count);
            $posts = get_posts($args);
			$template_args = array('posts' => $posts);
			uc_get_template("product-viewed.php",$template_args);
        }
        $outpout = ob_get_clean();
        return $outpout;
    }

}

new isuc();


