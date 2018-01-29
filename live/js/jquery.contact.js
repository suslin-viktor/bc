jQuery(function($){
	$('#submitted').click(function(){
		
		valid = true;
		var name = $('#contactName');
		var subject = $('#contactSubject');
		var email = $('#email');
		var message = $('#commentsText');
		var antispam = $('#antispam');

		if(antispam.val() != ''){
			valid = false;
		}

		if(name.val() == ''){
			name.parent().addClass('error');
			name.next('div').show();
			valid = false;
			name.focus(function(){
		               name.next('div').fadeOut();
		               name.parent().removeClass('error');
		           });
		}

		if(subject.val() == ''){
			subject.parent().addClass('error');
			subject.next('div').show();
			valid = false;
			subject.focus(function(){
		               subject.next('div').fadeOut();
		               subject.parent().removeClass('error');
		           });
		}

		if(message.val() == ''){
			message.parent().addClass('error');
			message.next('div').show();
			valid = false;
			message.focus(function(){
		               message.next('div').fadeOut();
		               message.parent().removeClass('error');
		           });
		}

		if(email.val() == ''){
			email.parent().addClass('error');
			email.next().next('div').hide();
			email.next('div').show();
			valid = false;
			email.focus(function(){
		               email.next('div').fadeOut();
		               email.parent().removeClass('error');
		           });
		}else if( !email.val().match(/^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/) ){
			email.parent().addClass('error');
			email.next('div').hide();
			email.next().next('div').show();
			valid = false;
			email.focus(function(){
		               email.next().next('div').fadeOut();
		               email.parent().removeClass('error');
		           });
		}
		
		return valid;
	});
	
});