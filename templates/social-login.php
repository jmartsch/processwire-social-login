<?php namespace ProcessWire;

/**
 * Template file for SocialLogin
 *
 * This should be copied to /site/templates/ folder if installation script cannot do it
 *
 * @see https://processwire.com/talk/topic/107-custom-login/
 */

// you can add your own markup and classes to support frameworks like Bootstrap, Foundation, UIKit, etc.
// just uncomment and you are ready to go
// ideally you would declare the markup and classes globally for example in your _init.php
$form_markup = array(
    'list' => "<div {attrs}>{out}</div>",
    'item' => "<div {attrs}>{out}</div>",
    'item_label' => "<label class='InputfieldHeader' for='{for}'><strong>{out}</strong></label>",
    'item_label_hidden' => "<label class='InputfieldHeader InputfieldHeaderHidden'><span>{out}</span></label>",
    'item_content' => "<div class='InputfieldContent {class}'>{description}{out}{error}{notes}</div>",
    'item_error' => "<small class='form-error is-visible'>{out}</small>",
    'item_description' => "<p class='description'><label>{out}</label></p>",
    'item_notes' => "<p class='notes'><label><small>{out}</small></label></p>",
    'success' => "<div data-alert class='alert-box success'>{out}</div>",
    'error' => "<div data-alert class='alert-box alert'>{out}</div>",
    'item_icon' => "",
    'item_toggle' => "",
    'InputfieldFieldset' => array(
        'item' => "<fieldset {attrs}>{out}</fieldset>",
        'item_label' => "<legend>{out}</legend>",
        'item_label_hidden' => "<legend class='hide'>{out}</legend>",
        'item_content' => "<div class='InputfieldContent'>{out}</div>",
        'item_description' => "<p class='fieldset-description'><label>{out}</label></p>",
        'item_notes' => "<p class='notes'><small>{out}</small></p>",
    )
);

$form_classes = array(
    'form' => 'InputfieldFormNoHeights',
    'list' => 'Inputfields',
    'list_clearfix' => 'clearfix',
    'item' => 'Inputfield Inputfield_{name} {class}',
    'item_required' => 'InputfieldStateRequired',
    'item_error' => 'InputfieldStateError',
    'item_collapsed' => 'InputfieldStateCollapsed',
    'item_column_width' => 'InputfieldColumnWidth',
    'item_column_width_first' => 'InputfieldColumnWidthFirst',
    'InputfieldFieldset' => array(
        'item' => 'Inputfield_{name} {class}',
    )
);


$SocialLogin = $this->modules->get("SocialLogin");
if (!$SocialLogin) {
    throw new Wire404Exception('SocialLogin module is not installed');
}

if (isset($_REQUEST['hybridauth'])) :

    // Process the request
    $SocialLogin->HybridAuthProcess();

else:

    ob_start();
    ?>

    <div class="content">

        <?php
            // if user is logged in show a welcome message and his profile
            if ($user->isLoggedin()) {
                $username = $user->email;
                if ($user->oauth)
                {
                    $oauth = unserialize($user->oauth);
                    $username = $oauth->displayName;
                }
                echo '<h1>' . __('Welcome') . ' ' . $username . '</h1>';

                $profile = $SocialLogin->showProfile();
                if ($profile) {
                    if ($form_markup) $profile->setMarkup($form_markup);
                    if ($form_classes) $profile->setClasses($form_classes);
                    echo $profile->render();
                }

                ?>
                <a class='action' href='<?php echo $config->urls->admin; ?>login/logout/'><?php echo __('Logout'); ?></a><?php

            } else {
                // else show the login form
                // you could use the internal function via
                // echo $SocialLogin->showLogin()
                // or customize the form to your likings below
                $this->session->set('redirect', $this->page->url);
                ?>
                <fieldset>
                <form action='./' method='post'>
                    <?php if ($this->input->post->user) echo "<h2 class='error'>Login failed</h2>"; ?>

                    <p><label>Email <input type='text' name='slogin_email'/></label></p>
                    <p><label>Password <input type='password' name='slogin_pass'/></label></p>
                    <p><input type='submit' name='slogin_submit' value='Login'/></p>
                </form>
                </fieldset>
                <?php $providers = $SocialLogin->getProvidersMenu(); ?>

                <?php if (!empty($providers)) : ?>

                    <fieldset>
                        <legend><?php echo __('Or use a social service'); ?></legend>

                        <ul class="social-services">
                            <?php
                                foreach ($providers as $provider) {
                                    echo "<li>$provider</li>";

                                }

                            ?>
                        </ul>
                    </fieldset>

                <?php endif; ?>
                <fieldset>
                    <legend><?php echo __('Or register'); ?></legend>

                    <a href="<?php echo $this->config->urls->root . $SocialLogin::registerPath; ?>"><?php echo __('Register'); ?></a>
                </fieldset>
                <?php
            }
        ?>

    </div>

    <?php

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

endif;
