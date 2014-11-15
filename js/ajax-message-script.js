jQuery(document).ready(function ($) {

	// Close popup
    $(document).on('click', '.message_overlay, .close', function () {
		$('form#message').fadeOut(500, function () {
            $('.message_overlay').remove();
        });
        return false;
    });

    // Show the login/signup popup on click
    $('#show_message').on('click', function (e) {
        if ($(this).attr('id') == 'show_message') 
			$('form#message').fadeIn(500);
        else 
			$('form#message').fadeIn(500);
        e.preventDefault();
    });	
});