<?php get_header(); ?>

<?php get_sidebar(); ?>

<?php include (TEMPLATEPATH . '/searchform.php'); ?>

<!-- Start main content -->
            <div id="content">
                <div id="intro">
                    <div class="intro_t">
                        <h1>404 Error Page</h1>
                    </div>
                </div> <!-- /intro -->

                <!-- Start main Content -->
            	<div class="post">
            		<h1><?php _e('Not Found'); ?></h1>
                    <p class="notfound"><?php _e('Sorry, but you are looking for something that isn&#39;t here.'); ?></p>
            	</div>


<?php get_footer(); ?>