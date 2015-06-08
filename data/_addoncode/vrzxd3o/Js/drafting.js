/* Shortcut Functions */
(function( $ ){
    $.fn.doEnable = function() {
       $(this).attr("disabled", false).removeClass('disabled').css( 'cursor', 'pointer' );
    };
    $.fn.doDisable = function() {
       $(this).attr("disabled", true).addClass('disabled').css( 'cursor', 'text' );
    };
    $.fn.doShow = function() {
       $(this).clearQueue().stop().css('color', '').fadeIn(100).delay( 3000 ).fadeOut(3000);
    };
    $.fn.doHighlight = function() {
       $(this).css('color', 'red');
    };
})( jQuery );
/* Input Subject - keyUp Event Handler */
$("#EasyNewsLetter_Form_Subject").keyup(function (even) {
	$('#drafting_wrapper a').doEnable();
});
/* Input Message - keyUp Event Handler */
$("#EasyNewsLetter_Form_Message").keyup(function (even) {
	$('#drafting_wrapper a').doEnable();
});
/* Load Link - Click Event Handler */
$('#load_draft').click( function(event) {
	event.preventDefault();
	if($(this).attr("disabled")) return;
	$.ajax({
		url: 'Admin_EasyNewsLetter_Mailing',
		type: 'post',
		dataType: 'json',
		data: {	'cmd': 'load_draft',
			'verified': '###NONCE###'
 		},
		beforeSend: function() { 
			$('#load_draft').doDisable();
			$('#ticker').html("Work in progress...");  
		},
		success: function(data) {
			if (!data.error) {
				$('#save_draft').doDisable();
				$('#load_draft').doDisable();
				$("#EasyNewsLetter_Form_Subject").val(data.draft.subject); 
				$("#EasyNewsLetter_Form_Message").val(data.draft.message); 
			} else {
				$('#ticker').doHighlight();
			}
			$('#ticker').html(data.msg); 
		},
		 error: function () {
			$('#ticker').doHighlight();
			$('#ticker').html('The http request has failed!'); 
		}
	});
	$( "#ticker" ).doShow();
});
/* Save Link - Click Event Handler */
$('#save_draft').click( function(event) {
	event.preventDefault();
	if($(this).attr("disabled")) return;
	$.ajax({
		url: 'Admin_EasyNewsLetter_Mailing',
		type: 'post',
		dataType: "json",
		data: {	'cmd': 'save_draft',
			'verified': '###NONCE###',
			'subject': $("#EasyNewsLetter_Form_Subject").val(),
			'message': $("#EasyNewsLetter_Form_Message").val(),
			},
		beforeSend: function() { 
			$('#save_draft').doDisable();
			$('#ticker').html("Work in progress...");  
		},
		success: function(data) { 
			if (!data.error) {
				$('#save_draft').doDisable();
				$('#load_draft').doDisable();
			} else {
				$('#ticker').doHighlight();
			}
			$('#ticker').html(data.msg); 
		},
		error: function() { 
			$('#ticker').doHighlight();
			$('#ticker').html('The http request has failed!'); 
		}
	});
	$( "#ticker" ).doShow();
});
/* Send Link - Click Event Handler */
$('#send_draft').click( function(event) {
	event.preventDefault();
	if($(this).attr("disabled")) return;
	$.ajax({
		url: 'Admin_EasyNewsLetter_Mailing',
		type: 'post',
		dataType: "json",
		data: {	'cmd': 'send_draft',
			'verified': '###NONCE###',
			'subject': $("#EasyNewsLetter_Form_Subject").val(),
			'message': $("#EasyNewsLetter_Form_Message").val(),
			},
		beforeSend: function() { 
			$('#send_draft').doDisable();
			$('#ticker').html("Work in progress...");  
		},
		success: function(data) { 
			if (!data.error) {
				$('#send_draft').doDisable();
			} else {
				$('#ticker').doHighlight();
			}
			$('#ticker').html(data.msg); 
		},
		error: function() { 
			$('#ticker').doHighlight();
			$('#ticker').html('The http request has failed!'); 
		}
	});
	$('#ticker').doShow();
});

