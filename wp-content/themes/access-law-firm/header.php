<?php
/**
 * Theme header.
 *
 * @package Access_Law_Firm
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
	<div class="container nav">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>#home">
			<div class="logo" aria-hidden="true">
				<img src="<?php echo alf_img( 'brand-mark.png' ); ?>" alt="" width="46" height="46" decoding="async">
			</div>
			<span>ACCESS<small>LAW FIRM</small></span>
		</a>
		<button class="nav-toggle" type="button" aria-label="Open menu" aria-expanded="false" aria-controls="primaryNav">
			<span></span><span></span><span></span>
		</button>
		<nav class="navlinks" id="primaryNav" aria-label="Primary">
			<a href="#home">Home</a>
			<a href="#about">About</a>
			<a href="#practice">Practice Areas</a>
			<a href="#faq">FAQ</a>
			<button class="btn btn-primary open-lobby" type="button">Join Virtual Lobby</button>
		</nav>
		<?php $alf_lobby_open = alf_is_lobby_open(); ?>
		<div id="alfHeaderStatus" class="status<?php echo $alf_lobby_open ? '' : ' status-closed'; ?>" data-lobby-status>
			<span class="dot" aria-hidden="true"></span>
			<span class="status-copy">
				<span data-lobby-status-label><?php echo $alf_lobby_open ? esc_html__( 'Virtual Lobby Open', 'access-law-firm' ) : esc_html__( 'Virtual Lobby Closed', 'access-law-firm' ); ?></span>
				<small class="status-hours">Mon–Fri 9:00 AM–5:00 PM · Sat–Sun 10:00 AM–3:30 PM CST</small>
			</span>
		</div>
	</div>
	<div class="header-accent" aria-hidden="true"></div>
</header>
