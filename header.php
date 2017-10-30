<?php

// adjustment (send the user to the "/" page again if not there)
global $wp;
$site_url = get_site_url();
$current_link = home_url( $wp->request );
$permalink_diff = str_replace($site_url, "", $current_link);
//var_dump($permalink_diff);
if( $permalink_diff != "/" && $permalink_diff != "" ) {
    $permalink_diff = (substr($permalink_diff, 0, 1) == "/") ? substr($permalink_diff, 1) : $permalink_diff;
//    $permalink_diff = (substr($permalink_diff, -1) == "/") ? substr($permalink_diff, 1) : $permalink_diff;
    echo "<script>window.location.href = '/#/' + '" . $permalink_diff . "';</script>";
    exit;
}

?>

<!doctype html>
<html <?php language_attributes(); ?> class="no-js">
	<head>
		<meta charset="<?php bloginfo('charset'); ?>">
		<title><?php wp_title(''); ?><?php if(wp_title('', false)) { echo ' :'; } ?> <?php bloginfo('name'); ?></title>

		<link href="//www.google-analytics.com" rel="dns-prefetch">
        <link href="<?php echo get_template_directory_uri(); ?>/img/icons/favicon.ico" rel="shortcut icon">
        <link href="<?php echo get_template_directory_uri(); ?>/img/icons/touch.png" rel="apple-touch-icon-precomposed">

		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="<?php bloginfo('description'); ?>">

		<?php wp_head(); ?>
		<script>
        // conditionizr.com
        // configure environment tests
        conditionizr.config({
            assets: '<?php echo get_template_directory_uri(); ?>',
            tests: {}
        });
        </script>

        <script>
            var bloginfo = [];
            bloginfo.url = '<?php bloginfo('url'); ?>';
        </script>

	</head>
	<body <?php body_class(); ?>>
		<!-- wrapper -->
		<div class="wrapper">

			<!-- header -->
			<header class="header clear" role="banner">

					<!-- logo -->
					<?php /* <div class="logo">
						<a href="<?php echo home_url(); ?>">
							<!-- svg logo - toddmotto.com/mastering-svg-use-for-a-retina-web-fallbacks-with-png-script -->
							<img src="<?php echo get_template_directory_uri(); ?>/img/logo.svg" alt="Logo" class="logo-img">
						</a>
					</div> */ ?>
					<!-- /logo -->

					<!-- nav -->
					<nav id="nav" class="nav clear" role="navigation">
						<?php
                        // this menu will answer to wt-nav.js component
                        wordstree_nav();
                        ?>
					</nav>
					<!-- /nav -->

			</header>
			<!-- /header -->
