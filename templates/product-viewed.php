<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (is_array($posts)) {
    ?>
    <ul class="rng-isuc ucrow">
        <?php
        $i = 1;
        foreach ($posts as $p) :
            $post_thumbnail = get_the_post_thumbnail($p->ID,'post-thumbnail',array('class'=>'isuc-thumbnail') );
            $date_format = get_option('date_format');
            ?>
            <li class="item-product uccol-sm-3">
                <a href="<?php echo get_the_permalink($p->ID); ?>" title="<?php echo $p->post_title; ?>">
                    <?php echo $post_thumbnail; ?>
                    <h4><?php echo $p->post_title; ?></h4>
                    <span class="post-date"><?php  ?></span>
                </a>
            </li>
            <?php
            if($i%4==0)
                echo '<li class="clear"></li>';
            $i++;
        endforeach;
        ?>
    </ul>
    <?php
}