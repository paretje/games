function randomgames_update(cid)
{
	if(use_xmlhttprequest == "1")
	{
		this.spinner = new ActivityIndicator("body", {image: imagepath + "/spinner_big.gif"});
		new Ajax.Request('xmlhttp.php?action=games_randomgames&cid='+cid, {method: 'get', onComplete: function(request) { randomgames_update_handle(request); }});
	}
}

function randomgames_update_handle(request)
{
	if(request.responseText.match(/<error>(.*)<\/error>/))
	{
		message = request.responseText.match(/<error>(.*)<\/error>/);
		if(!message[1])
		{
			message[1] = "An unknown error occurred.";
		}
		if(this.spinner)
		{
			this.spinner.destroy();
			this.spinner = '';
		}
		alert(message[1]);
	}
	else if(request.responseText)
	{
		$('randomgames').innerHTML = request.responseText;
	}
	
	if(this.spinner)
	{
		this.spinner.destroy();
		this.spinner = '';
	}
}
