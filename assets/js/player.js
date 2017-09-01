(function($){

	if ( E.bgm.audio && E.screen == 'pc' ) {
		$.ajax({
			type: "GET",
			data: { 
				action: 'music_list_get',
				form: E.ajaxurl,
			},
			success: function(data) {
				$('#listen').append(data);
				for (var i=0; i<playlist.length; i++){
					var item = playlist[i],
					j = i;j++;
					$('#listen .list .items').append('<li class="nowrap">'+j+'. '+item.artist+' - '+item.title+'</li>');
				}

				var repeat = localStorage.repeat || 0,
					shuffle = E.bgm.shuffle,
					continous = true,
					autoplay = true;

				var time = new Date(),
					currentTrack = shuffle === 'on' ? time.getTime() % playlist.length : 0,
					trigger = false,
					audio = $('#listen .bgm')[0], 
					video = $('#bgvideo video'),
					timeout, isPlaying, playCounts;

				var bgv_pause = function() {
					if (video.hasClass('instate')) {
						video[0].pause();
					}	
				}

				var bgv_play = function() {
					if (video.hasClass('instate')) {
						video[0].play();
					}
				}

				var play = function() {
					playTisp(currentTrack, 'play');
					audio.play();
					$('#listen .play').addClass('active').html('&#xe66d;');
					isPlaying = true;
					bgv_pause();
				}

				var pause = function() {
					playTisp(currentTrack, 'pause');
					audio.pause();
					$('#listen .play').removeClass('active').html('&#xe66e;');
					isPlaying = false;
					bgv_play();
				}

				var switchTrack = function(i) {
					if (i < 0){
						track = currentTrack = playlist.length - 1;
					} else if (i >= playlist.length){
						track = currentTrack = 0;
					} else {
						track = i;
					}

					loadMusic(track);
					
				}

				var shufflePlay = function() {
					var time = new Date(),
						lastTrack = currentTrack;
					currentTrack = time.getTime() % playlist.length;
					if (lastTrack == currentTrack) ++currentTrack;
					switchTrack(currentTrack);
				}

				var ended = function() {
					pause();
					audio.currentTime = 0;
					playCounts++;
					if (continous == true) isPlaying = true;
					if (currentTrack < playlist.length) switchTrack(++currentTrack);
				}

				// Ended
				var ended = function(){
					pause();
					audio.currentTime = 0;
					playCounts++;
					if (continous == true) isPlaying = true;
					if (repeat == 1){
						play();
					} else {
						if (shuffle === 'true'){
							shufflePlay();
						} else {
							if (repeat == 2){
								switchTrack(++currentTrack);
							} else {
								if (currentTrack < playlist.length) switchTrack(++currentTrack);
							}
						}
					}
				}

				var playTisp = function(i,type) {
					var item = playlist[i];
					switch(type) {
						case 'play' :
						tips_update('正在播放：' + item.title+' - '+item.artist);
						break;

						case 'pause' :
						tips_update('音乐已暂停。');
						break;

						case 'autoplay' :
						tips_update('即将播放：' + item.title+' - '+item.artist);
						break;
					}
				}

				var loadMusic = function(i) {
					item = playlist[i];
					$('#listen .cover').attr('src', item.cover);
					$('#listen .title').html(item.title +' - '+item.artist).attr('title', item.title+' - '+item.artist);
					$('#listen .list li').removeClass('playing').eq(i).addClass('playing');
					$.getJSON(E.bgm.url+'get.php?id='+item.id, function(arr) {
						//console.log(arr.url);
						$('.bgm').attr('src',arr.url);
						if (isPlaying == true) play();
						audio.addEventListener('ended', ended, false);
					});
				}
				
				loadMusic(currentTrack);

				if (E.bgv == 'no') {
					if (E.bgm.autoplay == 'on') {
						playTisp(currentTrack, 'autoplay');
						setTimeout(function(){
							audio.addEventListener('canplay', play(), false);
						}, 3000);
					}
				}
				

				$('#listen .play').on('click', function() {
					if ($(this).hasClass('active')) {
						pause();
					} else {
						play();
					}
				});
				$('#listen .rewind').on('click', function() {
					if (shuffle === 'on') {
						shufflePlay();
					} else {
						switchTrack(--currentTrack);
					}
				});
				$('#listen .fastforward').on('click', function() {
					if (shuffle === 'on') {
						shufflePlay();
					} else {
						switchTrack(++currentTrack);
					}
				});
				$('#listen .list li').each(function(i) {
					var _i = i;
					$(this).on('click', function() {
						switchTrack(_i);
						play();
					});
				});
				$('#listen .onlist').on('click', function() {
					list = $('#listen .list');
					if (list.hasClass('show')) {
						list.removeClass('show').css({'height':'0'});
					}else{
						list.addClass('show').css({'height':'100%'});
					}
				});
				$('#bgm').on('click', function(e) {
					player = $('#listen');
					_this = $(this);
					
					if (!$('#bgm.show')[0]) {
						player.show();
						_this.addClass('show');
					}
					else {
						player.fadeOut(200);
						_this.removeClass('show');
					}

					$(document).one('click', function() {
						player.fadeOut(200);
						_this.removeClass('show');
					});
					e.stopPropagation();

					player.on('click', function(e) {
						e.stopPropagation();
					});
				});

			}
		});
	}

})(jQuery);