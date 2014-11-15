<?php
/**
 * @package Front End Message
 * @version 1.0
 */
/*
Plugin Name: Front End Message
Plugin URI: http://#
Description: This plugin was created to extend on the "front-end-pm" plugin. This plugin contains a form inside of a shadowbox which can be placed on a members profile page.
Author: Gabriel Alfaro
Version: 1.0
Author URI: http://#
*/

//Main CLASS
if (!class_exists("fep_message_class")){
	
  class fep_message_class{
/******************************************SETUP BEGIN******************************************/
    //Constructor
    function __construct(){
		$this->setupLinks();
	}

	//Setup some variables
	// url var
	var $pluginDir = "";
	var $pluginURL = "";
	var $styleDir = "";
	var $styleURL = "";
	var $pageURL = "";
	var $actionURL = "";
	var $jsURL = "";
  
	function setupLinks(){ //And DB table name too :)
		global $wpdb;
		$this->pluginDir = plugin_dir_path( __FILE__ )."/";
		$this->pluginURL = plugins_url()."/front-end-message/";
		$this->styleDir = $this->pluginDir."style/";
		$this->styleURL = $this->pluginURL."style/";
		$this->jsURL = $this->pluginURL."js/";
	}
	
    function fep_enqueue_scripts(){
		wp_enqueue_style( 'fep-style', $this->styleURL . 'style.css' );
		wp_enqueue_script( 'fep-script', $this->jsURL . 'script.js', array(), '1.0.0', true );
		wp_enqueue_script( 'fem-script', $this->jsURL . 'ajax.js', array(), '1.0.0', true );		
    }
/******************************************SETUP END******************************************/

/******************************************NEW MESSAGE PAGE BEGIN******************************************/
//Create and display the message form
    function dispNewForm(){

		global $user_ID;
		$token = $this->fep_create_nonce();
		
/****** Update here to get users name ******/
		$store_user = get_userdata( get_query_var( 'author' ) );
		$message_to = $store_user->user_login;
		echo $message_to;
/****** end of Update here to get users name ******/
		
		$message_title = ( isset( $_REQUEST['message_title'] ) ) ? $_REQUEST['message_title']: '';
		$message_content = ( isset( $_REQUEST['message_content'] ) ) ? $_REQUEST['message_content']: '';
		$parent_id = ( isset( $_REQUEST['parent_id'] ) ) ? $_REQUEST['parent_id']: 0;
	  
		$newMsg = "
        <button class='contact_button contact-seller-button' id='pop_up' href=''>CONTACT SELLER</button>                        
        <div id='message_pop'>
			<div id='close_shadowbox'></div>
            <div id='shadow_container'>
				<div id='shadow_form'>			
					<form name='message' id='message' action='".$this->pluginURL."checkmessage.php' method='post' enctype='multipart/form-data'>";
						$newMsg .= __("To", "fep")."<font color='red'>*</font>:<br/>";		
						$newMsg .="<input type='text' name='message_to' placeholder='$message_to' autocomplete='off' value='$message_to' /><br/>";
						$newMsg .= __("Subject", "fep")."<font color='red'>*</font>:<br/>
						<input type='text' name='message_title' placeholder='Subject' maxlength='65' value='$message_title' /><br/>".
						__("Message", "fep")."<font color='red'>*</font>:<br/>
						<textarea name='message_content' placeholder='Message Content'>$message_content</textarea>";
						$newMsg .="<input type='hidden' name='message_from' value='$user_ID' />
						<input type='hidden' name='parent_id' value='$parent_id' />
						<input type='hidden' name='token' value='$token' /><br/>
					</form>
					<input type='button' id='message_submit' value='Send Message' />
					<div id='simple-msg'></div>
                </div>
            </div>
        </div>
        <script src='".$this->jsURL."shadow_box.js'></script>
		";
		
		return $newMsg;
    }
/******************************************NEW MESSAGE PAGE END******************************************/

/******************************************MISC. FUNCTIONS BEGIN******************************************/
	function fep_create_nonce($action = -1) {
		$time = time();
		$nonce = wp_create_nonce($time.$action);
		return $nonce . '-' . $time;
	}
/******************************************MISC. FUNCTIONS END******************************************/

  } //END CLASS
} //ENDIF

// display plugin and register
if(class_exists("fep_message_class")){
	$FEPNEWM = new fep_message_class();
}

if (isset($FEPNEWM)){
	
	//ADD ACTIONS
	add_action('wp_enqueue_scripts', array(&$FEPNEWM, "fep_enqueue_scripts"));
	
	//ADD SHORTCODES
	add_shortcode('front-end-message', array(&$FEPNEWM, "dispNewForm"));
}
?>