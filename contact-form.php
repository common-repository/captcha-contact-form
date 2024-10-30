<?php

/**

 * @package captchacontactform

 */

/*

Plugin Name: Captcha Contact Form

Plugin URI: http://www.example.com

Description: Contact form with Google reCaptcha V.2 Capabilities and SMTP integration

Version: 1.0

Author: Jonathon Durno

Author URI: http://jonathondurno.com

License: GPLv2 or later

Text Domain: Captcha Contact Form

*/

require_once('environment.php');

/**
 * Filter the mail content type.
 */
function ccf_set_html_mail_content_type() {
	return 'text/html';
}
add_filter( 'wp_mail_content_type', 'ccf_set_html_mail_content_type' );



add_action( 'phpmailer_init', 'ccf_send_smtp_email' );
function ccf_send_smtp_email( $phpmailer ) {
    $phpmailer->isSMTP();
    $phpmailer->Host       = $_ENV["MY_HOST"];
    $phpmailer->Port       = $_ENV["MY_PORT"];
    $phpmailer->SMTPSecure = 'tls';
    $phpmailer->SMTPAuth   = true;
    $phpmailer->Username   = $_ENV["MY_EMAIL"];
    $phpmailer->Password   = $_ENV["MY_PASSWORD"];
    $phpmailer->From       = $_ENV["MY_EMAIL"];
    $phpmailer->FromName   = $_ENV["CONTACT_NAME"];
    
}

add_filter( 'wp_mail_content_type','ccf_set_my_mail_content_type' );
function ccf_set_my_mail_content_type() {
    return "text/html";
}

// get course progress for user
add_action( 'wp_ajax_nopriv_contactFormFunction', 'ccf_ajax_contactFormFunction_handler' );
add_action( 'wp_ajax_contactFormFunction', 'ccf_ajax_contactFormFunction_handler' );
function ccf_ajax_contactFormFunction_handler() {

    // The secret Key
    $server_secret_key = $_ENV["SECRET_KEY"];
    
    $contactFormData = "";
    if (isset($_POST['contactFormData'])) {
        $contactFormData = filter_var($_POST['contactFormData'], FILTER_SANITIZE_STRING);
    }
	$contactFormDataPieces = explode("/////", $contactFormData);
	$name = strval($contactFormDataPieces[0]);
	$email = strval($contactFormDataPieces[1]);
    $subject = strval($contactFormDataPieces[2]);
	$message = strval($contactFormDataPieces[3]);
    $captchaResponse = strval($contactFormDataPieces[4]);

    

    header('Content-Type: application/json');
    if ($name === ''){
        // NB: This needs to be an echo. A return returns an 'undefined' value whether JSON encoded or not. 
        echo json_encode("Name cannot be empty");
    die();
    }
    if ($email === ''){
        // NB: This needs to be an echo. A return returns an 'undefined' value whether JSON encoded or not. 
        echo json_decode("Email cannot be empty");
    die();
    } else {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
        // NB: This needs to be an echo. A return returns an 'undefined' value whether JSON encoded or not. 
        echo json_encode("Email format invalid");
    die();
    } 
    }
    if ($subject === ''){
        // NB: This needs to be an echo. A return returns an 'undefined' value whether JSON encoded or not. 
        echo json_encode("Subject cannot be empty");
    die();
    }
    if ($message === ''){
        // NB: This needs to be an echo. A return returns an 'undefined' value whether JSON encoded or not. 
        echo json_encode("Message cannot be empty");
    die();
    }

    // verify captcha response
    $verify = wp_remote_retrieve_body(wp_remote_get( "https://www.google.com/recaptcha/api/siteverify?secret={$server_secret_key}&response={$captchaResponse}" ));
    $captcha_success=json_decode($verify);
    if ($captcha_success->success==false) {
        // NB: This needs to be an echo. A return returns an 'undefined' value whether JSON encoded or not. 
        echo json_encode("You might be a robot. Please reload the page and try again.");
    die();
    }
    
        
    $recipient = $_ENV["MY_EMAIL"];
    $headers[]   = 'Reply-To: ' . $name . '<' . $email . '>';
    $fullsubject = 'Contact from Website';
    $content = "<b>From:</b> " . $name . "<br>" . "<b>Subject:</b> " . $subject . "<br><br>" . "<b>Message:</b> " . "<br>" . $message;
	$whatIsTheProblem = array(
		"recipient" => $recipient, "subject" => $fullsubject, "content" => $content, "headers" => $headers
	);
	$problemToShow = array("Please try again", $whatIsTheProblem);
	$successToShow = array("Email successfully sent", "Email successfully sent");
	
    if (wp_mail( $recipient, $fullsubject, $content, $headers )) {
        // NB: This needs to be an echo. A return returns an 'undefined' value whether JSON encoded or not. 
		echo json_encode($successToShow);
    } else {
        // NB: This needs to be an echo. A return returns an 'undefined' value whether JSON encoded or not. 
        echo json_encode($problemToShow);
    }
	die();  
}


