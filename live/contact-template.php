<?php
/*
Template Name: Contact
*/
?>
<?php
/* Edit the error messages here --------------------------------------------------*/
$nameError = __( 'Please enter your name.', 'wolf' );
$subjectError = __( 'Please enter a subject.', 'wolf' );
$emailError = __( 'Please enter your email address.', 'wolf' );
$emailInvalidError = __( 'Please enter a valid email address.', 'wolf' );
$commentError = __( 'Please enter a message.', 'wolf' );
/*--------------------------------------------------------------------------------*/
$errors = array();
if(isset($_POST['submitted'])) {

	if($_POST['antispam']!=''){
		$hasError = true;
	}

	if(trim($_POST['contactName']) === '') {
		$errors['nameError'] = $subjectError;
		$hasError = true;
	} else {
		$name = trim($_POST['contactName']);
	}
		
	if(trim($_POST['contactSubject']) === '') {
		$errors['subjectError'] = $subjectError;
		$hasError = true;
	} else {
		$subject = trim($_POST['contactSubject']);
	}
	
	if(trim($_POST['email']) === '')  {
		$errors['emailError'] = $emailError;
		$hasError = true;
	} else if ( !is_email($_POST['email']) ) {
		$errors['emailInvalidError'] = $emailInvalidError;
		$hasError = true;
	} else {
		$email = trim($_POST['email']);
	}
		
	if(trim($_POST['comments']) === '') {
		$errors['commentError'] = $commentError;
		$hasError = true;
	} else {
		if(function_exists('stripslashes')) {
			$comments = stripslashes(trim($_POST['comments']));
		} else {
			$comments = trim($_POST['comments']);
		}
	}
		
	if(!isset($hasError)) {
		$emailTo = wolf_get_theme_option('user_email');

		if (!isset($emailTo) || ($emailTo == '') ){
			$emailTo = get_option('admin_email');
		}	


		$sitename = get_bloginfo( 'name' );
		$from = '['.$sitename.']';
		$body = "<p>
		<strong>Name</strong> : $name<strong></p>
		<p><strong>Email</strong>: $email </p>
		<strong>Message</strong>:<p>$comments</p>";
		$headers = 'From: '.$from.' <'.$email.'>' . "\r\n" . 'Reply-To: ' . $email;
		add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));
		if(wp_mail($emailTo, $subject, $body, $headers))
			$emailSent = true;
		else
			_e('An error occured, please try again later.', 'wolf');
	}
	
} 

get_header();  
wolf_page_before();
?>

	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
				<?php if(have_posts()): while(have_posts()): the_post(); ?>
					
					<?php $meta_key_prefix = ( wolf_is_old_version() ) ? 'bd' : '_wolf'; ?>
					<?php if( get_post_meta(get_the_ID(), $meta_key_prefix . '_page_title', true) ): ?>
						<header class="entry-header">
							<h1 class="entry-title"><?php the_title(); ?></h1>
						</header><!-- .entry-header -->
					<?php endif;

					the_content();

				
				if(isset($emailSent) && $emailSent == true) { ?>

					<div class="thanks">
						<h4><?php _e('Thanks, your email was sent successfully.', 'wolf') ?></h4>
					</div>

				<?php } else { ?>
				<?php if(isset($hasError)) { ?>
					<p class="error"><?php _e('Sorry, an error occured.', 'wolf') ?><br>
						<?php if($errors != array()): ?>
							<ul>
								<?php foreach($errors as $k => $v): ?>
									<li><?php echo $v; ?></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					<p>
				<?php } ?>
				<form action="<?php the_permalink(); ?>" id="contactForm" method="post">
					<div class="contactform">
						<div class="input">
							<label for="contactName"><?php _e('Your Name', 'wolf') ?></label>
							<input type="text" name="contactName" id="contactName" value="<?php if(isset($_POST['contactName'])) echo $_POST['contactName'];?>" class="required requiredField" autofocus>
							<div class="error-inline"><?php echo $nameError; ?></div> 
						</div>

						<div class="input"><label for="email"><?php _e('Your Email', 'wolf') ?></label>
							<input type="text" name="email" id="email" value="<?php if(isset($_POST['email']))  echo $_POST['email'];?>" class="required requiredField email">
							<div class="error-inline"><?php echo $emailError; ?></div> 
							<div class="error-inline"><?php echo $emailInvalidError; ?></div> 
						</div>
						
						<div class="input">
							<label for="contactSubject"><?php _e('Subject', 'wolf') ?></label>
							<input type="text" name="contactSubject" id="contactSubject" value="<?php if(isset($_POST['contactSubject'])) echo $_POST['contactSubject'];?>" class="required requiredField">
							<div class="error-inline"><?php echo $subjectError; ?></div> 
						</div>
					
						
						<div class="input">
							<textarea name="comments" id="commentsText" class="required requiredField"><?php if(isset($_POST['comments'])) { if(function_exists('stripslashes')) { echo stripslashes($_POST['comments']); } else { echo $_POST['comments']; } } ?></textarea>
							<div class="error-inline"><?php echo $commentError; ?></div> 
						</div>
						<input type="hidden" id="antispam" name="antispam" class="hidden" value="">
						<div class="buttons">
							<input type="submit" name="submitted" id="submitted" value="<?php _e('Send', 'wolf') ?>">
						</div>
					</div>
				</form>
				<?php } ?>
				<?php endwhile; endif; ?>
		</div><!-- #content -->
	</div><!-- #primary -->
<?php
get_sidebar();
wolf_page_after();
get_footer(); 
?>