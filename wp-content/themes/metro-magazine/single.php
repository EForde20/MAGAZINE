<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Metro_Magazine
 */

get_header(); ?>
<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">

	<?php
	while ( have_posts() ) : the_post();

		get_template_part( 'template-parts/content', get_post_format() );
		echo get_the_category_list(', '); 

	   /**
        * 
        * @hooked metro_magazine_post_author;
        * 
        **/
       do_action( 'metro_magazine_author_info_box' ); ;
        
       the_post_navigation();

		// If comments are open or we have at least one comment, load up the comment template.
		if ( comments_open() || get_comments_number() ) :
			comments_template();
		endif;

	endwhile; // End of the loop.
	?>
	

	</main><!-- #main -->
</div><!-- #primary -->
<?php
 $theParent = wp_get_post_parent_ID(get_the_ID());
 if($theParent){ ?>
<div class="metabox metabox--position-up metabox--with-home-link">
 <p><a class="metabox__blog-home-link" href="<?php echo
get_permalink($theParent); ?>">
 <i class="fa fa-home" aria-hidden="true"></i> Back to <?php
echo get_the_title($theParent); ?></a>
 <span class="metabox__main"><?php echo the_title(); ?>
 
</span></p>
 </div>
 <?php }

 ?>

<?php get_sidebar();
get_footer();

