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
			<div class="logo">A</div>
			<span>ACCESS<small>LAW FIRM</small></span>
		</a>
		<nav class="navlinks" aria-label="Primary">
			<a href="#home">Home</a>
			<a href="#about">About</a>
			<a href="#practice">Practice Areas</a>
			<a href="#faq">FAQ</a>
			<button class="btn btn-primary open-lobby" type="button">Join Virtual Lobby</button>
		</nav>
		<?php $alf_lobby_open = alf_is_lobby_open(); ?>
		<div id="alfHeaderStatus" class="status<?php echo $alf_lobby_open ? '' : ' status-closed'; ?>" data-lobby-status>
			<span class="dot" aria-hidden="true"></span>
			<span data-lobby-status-label><?php echo $alf_lobby_open ? esc_html__( 'Virtual Lobby Open', 'access-law-firm' ) : esc_html__( 'Virtual Lobby Closed', 'access-law-firm' ); ?></span>
		</div>
	</div>
</header>
