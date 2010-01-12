<?php get_header(); ?>

                <!-- Start main Content -->
                <?php if(have_posts()) : ?><?php while(have_posts()) : the_post(); ?>

                <div class="calendar">
					<p class="month"><?php the_time('M') ?></p>
					<p class="date"><?php the_time('j') ?></p>
				</div>

                <div class="post" id="post-<?php the_ID(); ?>">

                    <h1><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h1>
                    <p class="author">By <?php the_author() ?> <?php edit_post_link('Edit', ' &#124; ', '  '); ?></p>

			            <?php the_content(); ?>

                    <div class="links">
                        <div class="comment">
                            <?php comments_popup_link('No Comments', '1 Comment', '% Comments'); ?>
                        </div>
                        <div class="feed">
                            <a href="<?php bloginfo('rss2_url'); ?>">RSS</a>
                        </div>
                        <div class="cat">
                            Filed under: <?php the_category(', ') ?>
                        </div>
						<?php if ( function_exists('the_tags') && get_the_tags() ) {the_tags('<div class="tags">Tags: ', ', ', '.</div> ');} ?>
                    </div> <!-- /links -->

			        <?php trackback_rdf(); ?>

	            </div> <!-- /post -->

                <!-- Next and Previous Post -->
                <div class="navigation">
        			<div class="alignleft"><?php previous_post_link('%link &raquo;') ?></div>
        			<div class="alignright"><?php next_post_link('&laquo; %link') ?></div>
        		</div> <!-- /navigation -->


            	<?php endwhile; ?>

            	<div id="comments-template">
                <?php comments_template(); ?>
	            </div>  <!-- /comments-template -->


	            <?php else : ?>

            	<div class="post">
            		<h1><?php _e('Not Found'); ?></h1>
                    <p class="notfound"><?php _e('Sorry, but you are looking for something that isn&#39;t here.'); ?></p>
            	</div> <!-- /post -->

            	<?php endif; ?>



<?php get_footer(); ?>