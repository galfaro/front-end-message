<?php require('../../../wp-blog-header.php'); ?>
<?php
class Form_validate {
/******************************************CHECK MESSAGE BEGIN******************************************/
//function that proccesses the message. security filters, email notification, and update database we have to ajax this function.
    function dispCheckMsg(){
		
		global $wpdb, $user_ID;

		$from = $_POST['message_from'];
		$uData = get_userdata($from);
		$fromName = $uData->display_name;

		if ($_POST['message_to']) {
			$preTo = $_POST['message_to'];
		}

		$to = $this->convertToID($preTo);
		$title = $this->input_filter($_POST['message_title']);
		$content = $this->input_filter($_POST['message_content']);
		$parentID = $_POST['parent_id'];

		$send_date = current_time('mysql');
		
		//Check for errors first
		if (!$to || !$title || !$content || ($from != $user_ID)){
			if (!$to)
			  $theError .= __("You must enter a recipient!<br />", "fep");
			if (!$title)
			  $theError .= __("You must enter a subject!<br />", "fep");
			if (!$content)
			  $theError .= __("You must add a message!<br />", "fep");
			if ($from != $user_ID)
			  $theError .= __("You do not have permission to send this message!", "fep");
			$theError = $theError;
		}
		
		// Check if a form has been sent
		$postedToken = filter_input(INPUT_POST, 'token');
		if (empty($postedToken)){
			$this->error = __("Invalid Token. Please try again!", "fep");
			return;
		}
		
		if (!$theError){
			//If no errors then continue and send message
			if($this->fep_verify_nonce($postedToken)){
				if ($parentID == 0){
					$this->fepTable = $wpdb->prefix."fep_messages";
					$wpdb->query($wpdb->prepare("INSERT INTO {$this->fepTable} (from_user, to_user, message_title, message_contents, parent_id, last_sender, send_date, last_date) VALUES ( %d, %d, %s, %s, %d, %d, %s, %s )", $from, $to, $title, $content, $parentID, $from, $send_date, $send_date));
				}
				$this->sendEmail($to, $fromName, $title);
				echo "Your message has been sent...";
			}else{
				echo "Your message was already sent...";
			}
		}else{
			echo $theError;
		}

	}

    function sendEmail($to, $fromName, $title){
		$toOptions = $this->getUserOps($to);
		$notify = $toOptions['allow_emails'];
		if ($notify == 'true'){
			$sendername = get_bloginfo("name");
			$sendermail = get_bloginfo("admin_email");
			$headers = "MIME-Version: 1.0\r\n" .
				"From: ".$sendername." "."<".$sendermail.">\r\n" . 
				"Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"\r\n";
			$subject = "" . get_bloginfo("name").": New Message";
			$message = "You have received a new message in \r\n";
			$message .= get_bloginfo("name")."\r\n";
			$message .= "From: ".$fromName. "\r\n";
			$message .= "Subject: ".$title. "\r\n";
			$message .= "Please Click the following link to view full Message. \r\n";
			$message .= $this->pageURL."\r\n";		
			$mUser = get_userdata($to);
			$mailTo = $mUser->user_email;
		
			//wp_mail($mailTo, $subject, $message, $headers); // uncomment this line if you want blog name in message from, comment following line
			wp_mail($mailTo, $subject, $message);
		}
    }

    function convertToID($preTo){
		global $user_ID;
		$result = 0;
		$user = get_user_by( 'login' , $preTo );
		
		if ($user != '')
		  $result = $user->ID;
		if ($result != $user_ID)
		  return $result;
		else
		  return 0;
    }
	
	function input_filter($string){
		return esc_attr(sanitize_text_field($string));
    }	
/******************************************CHECK MESSAGE END******************************************/

/******************************************USER SETTINGS BEGIN******************************************/
// grab senders info
    function getUserOps($ID){
		$pmUserOps = array(
			'allow_emails' 	=> 'true',
			'allow_messages' 	=> 'true',
			'allow_ann' 		=> 'true'
		);
  
		//Get old values if they exist
		$userOps = get_user_meta($ID, $this->userOpsName, true);	   
  
		if (!empty($userOps)){
			foreach ($userOps as $key => $option)
			  $pmUserOps[$key] = $option;
		}
  
		update_user_meta($ID, $this->userOpsName, $pmUserOps);
		return $pmUserOps;
    }
/******************************************USER SETTINGS END******************************************/

/******************************************MISC. FUNCTIONS BEGIN******************************************/
	function fep_verify_nonce( $_nonce, $action = -1) {
		//Extract timestamp and nonce part of $_nonce
		$parts = explode( '-', $_nonce );
		$nonce = $parts[0]; // Original nonce generated by WordPress.
		$generated = $parts[1]; //Time when generated
	
		$nonce_life = 60*60; //We want these nonces to have a short lifespan
		$expire = (int) $generated + $nonce_life;
		$time = time(); //Current time
	  
		// bad formatted onetime-nonce
		if ( empty( $nonce ) || empty( $generated ) )
		return false;
  
		//Verify the nonce part and check that it has not expired
		if( !wp_verify_nonce( $nonce, $generated.$action ) || $time > $expire )
		return false;
  
		//Get used nonces
		$used_nonces = get_option('_fep_used_nonces');
  
		//Nonce already used.
		if( isset( $used_nonces[$nonce] ) )
		return false;
  
		foreach ($used_nonces as $nonces => $timestamp){
			if( $timestamp < $time ){
			//This nonce has expired, so we don't need to keep it any longer
			unset( $used_nonces[$nonces] );
			}
		}
  
		//Add nonce to used nonces and sort
		$used_nonces[$nonce] = $expire;
		asort( $used_nonces );
		update_option( '_fep_used_nonces',$used_nonces );
		return true;
	}
/******************************************MISC. FUNCTIONS END******************************************/
}

$form_validate = new Form_validate();
$form_validate->dispCheckMsg();
?>