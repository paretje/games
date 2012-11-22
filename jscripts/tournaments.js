function search_games()
{
	this.spinner = new ActivityIndicator("body", {image: imagepath + "/spinner_big.gif"});
	title = document.getElementById('title');
	new Ajax.Request('xmlhttp.php?action=games_search&title='+title.value, {method: 'get', onComplete: function(request) { search_games_handle(request); }});
}

function search_games_handle(request)
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
		$('games').innerHTML = request.responseText;
	}
	
	if(this.spinner)
	{
		this.spinner.destroy();
		this.spinner = '';
	}
}

function search_games_selected(gid)
{
	$('selected_game').innerHTML = '<a href="games.php?action=play&amp;gid='+gid+'">'+lang_testgame+'</a>';
}
