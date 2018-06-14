<?php

class uc_posts_viewed_widget extends WP_Widget {

    public function __construct() {
        $widget_options = array(
            'classname' => 'uc-post-viewed',
            'description' => __("Show last post viewed by user", "rng-isuc")
        );
        parent::__construct("uc-post-viewed", __("Last PostViewed", "rng-isuc"), $widget_options);
    }

    /**
     * output widget
     */
    public function widget($args, $instance) {
        wp_enqueue_style("uc-last-post-viewed-widget");
        //$instance = get value from admin panel
        //$args = get structure of widget
        //apply_filters widget_title
        $title = !empty($instance['title']) ? $instance['title'] : "";
        $title = apply_filters("widget_title", $title);
        $post_types = (!empty($instance['post_types']) and isset($instance['post_types'])) ? $instance['post_types'] : array('post');
        $posts_count = (!empty($instance['posts_count'])) ? $instance['posts_count'] : 4;
        $style = (!empty($instance['style'])) ? $instance['style'] : 0;
        $active_post_type = get_option("uc_settings");
        if ($active_post_type == FALSE) {
            $active_post_type = array("post");
        } else {
            $active_post_type = $active_post_type['legal_pt'];
        }

        $output = $args["before_widget"];
        $output .= $args["before_title"];
        $output .= $title;
        $output .= $args["after_title"];
        ob_start();
        $posts_viewed = $_COOKIE['uc_posts_viewed'];
        if (isset($posts_viewed) or count($posts_viewed) !== 0) {
            $posts_viewed = unserialize($posts_viewed);

            $query_args = array(
                'post_type' => $post_types,
                'posts_per_page' => $posts_count,
                'order' => 'DESC',
                'post__in' => $posts_viewed,
            );

            $query = new WP_Query($query_args);
            ?>
            <ul class="uc-post-viewed ja-pp-style-<?php echo $style; ?>">
                <?php
                if ($query->have_posts()):
                    switch ($style):
                        case '0':
                            while ($query->have_posts()):
                                $query->the_post();
                                ?>
                                <li>
                                    <a class="uc-post-viewed-title" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
                                </li>
                                <?php
                            endwhile;
                            break;
                        case '1':
                            while ($query->have_posts()):
                                $query->the_post();
                                $post_id = get_the_ID();
                                $img_thumb = get_the_post_thumbnail($post_id, 'thumbnail', array("class" => "papular-posts-widg-thumbnail"));
                                $block_el = (has_post_thumbnail($post_id)) ? "" : "block-el";
                                ?>
                                <li>
                                    <a class="uc-post-viewed-thumb-wrapper" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php echo $img_thumb; ?></a>
                                    <a class="uc-post-viewed-title-wrapper <?php echo $block_el; ?>" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
                                        <p class="uc-post-viewed-title"><?php the_title(); ?></p>
                                    </a>
                                    <span class="uc-post-viewed-date"><?php the_date(); ?></span>
                                </li>
                                <?php
                            endwhile;
                            break;
                    endswitch;
                endif;
                ?>
            </ul>
            <?php
        }else {
            _e("No Post Was Viewed","rng-isuc");
        }
        $output .= ob_get_clean();
        $output .= $args["after_widget"];
        echo $output;
    }

    /**
     * form admin panel widgt
     */
    public function form($instance) {
        //$instance = get value from admin panel fields
        //$this->get_field_id('FIELDNAME') = avoid id conflict
        //$this->get_field_name('FIELDNAME') = avoid name conflict
        $title = (!empty($instance['title'])) ? $instance['title'] : __("Last post viewed", "rng-isuc");
        $post_types = (!empty($instance['post_types']) and isset($instance['post_types'])) ? $instance['post_types'] : array('post');
        $posts_count = (!empty($instance['posts_count'])) ? $instance['posts_count'] : 4;
        $style = (!empty($instance['style'])) ? $instance['style'] : 0;
        $active_post_type = get_option("uc_settings");
        if ($active_post_type == FALSE) {
            $active_post_type = array("post");
        } else {
            $active_post_type = $active_post_type['legal_pt'];
        }
        $uc_settings = get_option("uc_settings");
        $post_count = $uc_settings['post_count'];
        if (empty($post_count)) {
            $post_count = 10;
        }
        ?>
        <p>
            <label><?php _e("Title", "rng-isuc"); ?></label>
            <input type="text" id="<?php echo $this->get_field_id("title"); ?>" name="<?php echo $this->get_field_name("title"); ?>" style="width: 100%;" name="<?php echo $this->get_field_name("title"); ?>" value="<?php echo $title; ?>">
        </p>
        <p>
            <label><?php _e("Select post types", "rng-isuc"); ?></label>
            <select id="<?php echo $this->get_field_id("post-types") ?>" multiple="" name="<?php echo $this->get_field_name("post_types"); ?>[]" style="width: 100%;">
                <?php
                foreach ($active_post_type as $post_type) {
                    $selected = (in_array($post_type, $post_types)) ? 'selected=""' : '';
                    ?>
                    <option <?php echo $selected; ?> value="<?php echo $post_type; ?>"><?php echo $post_type; ?></option>
                    <?php
                }
                ?>
            </select>
        </p>
        <p>
            <label><?php _e("Posts per page", "rng-isuc"); ?></label>
            <input type="number" id="<?php echo $this->get_field_id('posts-count'); ?>" style="width: 100%;" name="<?php echo $this->get_field_name('posts_count'); ?>" value="<?php echo $posts_count; ?>" max="<?php echo $post_count; ?>" />
        </p>
        <p>
            <label><?php _e("Select style", "rng-isuc"); ?></label>
            <select id="<?php echo $this->get_field_id("style"); ?>" style="width: 100%;" name="<?php echo $this->get_field_name("style") ?>">
                <option <?php echo ($style == 0) ? 'selected=""' : ''; ?> value="0"><?php _e("style1 (simple list)", "rng-isuc"); ?></option>
                <option <?php echo ($style == 1) ? 'selected=""' : ''; ?> value="1"><?php _e("style2 (with thumbnail)", "rng-isuc"); ?></option>
            </select>
        </p>
        <?php
    }

    /**
     * save admin panel fields in $instance
     */
    public function update($new_instance, $old_instance) {
//$old_instance = old instance
//$new_instance = new instance
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        $instance['post_types'] = $new_instance['post_types'];
        $instance['posts_count'] = $new_instance['posts_count'];
        $instance['style'] = $new_instance['style'];
        return $instance;
    }

}

/**
 * register widget main function
 */
function register_uc_posts_viewed_widget() {
    register_widget("uc_posts_viewed_widget");
}

add_action("widgets_init", "register_uc_posts_viewed_widget");
/*
*Constants*
1.*uc-post-viewed
2.*WIDGET_DESCRIPTION
3.uc_posts_viewed_widget
4.WIDGET_TITLE
5.OUTPUT_CONTENT
6.WIDGET_ID
*/

