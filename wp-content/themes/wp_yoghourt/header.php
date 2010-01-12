<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">

<!--
 ____________________________________________________________
|                                                            |
|    DESIGN : Jeeremie { http://web-kreation.com }           |
|      DATE : 2008-01-23                                     |
|     EMAIL : webmaster@web-kreation.com                     |
|   VERSION : 2.1                                            |
|  DOWNLOAD : http://web-kreation.com/index.php/freebies     |
|____________________________________________________________|
-->

	<title><?php bloginfo('name'); ?><?php wp_title(); ?></title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />	
	<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats please -->

	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
	<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
	<link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

	<?php wp_get_archives('type=monthly&format=link'); ?>
	<?php wp_head(); ?>


    <link rel="shortcut icon" href="<?php bloginfo('template_url'); ?>/favicon.ico" />
</head>
<body>
    <div id="container">
        <div id="wrapper">

			<?php get_sidebar(); ?>

			<?php include (TEMPLATEPATH . '/searchform.php'); ?>

            <!-- Start Intro -->
            <div id="content">
                <div id="intro">
                    <?php /* If this is a 404 page */ if (is_404()) { ?>
					<?php /* If this is a category archive */ } elseif (is_category()) { ?>
					<h1>Archive</h1>
					<h2>You are currently browsing the archives for the <?php single_cat_title(''); ?> category.</h2>

					<?php /* If this is a yearly archive */ } elseif (is_day()) { ?>
					<h1>Archive</h1>
					<h2>You are currently browsing the <a href="<?php bloginfo('url'); ?>/"><?php echo bloginfo('name'); ?></a> blog archives
					for the day <?php the_time('l, F jS, Y'); ?>.</h2>

					<?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
					<h1>Archive</h1>
					<h2>You are currently browsing the <a href="<?php bloginfo('url'); ?>/"><?php echo bloginfo('name'); ?></a> blog archives
					for <?php the_time('F, Y'); ?>.</h2>

					<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
					<h1>Archive</h1>
					<h2>You are currently browsing the <a href="<?php bloginfo('url'); ?>/"><?php echo bloginfo('name'); ?></a> blog archives
					for the year <?php the_time('Y'); ?>.</h2>

					<?php /* If this is a monthly archive */ } elseif (is_search()) { ?>
                	<h1>Search Results:</h1>
					<h2>You have searched the <a href="<?php echo bloginfo('url'); ?>/"><?php echo bloginfo('name'); ?></a> blog archives
					for <strong>'<?php the_search_query(); ?>'</strong>. </h2>

					<?php /* If this is a monthly archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
					<h1>Archive</h1>
					<h2>You are currently browsing the <a href="<?php echo bloginfo('url'); ?>/"><?php echo bloginfo('name'); ?></a> blog archives.</h2>

                    <?php /* If this is a monthly archive */ } elseif (is_home() || is_page()|| is_single() ) { ?>
                	<h1><a href="<?php echo get_option('home'); ?>/" title="<?php bloginfo('name'); ?>"><?php bloginfo('name'); ?></a></h1>
                    <h2><?php bloginfo('description'); ?></h2>
					<?php } ?>
                </div> <!-- /intro -->

                <!-- Start main Content -->
