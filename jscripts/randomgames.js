function randomgames_update(cid)
{
	if(use_xmlhttprequest == "1")
	{
		$('#randomgames').html(spinner);
		$.ajax(
		{
			url: 'xmlhttp.php?action=games_randomgames&cid=' + cid,
			type: 'get',
			complete: function (request, status)
			{
				randomgames_update_handle(request);
			}
		});
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
		alert(message[1]);
	}
	else if(request.responseText)
	{
		$('#randomgames').html(request.responseText);
	}
}
