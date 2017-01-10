$(function() {

	$elem = $('#lastfm-scrobble-grab');

	window.setInterval(function(){

console.log($elem);

		var url = '/actions/lastfmscrobblegrab/update/updatescrobble?limit=' + $elem.data('scrobble-limit') + '&' +
				 'username=' + $elem.data('scrobble-username') + '&' +
				 'showcurrentlyplaying=' + $elem.data('scrobble-showcurrentlyplaying') + '&' +
				 'showrecentlyplayed=' + $elem.data('scrobble-showrecentlyplayed') + '&' +
				 'showalbumthumbnail=' + $elem.data('scrobble-showalbumthumbnail') + '&' +
				 'showartist=' + $elem.data('scrobble-showartist') + '&' +
				 'showalbum=' + $elem.data('scrobble-showalbum') + '&' +
				 'showtrack=' + $elem.data('scrobble-showtrack');
		
		console.log($elem.data());

		$.ajax({
			url: url,
			type: 'GET',
			dataType: 'json',
			success: function(data){

						if(data.success == true){
							var scroggleListElement = $('#lastfm-scrobble-grab')
							if(scroggleListElement.length > 0){
								scroggleListElement.empty();
								scroggleListElement.html(data.data);
							}
						}
			}
		});
		
	}, $elem.data('scrobble-requeryfrequency'), $elem);

});
