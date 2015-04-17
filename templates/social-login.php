<?php
/**
 * Template file for SocialLogin
 *
 * This should be copied to /site/templates/ folder if installation script cannot do it
 * 
 * @see https://processwire.com/talk/topic/107-custom-login/
 */

$SocialLogin = $this->modules->get( "SocialLogin" );
if ( ! $SocialLogin ) {
    throw new Wire404Exception( 'SocialLogin module is not installed' );
}

if ( isset( $_REQUEST['hybridauth'] ) ) :

    // Process the request
    $SocialLogin->HybridAuthProcess();

else:

ob_start();
?>

<div class="content">

    <?php
        if ( $user->isLoggedin() ) {
            echo '<h1>'. __( 'Welcome' ) . ' ' . $user->name .'</h1>';

            echo "<p>Da questa pagina puoi gestire il tuo profilo e fare tante altre cose</p>";

            echo "<br><hr><br>";

            $SocialLogin->showProfile();

            ?><a class='action' href='<?php echo $config->urls->admin; ?>login/logout/'><?php echo __( 'Logout' ); ?></a><?php

        }
        else {
            echo '<h1>'.$title.'</h1>';

            $SocialLogin->showLogin();
        }
    ?>

</div>

<?php

// User login through email/pass
// 
if ( $input->post->slogin_submit && $input->post->slogin_email ) {
    $email = $sanitizer->email( $input->post->slogin_email );
    $emailUser = $users->get( "email=$email" );
    if ( $emailUser->id ) {
        $user = $session->login( $emailUser->name, $input->post->slogin_pass );
        if ( $user ) {
            $session->redirect( $page->path );
        }
        else {
            echo __( "Login failed!" );
        }
    }
    else {
        echo __( "Unrecognized email address" );
    }
}

if ( isset( $_REQUEST["provider"] ) ) {
    try {
        echo $SocialLogin->execute( $_REQUEST["provider"] );
    }
    catch ( Exception $e ) {
        $SocialLogin->showError( sprintf( __( 'An error occours: %s' ), $e->getMessage() ) );
    }
}

$content = ob_get_clean();

endif;