<?php

return array(

	// http://hybridauth.sourceforge.net/userguide/IDProvider_info_Google.html
	"google" => array(
		"label"   => "Google",
		"fields"  => array(
			"enabled" => array(
				"type"        => "InputfieldCheckbox",
				"label"       => "Enable Google if checked...",
				"description" => ""
			),
			"keys" => array(
				"id" => array(
					"type"        => "InputfieldText",
					"label"       => "Google ID",
					"description" => "The Google ID..."
				),
				"secret" => array(
					"type"        => "InputfieldText",
					"label"       => "Google Secret",
					"description" => "The Google Secret..."
				)
			)
		)
	),

	// http://hybridauth.sourceforge.net/userguide/IDProvider_info_Facebook.html
	"facebook" => array(
		"label"   => "Facebook",
		"fields"  => array(
			"enabled" => array(
				"type"        => "InputfieldCheckbox",
				"label"       => "Enable Facebook if checked...",
				"description" => ""
			),
			"keys" => array(
				"id" => array(
					"type"        => "InputfieldText",
					"label"       => "APP ID",
					"description" => "App Id for your website. You can create one from here: https://developers.facebook.com/apps/"
				),
				"secret" => array(
					"type"        => "InputfieldText",
					"label"       => "APP Secret",
					"description" => "App Secret for your website. After you have created your 'facebook app', you find this from: https://developers.facebook.com/apps/"
				),
			),
			"trustForwarded" => array(
				"type"        => "InputfieldCheckbox",
				"label"       => "Enable trustForwarded...",
				"description" => ""
			)
		)
	),

	// http://hybridauth.sourceforge.net/userguide/IDProvider_info_Twitter.html
	"twitter" => array(
		"label"   => "Twitter",
		"fields"  => array(
			"enabled" => array(
				"type"        => "InputfieldCheckbox",
				"label"       => "Enable Twitter if checked...",
				"description" => ""
			),
			"keys" => array(
				"key" => array(
					"type"        => "InputfieldText",
					"label"       => "Twitter Key",
					"description" => "The Twitter Key..."
				),
				"secret" => array(
					"type"        => "InputfieldText",
					"label"       => "Twitter Secret",
					"description" => "The Twitter Secret..."
				)
			)
		)
	),
);