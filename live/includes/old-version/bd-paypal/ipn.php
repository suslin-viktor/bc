<?php
function bd_paypal_debug_log($file, $content)
{
	if(function_exists('file_put_contents'))
		file_put_contents($file, $content );
}

include_once 'wp.php';
global $options;

$o = get_option('bd_paypal_settings');

/* *
 * Manage the information here to securise the payment
 */
$email_account = $o['email'];

$admin_email = bd_get_option('user_email');

if (!isset($admin_email) || ($admin_email == '') ){
	$admin_email = get_option('admin_email');
}

if( !BD_PAYPAL_PRODUCTION )
	$target_url = 'sandbox.paypal.com';
else
	$target_url = 'paypal.com';


$req = 'cmd=_notify-validate';
foreach ($_POST as $key => $value) {
	$value = urlencode(stripslashes($value));
	$req .= "&$key=$value";
}

$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Host: www.$target_url:443\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
$fp = fsockopen ('ssl://www.'. $target_url , 443, $errno, $errstr, 30);


if (!$fp) {

	bd_paypal_debug_log( 'log', 'HTTP ERROR'  );

}else{
	$item_name = $_POST['item_name'];
	$item_number = $_POST['item_number'];
	$payment_status = $_POST['payment_status'];
	$payment_amount = $_POST['mc_gross'];
	$payment_currency = $_POST['mc_currency'];
	$txn_id = $_POST['txn_id'];
	$receiver_email = $_POST['receiver_email'];
	$payer_email = $_POST['payer_email'];

	/*  Buyer info */
	$first_name = $_POST['first_name'];
	$last_name = $_POST['last_name'];

	parse_str($_POST['custom'],$custom);
	// $custom['item_id']

	fputs($fp, $header . $req);
	while (!feof($fp)) {

		$res = fgets ($fp, 1024);


		if (strcmp($res, "VERIFIED") == 0) {

			// Payment status
			if ( $payment_status == "Completed" || $payment_status == "Pending" ) {

				if ( $email_account == $receiver_email) {
				/**
				* OK
				*/
					if( $o && isset($o['confirm']) && $o['confirm']  == 'true' ){

						if( !BD_PAYPAL_PRODUCTION )
							$to = 'support@brutaldesign.com';
						else
							$to = $payer_email;

						if(function_exists('file_get_contents'))
							$email_body = file_get_contents('email_template.html');
						else
							$email_body = '<p>We have recieved your payment.<br>Thanks for your order!</p>';
						
						$search = array('%NAME%');
						$replace = array( $first_name.' '.$last_name );
						$email_body = str_replace($search, $replace, $email_body);


						$sitename = get_bloginfo( 'name' );
						$from = '['.$sitename.' Store]';
						$subject = __('Order Confirmation', 'wolf');
						$headers = 'From: '.$from.' <'.$admin_email.'>' . "\r\n" . 'Reply-To: ' . $admin_email;
						add_filter('wp_mail_content_type', create_function('', 'return "text/html"; '));
						if(wp_mail( $to , $subject, $email_body, $headers))
							$_POST['confirm_email'] = 'true';
					}
					
					bd_paypal_debug_log('log', print_r( $_POST, true ) );

				/**
				* end
				*/
				}
			}
			else {
				// faill
				bd_paypal_debug_log( 'log', 'payment status : '. $payment_status  );
			}
			exit();
		}
		else if (strcmp ($res, "INVALID") == 0) {
			// faill
			bd_paypal_debug_log( 'log', 'paypal returns invalid buyer account'  );
		}
	}
	fclose ($fp);
}	
?>