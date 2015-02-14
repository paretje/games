function search_games()
{
	$('#games').html(spinner);
	$.ajax(
	{
		url: 'xmlhttp.php?action=games_search&title=' + $('#title').val(),
		type: 'get',
		complete: function (request, status)
		{
			search_games_handle(request);
		}
	});
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
		alert(message[1]);
	}
	else if(request.responseText)
	{
		$('#games').html(request.responseText);
	}
}

function search_games_selected(gid)
{
	$('#selected_game').html('<a href="games.php?action=play&amp;gid='+gid+'">'+lang_testgame+'</a>');
}
