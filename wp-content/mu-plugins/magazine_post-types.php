<?php 
function magazine_post_types(){
    register_post_type('beauty',array(
        'supports' => array('title', 'editor','excerpt','custom-fields','thumbnail','comments'),
	'capability_type' => 'beauty',
    'map_meta_cap'=> true,
    'rewrite'=> array('slug' => 'beauty' ),
'has_archive' => true,
 'public' => true,
 'labels' => array(
 'name' => "Beauty",
 'add_new_item' => 'Add New Beauty Post',
 'edit_item' => 'Edit Beauty Post',
 'all_items' => 'All Beauty Posts',
 'singular_name' => "Beauty"
 ),
 'menu_icon' => 'dashicons-products'
));

register_post_type('health',array(
'supports' => array('title', 'editor','excerpt','custom-fields','thumbnail','comments'),
'capability_type' => 'health',
'rewrite'=> array('slug' => 'health' ),
'has_archive' => true,
'map_meta_cap'=> true,
    'public' => true,
    'labels' => array(
    'name' => "Health",
    'add_new_item' => 'Add New Health Post',
    'edit_item' => 'Edit Health Post',
    'all_items' => 'All Health Posts',
    'singular_name' => "Health"
    ),
    'menu_icon' => 'dashicons-heart'
   ));

   register_post_type('digital',array(
    'supports' => array('title', 'editor','excerpt','custom-fields','thumbnail','comments'),
    'map_meta_cap'=> true,
   'capability_type' => 'digital',
   'rewrite'=> array('slug' => 'digital' ),
'has_archive' => true,
    'public' => true,
    'labels' => array(
    'name' => "Digital World",
    'add_new_item' => 'Add Digital World Post',
    'edit_item' => 'Edit Digital Post',
    'all_items' => 'All Digital World Posts',
    'singular_name' => "Digital"
    ),
    'menu_icon' => 'dashicons-money-alt'
   ));

   register_post_type('fashion',array(
    'supports' => array('title', 'editor','excerpt','custom-fields','thumbnail','comments'),
    'map_meta_cap'=> true,
   'capability_type' => 'fashion',
   'rewrite'=> array('slug' => 'fashion' ),
'has_archive' => true,
    'public' => true,
    'labels' => array(
    'name' => "Fashion",
    'add_new_item' => 'Add New Fashion Post',
    'edit_item' => 'Edit Fashion Post',
    'all_items' => 'All Fashion Posts',
    'singular_name' => "Fashion"
    ),
    'menu_icon' => 'dashicons-businessperson'
   ));

   register_post_type('music',array(
    'supports' => array('title', 'editor','excerpt','custom-fields','thumbnail','comments'),
    'map_meta_cap'=> true,
   'capability_type' => 'music',
   'rewrite'=> array('slug' => 'music' ),
'has_archive' => true,
    'public' => true,
    'labels' => array(
    'name' => "Music",
    'add_new_item' => 'Add New Music Post',
    'edit_item' => 'Edit Music Post',
    'all_items' => 'All Music Posts',
    'singular_name' => "Music"
    ),
    'menu_icon' => 'dashicons-format-audio'
   ));

}
add_action('init', 'magazine_post_types');


?>