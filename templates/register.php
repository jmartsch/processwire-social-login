<?php
/**
 * Template file for Registration
 *
 * This should be copied to /site/templates/ folder if installation script cannot do it
 */


ob_start(); ?>

<div class="content">

	<h1><?php echo $title; ?></h1>

	<?php
		$SocialLogin = $this->modules->get( "SocialLogin" );
		if ( ! $SocialLogin ) {
			throw new Wire404Exception( 'SocialLogin module is not installed' );
		}

		// if user isn't logged in, then we pretend this page doesn't exist
		if ( $user->isLoggedin() ) :
			
			// Maybe redirect to your profile?
			// 
			$SocialLogin->showError( __( 'You are already logged in...' ) ); ?>

			<a class='action' href='<?php echo $config->urls->admin; ?>login/logout/'><?php echo __( 'Logout' ); ?></a>

		<?php
		else :

			$SocialLogin->showRegistration();

		endif;

	?>

</div>

<?php

$content = ob_get_clean();