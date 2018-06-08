<?php
if (is_array($posts)) {
    echo '<ul class="rng-isuc">';
    foreach ($posts as $p) :
        echo '<li class="item-product"><a href="' . get_the_permalink($p->ID) . '" title="' . $p->post_title . '">' . $p->post_title . '</a></li>';
    endforeach;
    echo '</ul>'; //rng-product-viewed
}