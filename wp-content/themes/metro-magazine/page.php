<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Metro_Magazine
 */
$sidebar_layout = metro_magazine_sidebar_layout();

get_header(); ?>

			<?php
			while ( have_posts() ) : the_post();

				get_template_part( 'template-parts/content', 'page' );
				 

				// If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;

			endwhile; // End of the loop.
			?>

		</main><!-- #main -->
	</div><!-- #primary -->

	<?php
 $homepagePosts = new WP_Query(array(
 'posts_per_page' => 20,
 'post_type' => array('beauty', 'fashion', 'music', 'health')
 ));
 while($homepagePosts->have_posts()){
 $homepagePosts->the_post();?>
 <a href="<?php the_permalink();?>">
 <p><?php echo wp_trim_words(get_the_content(),18); ?>
 <li> <?php the_title(); ?> </li>
 <div class= "metabox">
 <p>Posted by <?php
 the_author_posts_link();?></p>
<?php the_time('n.j.y');
get_the_category_list();
?>
 </div>
 
 <div class="generic-content">
	
 </div>
 <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('home.jpg')?>);"></div>
 <?php }
 ?>

<?php 
if( $sidebar_layout == 'right-sidebar' )
wp_list_pages(array(
	'title_li' => NULL ,
	'child_of' => $findChildrenOf,
	'sort_column' => 'menu_order'
	));
get_sidebar(); 
get_footer();
?>
<?php
 // this returns the pages but doesn't output it. If the pages has a parent or
 $testArray = get_pages(array(
 'child_of' => get_the_ID()
 ));
 if($theParent or $testArray){ ?>
 <div class="page-links">
 <h2 class="page-links__title">
 <a href="<?php echo get_permalink($theParent); ?>">
 <?php echo get_the_title($theParent); ?>
 </a>
 </h2>
 <ul class="min-list">
 <?php
 if($theParent){ // if the current page has a parent
 $findChildrenOf = $theParent;
 }
 else{ //viewing a parent page
 $findChildrenOf = get_the_ID();
 }
 wp_list_pages(array(
 'title_li' => NULL ,
 'child_of' => $findChildrenOf,
 ));
 ?>
 </ul>
 </div>
 
 <?php } ?>

 
