idleTime = 0;
$(document).ready(function(){
	$limit = 15;
	
	if ($.cookie('test_status') != '1') {
		$('.cookie').html('<div class="container"><div class="alert alert-warning alert-dismissable">' +
            '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' +
            '<span>You haven\'t been to this page recently, or you\'ve cleared your browser\'s stored cookies.  Welcome!  Shortly, you\'ll see the email subscription popup.  If you refresh the page, this message will change!</span>' +
            '</div></div>');
		$.get('/form.html', function(data) {
			$('.subs-popup').html(data);
		});
		function timerIncrement() {
			idleTime = idleTime + 1;
			if (idleTime > $limit) { 
				$('.subs-popup ').show();
				idleTime = 0;
			}
		}
		
		// Increment the idle time counter every second.
		var idleInterval = setInterval(timerIncrement, 1000); // 1 second

		// Zero the idle timer on mouse movement.
		$(this).mousemove(function (e) {
			idleTime = 0;
		});
		$(this).keypress(function (e) {
			idleTime = 0;
		});
		
		$.cookie('test_status', '1', { expires: 30 });
	} else {
		$('.cookie').html('<div class="container"><div class="alert alert-warning alert-dismissable">' +
			'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' +
			'<span>You have been to this page recently.  Welcome back!  You won\'t see the email subscription popup.  If you\'d like to see it again, clear your browser\'s cookies.</span>' +
			'</div></div>');
	}
});