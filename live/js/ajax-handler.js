function getAjax() {
	var url = 'http://' + location.hostname + '/ajax-handler/'; //тут хранится обработчик на рнр
	var fd = new FormData(); //зарезервированный класс

	var xhr = new XMLHttpRequest();
	xhr.open("POST", url);

	xhr.onreadystatechange = function() {
		if (this.readyState !== 4) {
			return;
		}
		
		resp = eval(this.responseText);
		//скорей всего был временный вывод
		document.getElementById('timestamp').innerHTML = resp.time;
	}
	xhr.send(fd);
}

function getAjaxSend() {

    var url = 'http://' + location.hostname + '/send-ajax-handler/'; //тут хранится обработчик на рнр
    var fd = new FormData(); //зарезервированный класс
    fd.append('user_input', document.getElementById('user_input').value);	//кол-во append = кол-во инпутов, user_input - name инпута
    fd.append('game_id', document.getElementById('game_id').value);	// id игры для записи в game_log

    var xhr = new XMLHttpRequest();
    xhr.open("POST", url);

    xhr.onreadystatechange = function() {

        if (this.readyState !== 4) {
            return;
        }

        sendresp = eval(this.responseText);

        //вывод результата correct/incorrect
        if (document.getElementById('artist').innerHTML != 'correct') {
            document.getElementById('artist').innerHTML = sendresp.artist;
        }

        if (document.getElementById('artist').innerHTML == 'undefined') {
            document.getElementById('artist').innerHTML = '';
        }

        if (document.getElementById('song').innerHTML != 'correct') {
            document.getElementById('song').innerHTML = sendresp.song;
        }

        if (document.getElementById('song').innerHTML == 'undefined') {
            document.getElementById('song').innerHTML = '';
        }

        document.getElementById('status1').innerHTML = sendresp.status1;
        document.getElementById('status2').innerHTML = sendresp.status2;

        document.getElementById('text_print').innerHTML = sendresp.text_print;
        /*
        document.getElementById('view1').innerHTML = sendresp.view1;
        document.getElementById('view2').innerHTML = sendresp.view2;
        document.getElementById('view3').innerHTML = sendresp.view3;
        document.getElementById('view4').innerHTML = sendresp.view4;
        */

        if ( (document.getElementById('artist').innerHTML == 'correct' && document.getElementById('song').innerHTML != 'correct') && sendresp.status1 == 1 ) {
            document.getElementById('user_input').value = '';
        }

        if ( (document.getElementById('song').innerHTML == 'correct' && document.getElementById('artist').innerHTML != 'correct') && sendresp.status2 == 1 ) {
            document.getElementById('user_input').value = '';
        }


        if ( ( sendresp.status1 == 1 && sendresp.status2 == 1 ) || ( document.getElementById('artist').innerHTML == 'correct' && document.getElementById('song').innerHTML == 'correct' ) || ( sendresp.result1 == 'correct' && sendresp.result2 == 'correct' ) ) {
            document.getElementById('user_input').setAttribute("disabled", "disabled");
            document.getElementById('user_input').value = '';
            //document.getElementById("submit").style.display = "none";
            //document.getElementById('first_fone').style.display = 'block';
        }

        //очищать поле ввода при отправке формы
        document.getElementById('user_input').value = '';

    }

    xhr.send(fd);

}




