$(document).ready(function(){
	
	// grab the submits button ID. do not use <input type="submit"> inside the form. Use a button instead outside the form.
	$("#message_submit").click(function()
	{
		// grab the forms ID
		$("#message").submit(function(e)
		{
			// add a loading image in place of your returning outcome
			$("#simple-msg").html("sending...");
			
			// serialize/combine all submitted fields into an array
			var postData = $(this).serializeArray();
			
			// set url based of action
			var formURL = $(this).attr("action");
			
			// set ajax parameters
			$.ajax(
			{
				url : formURL,
				type: "POST",
				data : postData,
				success:function(data, textStatus, jqXHR) 
				{
					$("#simple-msg").html('<pre><code class="prettyprint">'+data+'</code></pre>');
	
				},
				error: function(jqXHR, textStatus, errorThrown) 
				{
					$("#simple-msg").html('<pre><code class="prettyprint">AJAX Request Failed<br/> textStatus='+textStatus+', errorThrown='+errorThrown+'</code></pre>');
				}
			});
			e.preventDefault();	//STOP default action
			e.unbind();
		});
			
		$("#message").submit(); //SUBMIT FORM
	});

});
