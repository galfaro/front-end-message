$( "#pop_up" ).click(function() {
    $( "#message_pop" ).fadeIn( 500, function() {
        $( "#shadow_form" ).show();
    });
    return false;
});

$( "#close_shadowbox" ).click(function() {
    $( "#message_pop" ).fadeOut( "slow", function() {
    // Animation complete.
    });
});