function ccf_jonathon_durno_contact_shortcode() {
    ob_start();
    include plugin_dir_path(__FILE__) . "/contact-template.php";
    return ob_get_clean();   
} 
add_shortcode( 'contactForm', 'ccf_jonathon_durno_contact_shortcode' );





/**
 * Add Captcha Contact form settings menu to wp_admin
 */

function captcha_contact_form() {
	add_menu_page('Captcha Contact Form', 'Captcha Contact Form', 'manage_options', 'captcha_contact_form', 'captcha_contact_form_main', 'dashicons-email', 4);
}
add_action('admin_menu', 'captcha_contact_form');
	
function captcha_contact_form_main() {

	echo "<div style='display: flex; flex-direction: row; padding: 100px; justify-content: center; align-items: center;'>";
	
	echo "<div style='display: flex; flex-direction: column; width: 800px;'>";
	
	echo '<div class="wrap"><h2>Captcha Contact Form Settings</h2>Edit Captcha Contact Form Settings Here</div>';

	echo "<form method='post' action=''>
	<h4>Google Captcha Site Key</h4>
	<input type='password' style='margin-right: 10px;' name='siteKey'></input>" . esc_html($_ENV["SITE_KEY"]) . 
	"<h4>Google Captcha Secret Key</h4>
	<input type='password' style='margin-right: 10px;' name='secretKey'></input>" . esc_html($_ENV["SECRET_KEY"]) .
	"<h4>Email Address</h4>
	<input type='text' style='margin-right: 10px;' name='emailAddress'></input>" . esc_html($_ENV["MY_EMAIL"]) .
	"<h4>Email Password</h4>
	<input type='password' style='margin-right: 10px;' name='emailPassword'></input>" .esc_html($_ENV["MY_PASSWORD"]) .
	"<h4>Host Name</h4>
	<input type='text' style='margin-right: 10px;' name='hostName'></input>" . esc_html($_ENV["MY_HOST"]) .
	"<h4>Port Number</h4>
	<input type='text' style='margin-right: 10px;' name='portNumber'></input>" . esc_html($_ENV["MY_PORT"]) .
	"<h4>Contact Name</h4>
	<input type='text' style='margin-right: 10px;' name='contactName'></input>" . esc_html($_ENV["CONTACT_NAME"]);
	echo submit_button("Save Changes", "primary", "submit"); 	echo "<p style='color: red;'>Please reload the page to see saved changes.</p>";

	echo "</form>";
	echo "</div>";
	
	echo "<div style='display: flex; flex-direction: column;'><h3>Contact Form for Testing</h3>" . do_shortcode('[contactForm]') . "<br><br><p>To add a contact form to your site, add the shortcode [contactForm]</p></div>";
	
	
	
	echo "</div>";
	
	
}