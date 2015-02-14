Rating.build_gamesbit = function(tid, options)
{
	if(!$("#rating_thread_"+tid))
	{
		return;
	}
	var list = $("#rating_thread_"+tid);
	list.addClass("star_rating")
		.addClass(options.extra_class);

	list_classes = new Array();
	list_classes[1] = 'one_star';
	list_classes[2] = 'two_stars';
	list_classes[3] = 'three_stars';
	list_classes[4] = 'four_stars';
	list_classes[5] = 'five_stars';

	for(var i = 1; i <= 5; i++)
	{
		var list_element = $("<li></li>");
		var list_element_a = $("<a></a>");
		list_element_a.addClass(list_classes[i])
					  .attr("title", lang.stars[i])
					  .attr("href", "./games.php?action=rate&tid="+tid+"&rating="+i+"&my_post_key="+my_post_key)
			      .html(i);
		list_element.append(list_element_a);
		list.append(list_element);
	}
};

Rating.add_rating = function(parameterString)
{
	var tid = parameterString.match(/tid=(.*)&(.*)&/)[1];
	var rating = parameterString.match(/rating=(.*)&(.*)/)[1];
	$.ajax(
	{
		url: 'games.php?action=rate&ajax=1&my_post_key='+my_post_key+'&tid='+tid+'&rating='+rating,
		async: true,
		method: 'post',
		dataType: 'json',
		complete: function (request)
		{
			Rating.rating_added(request, tid);
		}
	});
	return false;
};
