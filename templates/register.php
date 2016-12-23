<?php namespace ProcessWire;

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

//ob_start();


$SocialLogin = $this->modules->get("SocialLogin");

if (!$SocialLogin) {
    throw new Wire404Exception('SocialLogin module is not installed');
}

// if user isn't logged in, then we pretend this page doesn't exist
if ($user->isLoggedin()) :

    // Maybe redirect to your profile?
    //
    $SocialLogin->showError(__('You are already logged in...')); ?>

    <a class='action' href='<?php echo $config->urls->admin; ?>login/logout/'><?php echo __('Logout'); ?></a>

    <?php
else :

    $form = $SocialLogin->showRegistration();
    if ($form) {
        if ($form_markup) $form->setMarkup($form_markup);
        if ($form_classes) $form->setClasses($form_classes);
        echo $form->render();
    }

endif;

?>
    <!--    To get the validation and password strength function you need to include the according javascripts and styles from ProcessWire -->
    <!-- They could be included by the module, but that´s a todo for now  -->
    <!-- they also require jQuery. waaaaaah -->
    <script src="//code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>

    <link rel="stylesheet" type="text/css" href="<?php echo $config->urls->modules ?>Inputfield/InputfieldPassword/InputfieldPassword.css"/>
    <script type="text/javascript" src="/wire/modules/Inputfield/InputfieldPassword/complexify/jquery.complexify.min.js"></script>
    <script type="text/javascript" src="/wire/modules/Inputfield/InputfieldPassword/complexify/jquery.complexify.banlist.js"></script>
    <script type="text/javascript" src="/wire/modules/Jquery/JqueryCore/xregexp.js?v=1466417387"></script>
    <script src="/wire/modules/Inputfield/InputfieldPassword/InputfieldPassword.min.js?v=101-1466417387"></script>

    <?php

$content = ob_get_clean();