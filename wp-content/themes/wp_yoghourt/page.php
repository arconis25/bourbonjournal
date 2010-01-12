<?php get_header(); ?>             

                <!-- Start main Content -->
                <h1><?php the_title(); ?><span><?php edit_post_link('Edit Page', '[ ', ' ]'); ?></span></h1>

                <div id="post">
                <?php if(have_posts()) : ?><?php while(have_posts()) : the_post(); ?>
                    <div class="post" id="post-<?php the_ID(); ?>">

			        <?php the_content(); ?>
		        	<?php link_pages('<p><strong>Pages:</strong> ', '</p>', 'number'); ?>


    			<!-- Trackback -->
    			<?php trackback_rdf(); ?>

	            </div> <!-- /post -->


            	<?php endwhile; ?>

            	<?php else : ?>

            	<div class="post">
            		<h2><?php _e('Not Found'); ?></h2>
            		<div class="entry">
            <p class="notfound"><?php _e('Sorry, but you are looking for something that isn&#39;t here.'); ?></p>
            		</div>
            	</div> <!-- /post -->

	            <?php endif; ?>

                </div>

<?php get_footer(); ?>