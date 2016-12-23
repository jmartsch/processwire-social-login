<?php namespace ProcessWire;

$SocialLogin = $this->modules->get("SocialLogin");
if (!$SocialLogin) {
    throw new Wire404Exception('SocialLogin module is not installed');
}

if (isset($_REQUEST['hybridauth'])) :

    // Process the request
    $SocialLogin->HybridAuthProcess();

else:
    if ($user->isLoggedin()) {
        $oauth = unserialize($user->oauth);
        $view->set('oauth', $oauth);

        echo '<h1>' . __('Welcome') . ' ' . $oauth->displayName . '</h1>';

        $SocialLogin->showProfile();

        ?>
        <a class='action' href='<?php echo $config->urls->admin; ?>login/logout/'><?php echo __('Logout'); ?></a><?php

    } else {
        $SocialLogin->showLogin();
    }

    // User login through email/pass
//
    if ($input->post->slogin_submit && $input->post->slogin_email) {
        $email = $sanitizer->email($input->post->slogin_email);
        $emailUser = $users->get("email=$email");
        if ($emailUser->id) {
            $user = $session->login($emailUser->name, $input->post->slogin_pass);
            if ($user) {
                $session->redirect($page->path);
            } else {
                echo __("Login failed!");
            }
        } else {
            echo __("Unrecognized email address");
        }
    }

    if (isset($_REQUEST["provider"])) {
        try {
            echo $SocialLogin->execute($_REQUEST["provider"]);
        } catch (Exception $e) {
            $SocialLogin->showError(sprintf(__('An error occours: %s'), $e->getMessage()));
        }
    }

    $content = ob_get_clean();

//    echo $content;
endif;