var er_s = "<span class='msgbox-error'>";
var er_e = "<span class='msgbox-success'>";
var js_e = "<span class='msgbox-error'>";
var js_s = "<span class='msgbox-success'>";
var span = "</span>";
var loading_min = "<div class='loading_c'><div class='loading_min'><div></div><div></div><div></div><div></div><div></div></div></div>";
var domain_s = location.protocol+'//'+document.domain;

function no_referer(url){
	window.open('/class/no_referer.php?go_url='+url+'');
}

var basket_new = "<a href='/basket'><div class='notifications_e'><img src='/css/img/info/basket_new.png' align='absmiddle'><span style='margin: 10px;'>Товар ожидает оплаты</span></div></a>";
function renew_balance_rating(){ $.post('infoall.php?echo_info_us=ok', function(data){ $("#echo_moneys").html(data.split('|')[0]); $("#echo_rating").html(data.split('|')[1]); }); }

function ex_menu(x){ if (document.getElementById(x).style.display == 'none') { document.getElementById(x).style.display = ''; } else { document.getElementById(x).style.display = 'none'; } return false;  }
// Выход
function exit_us(pam){
	if(pam == 1){ 
		if(window.sessionStorage && window.localStorage){ localStorage.clear(); } 
		var cookieDate = new Date(), cookieStr = document.cookie, cookieArray = cookieStr.split(';'), i, j;
		function deleteCookie(name){
			cookieDate.setTime(cookieDate.getTime() - 1);
			var cookie = name += "=; expires=" + cookieDate.toGMTString();
			document.cookie = cookie;
		}
		for (j=0; j<cookieArray.length; j++){ cookieArray[j] = cookieArray[j].replace(/(\s*)\B(\s*)/g, ''); }
		for (i=0; i<cookieArray.length; i++){
			var cookies_one = cookieArray[i].match('^.+(?==)');
			console.log(cookies_one);
			deleteCookie(cookies_one);
		}
	}
	$.ajax({ type: "POST", url: "ajax/ajax_rest_sf.php", data: { 'sf' : 'exit_us' }, success: function(){ location.replace('/main'); } }); 
}
/*Сокрашения текста*/
function text_qualifier(value, number){
	var text = $('#'+value).val();
	if (text.length > number) { text = text.substr(0, number); }
	$('#'+value).val(text);
	$("#count").html((number-text.length));
}
/*Вставка bb code*/
function bb_code(text1, text2, pole, id){
	var content = document.getElementById(pole+id);
	content.focus();
	if (content.selectionStart == null){
		var rng = document.selection.createRange();
		rng.text=text1+rng.text+text2
	}else{
		content.value=content.value.substring(0, content.selectionStart)+text1+content.value.substring(content.selectionStart, content.selectionEnd)+text2+content.value.substring(content.selectionEnd);
	}
}
/*Вставка смайлов*/
function smilies_insert(asmile, pole, id, pam){ 
	if(pam == 1){
		var am_siple_f = Number($('#simple_am'+id).val());
		if(am_siple_f == 0){
			var am_siple = 1;
		}else{
			var am_siple = 1;
			am_siple += am_siple_f;
		}
		
		var am_t = $.trim($('#ask_desc'+id).val()).length;
		var am_s_ok;
		am_t = am_t-am_siple_f*6;

		if(am_t >= 10000)    { am_s_ok = 50; }
		else if(am_t >= 5000){ am_s_ok = 30; }
		else if(am_t >= 1000){ am_s_ok = 20; }
		else if(am_t >= 100) { am_s_ok = 10; }
		else if(am_t >= 30)  { am_s_ok = 5;  }
		else if(am_t <= 30)  { am_s_ok = 3;  }

		if(am_siple <= am_s_ok){
			$('#simple_am'+id).val(am_siple);
			bb_code('', ' '+asmile, pole, id); 
		}else{ 
			alert('Количество смайлов не может превышать '+am_s_ok+' штук');
		}
	}else{
		bb_code('', ' '+asmile, pole, id);
	}
}
/*Вставка URL в текст*/
function add_url_o(pole, id)
{
	var text_url = window.prompt("Укажите текст ссылки","");
	var _url = window.prompt("Укажите ссылку","");
	
	if($.trim(text_url) != '' && $.trim(_url) != ''){
		if("Microsoft Internet Explorer" != navigator.appName)
		{
			var content = document.getElementById(pole+id);
			content.focus();
			if (content.selectionStart == null){
				$('#'+pole+id).val('[url='+_url+']'+text_url+'[/url] ');
			}
			else{
				content.value = content.value.substring(0, content.selectionStart)+
				' [url='+_url+']'+text_url+'[/url] '+
				content.value.substring(content.selectionEnd);
			}
			
		}else{	$('#'+pole+id).val($('#'+pole+id).val()+ ' [url='+_url+']'+text_url+'[/url] ') }
	}
}
function hintOver(bname, id, t){ 
	var left_s;
	if(t == 1){ 
		left_s = '600';
	}else if(t == 2){
		left_s = '750';
	}else if(t == 3){
		left_s = '600';
	}else{
		left_s = '400';
	}
	document.getElementById(bname).style.display = ''; 
	document.getElementById(bname).style.left=left_s+"px"; 
	$.ajax({ 
		type: "POST",url: "ajax/ajax_rest_sf.php", data: { 'sf' : 'tooltip_all', 'id' : id, 't' : t}, 
		success: function(data){ 
			$('#'+bname).html(data);
		} 
	});
	return false; 
}
function hintOut(bname) { document.getElementById(bname).style.display = 'none'; return false; }
/*
function tooltip_all(id, t){ 
	if(t == 0){ 
		document.getElementById('tooltip_all').style.display = 'none'; 
	}else{ 
		$.ajax({ 
			type: "POST",url: "ajax/ajax_rest_sf.php", data: { 'sf' : 'tooltip_all', 'id' : id, 't' : t}, 
			success: function(data){ 
				document.getElementById('tooltip_all').style.display = ''; 
				document.getElementById('tooltip_all').style.left="300px";
				$('#tooltip_all').html(data);
			} 
		});
	} 
	return false;
}
*/

function tooltip_all_on(id,t) {

	//document.getElementById(tooltip+id).style.display = '';
	//document.getElementById(tooltip+id).style.left= left_s+"px";
	var left_s;
	if(t == 1){ 
		left_s = '600';
	}else if(t == 2){
		left_s = '750';
	}else if(t == 3){
		left_s = '600';
	}else{
		left_s = '400';
	}
	$.ajax({ 
		type: "POST",url: "ajax/ajax_rest_sf.php", data: { 'sf' : 'tooltip_all', 'id' : id, 't' : t}, 
		success: function(data){ 
			document.getElementById('tooltip').style.display = ''; 
			document.getElementById('tooltip').style.left=left_s+"px";
			$('#tooltip').html(data);
			return false;
		} 
	});
}
function tooltip_all_off(id) {
	document.getElementById('tooltip').style.display = 'none';
	return false;
}


function rand( min, max ) {  if( max ) { return Math.floor(Math.random() * (max - min + 1)) + min; } else { return Math.floor(Math.random() * (min + 1)); }  }
function RoundTo(aValue, aDigit)
{
	var LFactor = arguments.length>=2 ? Math.pow(10,-aDigit) : 1, tmpInt;
	aValue*=LFactor;
	tmpInt = aValue<0 ? aValue-0.5 : aValue+0.5;
	tmpInt = Math.floor(tmpInt);
	return(tmpInt/LFactor);
}
function get_cookie(cookie_name){
	var results = document.cookie.match ( '(^|;) ?' + cookie_name + '=([^;]*)(;|$)' );
	if(results){ return ( unescape ( results[2] ) ); }else{ return ""; }
}
function explode( delimiter, string ) {	
	var emptyArray = { 0: '' };

	if ( arguments.length != 2
		|| typeof arguments[0] == 'undefined'
		|| typeof arguments[1] == 'undefined' )
	{
		return "";
	}

	if ( delimiter === ''
		|| delimiter === false
		|| delimiter === null )
	{
		return false;
	}

	if ( typeof delimiter == 'function'
		|| typeof delimiter == 'object'
		|| typeof string == 'function'
		|| typeof string == 'object' )
	{
		return emptyArray;
	}

	if ( delimiter === true ) {
		delimiter = '1';
	}

	return string.toString().split ( delimiter.toString() );
}
function RadioValue(GroupName) {
	var rads = document.getElementsByName(GroupName), i, mass="";
	for (i=0; i < rads.length; i++)
	{
		if (rads[i].checked){
			mass += rads[i].value+",";
		}
	}
	mass = mass.substring(0, mass.length - 1)
	return mass;
}
function wordwrap( str, int_width, str_break, cut ) {	
	var i, j, s, r = str.split("\n");
	if(int_width > 0) for(i in r){
		for(s = r[i], r[i] = ""; s.length > int_width;
			j = cut ? int_width : (j = s.substr(0, int_width).match(/\S*$/)).input.length - j[0].length || int_width,
			r[i] += s.substr(0, j) + ((s = s.substr(j)).length ? str_break : "")
		);
		r[i] += s;
	}
	return r.join("\n");
}
function length_text(pole, am){
	var vl    = document.getElementById(pole);
	var box   = $('#'+pole).val();
	var main  = box.length * 100;
	var value = (main / am);
	var count = am - box.length;
	if(box.length <= am){ $('#count').html(count); }else{ $('#count').html('0'); vl.value = vl.value.substr(0, am);  } 
	return false;
}
function stats_clouse(){ $('#stats_us_e').hide(); }
function stats_us(id_us){  $.ajax({ type: "POST", url: "/statsall.php", data: { 'is' : id_us }, success: function(data){ $('#stats_us_e').fadeIn(100).html(data); } });  }
function test_virus(id,url_test){ 
/*
	$('#t_virus'+id).hide();
	ws = window.open('http://online.us.drweb.com/result/?url='+url_test);
	ws.focus();
	
	http://antivirus-alarm.ru/proverka/?url=  лучше эту
	*/
	
	$.ajax({  
		type: "POST", url: "ajax/ajax_rest_sf.php", data: { 'sf' : 'test_virus', 'url_test' : $.trim(url_test) }, 
		beforeSend: function(){	$('#loading').show(); }, success: function(data) { $('#loading').hide(); $('#t_virus'+id).hide(); alert(data); }  
	});

}
function clouse_r_block(){ 
	var cooke_b_r = get_cookie('clouse_r_block');
	if(cooke_b_r == '' || cooke_b_r == 'on'){
		if(confirm('Спрятать правый блок ?')){ $('#right_block').hide(); document.cookie="clouse_r_block=off; path=/;"; } 
	}else{
		document.cookie="clouse_r_block=on; path=/;"; 
		$.post('infoall.php?right_block_show=ok', function(data) { $('#right_block').html(data); });
	}
}
function admin(){ $.ajax({ type: 'GET', url: "infoall.php?admin", success: function(data){$('#echoall').html(data); }}); }
function ref_allclouse(){$('#ref_all').fadeOut(300);}
function ref_all(id){ 
	$.ajax({
		type: 'POST', url: "infoall.php", data: { 'ref_all' : id },      
		beforeSend: function(){	$('#ref_all').fadeOut(1000); $("#load_ref"+id).append('<img style=" margin-top: 4px; vertical-align: middle;" src="css/img/forum/spinner.gif" width=16 height=11 border=0">'); },
		success: function(data){	$('#ref_all').fadeIn(1).html(data); $('#load_ref'+id).fadeOut(1000);   }
	});
}
function salemy(){ 
	$.ajax({ type: "POST",url: "ajax/ajax_rest_sf.php", data: { 'sf' : 'salemy', 'proc' : $.trim($('#proc_s').val()), 'price' : $.trim($('#price').val()) }, success: function(data) { $('#spgomyy').html(data); }	});
}
function backmy() { 
	$.ajax({ type: "POST",url: "ajax/ajax_rest_sf.php", data: { 'sf' : 'backmy'}, success: function(data) { $('#backmyy').html(data); } });
}

function coun_view(id){ 
	$.ajax({ type: "POST",url: "ajax/ajax_tests.php", data: { 'sf' : 'co_view', 'id' : id }, success: function(data){ $('#coun'+id).fadeIn(1).html(data); } });
}

function comp(id,titrek) 
{ 
	var tm;
	var comp_text = $.trim($('#comptext'+id).val()).length;
	
    function slow()
    {
        $('#compecho'+id).fadeOut('slow');
        if (tm) clearTimeout(tm);
    }
	
	if(comp_text < 10){
	
		$('#compecho'+id).html("<center>Укажите минимум 10 символов текста для жалобы.</center><br/>");
		$('#compecho'+id).attr("style", "");
		tm = setTimeout(function() { slow() }, 3000);
		return false;
	
	}else{	

		$.ajax({ 
				type: "POST", url: "ajax/ajax_manage_adv.php",	
				data: { 'sf': 'comp', 'id': id, 'titrek': titrek, 'comptext': $('#comptext'+id).val() },  
				success: function(data) {	
				
					if(data == 0){ $('#compecho2'+id).html("<center>Жалоба отправлена.</center>"); }
					else if(data == 1){ $('#compecho'+id).html("<center>Укажите минимум 10 символов текста для жалобы.</center><br/>"); }
					else if(data == 2){ $('#compecho2'+id).html("<center>Вы уже оставляли жалобу.</center>"); }
					
				}
	   });
	   return false;
	}
}

function plus_rek(id,titrek) 
{ 
	$.ajax({ 
		type: "POST", url: "ajax/ajax_manage_adv.php",	
		data: { 'sf': 'plus_rek', 'id': id, 'titrek': titrek },  
		success: function(data) {	
			
					if(data == 0){ alert('OK'); }
					if(data == 1){ alert('Вы уже ставили плюс...'); }
					if(data == 2){ alert('На этой рекламе уже достаточно плюсов!'); }
				}
	   });
	   return false;
}

//Рефералы
function price_ref(id)
{
	if (confirm('Приступить к покупке реферала ID: '+id+' ?'))
	{
		$.ajax({
			type: "POST",url: "ajax/ajax_pay_adv.php", data: { 'sf' : 'price_ref', 'id' : id },         
			success: function(data){ 
				if(!isNaN(data)){
					if(data == 0){ alert('Нельзя купить самого себя...'); }
					else if(data == 1){ alert('Ваш рейтинг не позволяет покупать рефералов...'); }
					else if(data == 2){ alert('Этот реферал уже забронирован кем то...'); }
					else if(data == 3){ alert('Вы превысили максимум для бронирования рефералов...'); }
					else if(data == 4){ alert('Этот реферал уже не продаётся...'); }
					else if(data == 5){ alert('Вы не можете купить этого реферала...'); }
					else if(data == 6){ alert('Это ваш реферал...'); }
					else if(data == 7){ alert('Невозможно забронировать, реферал учавствует в конкурсе своего реферера...'); }
					else if(data == 8){ alert('Ошибка! Обратитесь в техподдержку...'); }
					else{ alert('ERROR'); }
					
				}else{
					$('#notifications').html(basket_new); 
				}
			}
		});
	}
}

function up_ref_fair(id, pam){ 
	$.ajax({  type: "POST", url: "ajax/ajax_rest_sf.php", data: { 'sf' : 'up_ref_fair', 'id' : id, 'pam' : pam }, success: function(data){ $('#ok_up_r'+id).html(data); }  });
}

function gosale(id) 
{ 
	var price = $.trim($('#price'+id).val());
	if(price == ''){ alert('Укажите сумму...'); return false; }
	
	$.ajax({
		type: "POST", url: "ajax/ajax_rest_sf.php",  data: { 'sf' : 'gosale', 'id' : id, 'price' : price },   
		success: function(data){  
			if($.trim(data) == ''){ 
				//$('#exblock'+id).html("");
				document.getElementById('exblock'+id).style.display = 'none';
				$('#refbackok'+id).html("<span style='color: #b2beb5; font-size: 10px;'>Продаётся: <b>"+price+"</b></span>");	
				$('#ref_set'+id).html("<div class='m_rf_a' onClick=\"refback('"+id+"'); ex_menu('r_a"+id+"');\"; >Вернуть</div>");
			}else{ $('#echo_sale'+id).html(data); } }  
	});
	return false;	
}
function refback(id)
{ 
	$.ajax({
		type: "POST", url: "ajax/ajax_rest_sf.php", data: { 'sf' : 'refback', 'id' : id }, 
		success: function(data){ 
			$('#refbackok'+id).html("");
			$('#ref_set'+id).html("<div class='m_rf_a' onClick=\"ex_menu('exblock"+id+"'); ex_menu('r_a"+id+"');\" >Продать</div>");
		}
   });
}
function gorefbref(proc,id) 
{ 
	var tm;
    function slow()
    {
        $('#gorefbref'+id).fadeOut('slow');
		$('#gorefbref'+id).attr("style", "");
		$('#bonusadd'+id).fadeOut('slow');
		$('#bonusadd'+id).attr("style", "");
		$('#desk'+id).fadeOut('slow');
		$('#desk'+id).attr("style", "");
        if (tm) clearTimeout(tm);
    }
	function hide_all()
    {
		tm = setTimeout(function() { slow() }, 3000);
	}
	
	$.ajax({
		type: "POST",url: "ajax/ajax_rest_sf.php",data: { 'sf' : 'gorefbref', 'id' : id, 'proc' : proc },  
		 success: function (data)
		{
			if(data == 0){ $("#gorefbref"+id).html("<span class='msgbox-success'>Установлено "+proc+"0%</span>"); hide_all(); }
			else if(data == 1){ $("#gorefbref"+id).html("<span class='msgbox-error'>Вы не можете установить рефбек ниже того под который приглашали</span>");  hide_all(); }
		}
	});
}

function dellref(id){ 
	if (confirm('Вы уверены, что хотите удалить реферала?')){
		$.ajax({ type: "POST", url: "ajax/ajax_rest_sf.php",	data: { 'sf' : 'dellref', 'id' : id }, success: function(data) { if($.trim(data) != ''){ alert(data); }else{ $('#desk'+id).hide(); $('#reff'+id).hide(); } } });
	}
}
//Разбудить реферала
function refsend(id) 
{ 
	var message = $.trim($('#message_ref'+id).val());
	if(message == null){ alert('Укажите текст...'); return false; }
	else{
		$.ajax({ type: "POST", url: "ajax/ajax_rest_sf.php", data: { 'sf' : 'refsend', 'id' : id, 'message' : message }, success: function(data) { $('#refsend'+id).html(data); } });
	}
}
//Комент к рефералу
function сomment_ref(id) 
{ 
	var message = $.trim($('#message_comm'+id).val());
	$.ajax({ type: "POST", url: "ajax/ajax_rest_sf.php", data: { 'sf' : 'rmess_ref', 'id' : id, 'message' : message }, success: function(data) { $('#refcomm'+id).html(data); } });
}
//Оценка рассылки рефералам / Рассылка рефералам
function ref_send_price(type){ 
	$.ajax({
			type: "POST", url: "ajax/ajax_rest_sf.php", 
			data: { 'sf' : 'ref_send_price', 'type': type, 'online_rs' : $.trim($('#online_rs').val()), 'actref_rs' : $.trim($('#actref_rs').val()), 'actref100_rs' : $.trim($('#actref100_rs').val()), 'sex_r_rs' : $.trim($('#sex_r_rs').val()), 'message' : $.trim($('#message').val()), 'subject' : $.trim($('#subject').val()) },   
			success: function(data) { $('#echo_rs').html(data); }
		});
}

function procnew(){ 
	var proc=$.trim($('#procnew').val());
	$.ajax({ type: "POST", url: "ajax/ajax_rest_sf.php", data: { 'sf' : 'procnew', 'proc' : proc }, success: function(data) { $('#procok').html(data); } });
}
function procall() 
{ 
	var proc=$.trim($('#procall').val());
	$.ajax({ type: "POST", url: "ajax/ajax_rest_sf.php", data: { 'sf' : 'procall', 'proc' : proc }, success: function(data) { $('#procok').html(data); } });
}
//
//Ярмарка рефералов
function search_fair(pam) 
{ 
	var login_r = $.trim($('#login_r').val());
	var login_p = $.trim($('#login_p').val());
	var price_f_min = $.trim($('#price_f_min').val());
	var price_f_max = $.trim($('#price_f_max').val());

	if(pam == 1 && login_r != ''){
		$.ajax({
		type: "POST", url: "ajax/ajax_rest_sf.php", data: { 'sf' : 'search_fair', 'login_r' : login_r }, success: function(data) { if(data == 1){ alert('Реферал с таким ником не найден'); }else{ $('#search').html(data); } }
		});
	}else if(pam == 2 && login_p != ''){
		document.cookie="search_login_p="+login_p+"; path=/;";
		//location.reload();
		top.location = '/fair';
	}else if(pam == 3 && price_f_min != '' && price_f_max != ''){
		document.cookie="price_f_min="+price_f_min+"; path=/;";
		document.cookie="price_f_max="+price_f_max+"; path=/;";
		//location.reload();
		top.location = '/fair';
	}else if(pam == 4){
		document.cookie="sorting_f=def; path=/;";
		document.cookie="search_login_p=0; path=/;";
		document.cookie="price_f_min=; path=/;";
		document.cookie="price_f_max=; path=/;";
		document.cookie="only_today_f=; path=/;";
		//location.reload();
		top.location = '/fair';
	}
}

//Сортировки
function sorting_s(mode)
{
	document.cookie="sorting_s="+mode;
	top.location = '/work_surfing?go';
	return false;
}
function sorting_te(mode)
{
	document.cookie="sorting_te="+mode;
	top.location = '/work_tests?go';
	return false;
}
function sorting_te_cat()
{
	document.cookie="sorting_te_cat="+$.trim($('#type_test').val());
	top.location = '/work_tests?go';
	return false;
}
function sorting_f(mode)
{
	document.cookie="sorting_f="+mode;
	top.location = '/fair';
	return false;
}
function only_today_f(mode)
{
	document.cookie="only_today_f="+mode;
	top.location = '/fair';
	return false;
}
function sorting_r(mode)
{
	document.cookie="sorting_r="+mode;
	top.location = '/referals';
	return false;
}
function sorting_fru(mode)
{
	document.cookie="sorting_fru="+mode;
	top.location = '/friends';
	return false;
}
function sorting_fr(mode)
{
	document.cookie="sorting_fr="+mode;
	top.location = '/freeus';
	return false;
}
function wall_search()
{
	top.location = '/wall_user?is='+$.trim($('#name_wall').val());
	return false;
}
//Просмотры
function start_youtube_go(url, id){
	ws = window.open(url);
	ws.focus();
	$('#dyn_none'+id).html("<div style='margin: auto; color: #87a96b; font-size: 13pt; width: 300px; height: 35px; line-height: 35px; background: #d5e1cba8;'>Спасибо за просмотр</div>"); 
}
function start_surfing_go(id){
	$.ajax({  
		type: "POST", url: "/site_surfing/ajax/ajax_surfing_all.php", data: { 'sf' : 'viewing_surfing', 'id' : id }, 
		success: function(res){ 
			var r = JSON.parse(res);
			if(r.success){
				$('#res_views'+id).html("<div style='text-align: center;'>"+r.button+"</div>");
			}else if(r.error){
				$('#res_views'+id).html("<div style='text-align: center;'>"+r.text+"</div>"); 
			}
		}
	});
}
function start_mails_go(id){
	$.ajax({  
		type: "POST", url: "/site_mails/ajax/ajax_mails_all.php", data: { 'sf' : 'viewing_mails', 'id' : id }, 
		success: function(res){ 
			var r = JSON.parse(res);
			if(r.success){
				$('#res_views'+id).html("<div style='text-align: center;'>"+r.button+"</div>");
			}else if(r.error){
				$('#res_views'+id).html("<div style='text-align: center;'>"+r.text+"</div>"); 
			}
		}
	});
}
function start_surfing_result(id, url){
	window.open(url);
	$('#res_views'+id).html("<div style='text-align: center;'><div style='margin: auto; color: #87a96b; font-size: 13pt; width: 300px; height: 35px; line-height: 35px; background: #d5e1cba8;'>Спасибо за просмотр</div></div>"); 
}
function start_mails_test(id, url){
	window.open(url);
	$('#res_views'+id).html("<div style='text-align: center;'><div style='color: #87a96b; font-size: 13pt;'>Спасибо за заказ</div></div>"); 
}
function start_mails_result(id){
	$('#res_views'+id).html("<div style='text-align: center;'><div style='margin: auto; color: #87a96b; font-size: 13pt; width: 300px; height: 35px; line-height: 35px; background: #d5e1cba8;'>Спасибо за просмотр</div></div>"); 
}
function start_transitions(id){
	$.ajax({  
		type: "POST", url: "viewing_transitions.php", data: { 'sf' : 'start_transitions', 'id' : id }, 
		success: function(res){ 
			var r = JSON.parse(res);
			if(r.success){
				$('#res_views'+id).html("<div style='text-align: center;'>"+r.button+"</div>");
			}else if(r.error){
				$('#res_views'+id).html("<div style='text-align: center;'>"+r.text+"</div>"); 
			}
		}
	});
}
function start_transitions_result(id, price, url){
	window.open(url);
	$('#res_views'+id).html("<div style='text-align: center;'><div style='margin: auto; color: #87a96b; font-size: 13pt; width: 300px; height: 35px; line-height: 35px; background: #d5e1cba8;'>Оплата получена +"+price+" р. </div></div>"); 
}
function start_vkontakte(id, type){
	$.ajax({  
		type: "POST", url: "viewing_vkontakte.php", data: { 'sf' : 'start_vkontakte', 'type_y' : type, 'id' : id }, 
		success: function(data) { $('#start_vkontakte'+id).html("<div style='text-align: center;'>"+data+"</div>"); }  
	});
}
function start_vkontakte_result(id){
	$.ajax({  
		type: "POST", url: "site_vkontakte/ajax/ajax_vkontakte_pay.php", data: { 'sf' : 'viewing_vkontakte', 'id' : id }, 
		beforeSend: function(){ $('#start_vkontakte'+id).html("<div class='spinner_all'></div>"); },
		success: function(data) { $('#start_vkontakte'+id).html("<div style='text-align: center;'>"+data+"</div>"); }  
	});
}
function start_youtube(id, type){
	$.ajax({  
		type: "POST", url: "viewing_youtube.php", data: { 'sf' : 'start_youtube', 'type_y' : type, 'id' : id }, 
		success: function(data) { $('#start_youtube'+id).html("<div style='text-align: center;'>"+data+"</div>"); }  
	});
}
function start_youtube_result(id, type){
	$.ajax({  
		type: "POST", url: "site_youtube/ajax/ajax_youtube_pay.php", data: { 'sf' : 'viewing_youtube_l', 'type_y' : type, 'id' : id }, 
		beforeSend: function(){ $('#start_youtube'+id).html("<div class='spinner_all'></div>"); },
		success: function(data) { $('#start_youtube'+id).html("<div style='text-align: center;'>"+data+"</div>"); }  
	});
}
//Скрыть, раскрыть тест (Мусорка)
function st_view_test(id, pam){ 
	$.ajax({ 
		type: "POST", url: "ajax/ajax_tests.php", 
		data: { 'sf' : 'st_view_test', 'idrek' : id, 'pam' : pam }, 
		success: function(data) { if(pam != 3){ $('#rek_test'+id).hide(); } else if(pam == 3){ $('#sel_t'+id).html("<a class='ico_16_liked_a' ></a>"); } }
	});
}
//Показать поле жалоб
function com_send_e(id, type, pam){
	if (document.getElementById('comp'+id).style.display == 'none') { 
		document.getElementById('comp'+id).style.display = ''; 
		$.ajax({ type: "POST", url: "ajax/ajax_rest_sf.php", data: { 'sf' : 'com_send_e', 'id_rek' : id, 'type' : type, 'pam' : pam }, success: function(data) { 	$('#comp'+id).html(data); } });
	} else { document.getElementById('comp'+id).style.display = 'none'; }
}
/*
function info_surf_e(id, type, pam){
	if (document.getElementById('comp'+id).style.display == 'none') { 
		document.getElementById('comp'+id).style.display = ''; 
		$.ajax({ type: "POST", url: "ajax/ajax_rest_sf.php", data: { 'sf' : 'com_send_e', 'id_rek' : id, 'type' : type, 'pam' : pam }, success: function(data) { 	$('#comp'+id).html(data); } });
	} else { document.getElementById('comp'+id).style.display = 'none'; }
}
*/
function start_test(url,id)
{
	$.ajax({ type: "POST", url: "ajax/ajax_tests.php", data: { 'sf' : 'test_interval', 'id' : id } });
	ws = window.open(url);
	ws.focus();
}
function search_ref_m(m){
	if(m == 1){
		var login_referal = $.trim($('#login_referal').val());
		if(login_referal != null){
		top.location = '/referals?login_referal='+login_referal;
		}
	}else if(m == 2){
		var from_referal = $.trim($('#from_referal').val());
		if(from_referal != null){
		document.cookie="from_referal="+from_referal;
		top.location = '/referals';
		}
	}else if(m == 3){
		document.cookie="from_referal=";
		top.location = '/referals';
	}
}
//HEAD
function slow_eh(){ $('#entermsg').hide(); }
/*
function sendreiting(){ 
	$.ajax({ 
		type: "POST",url: "ajax/ajax_rest_sf.php",data: { 'sf' : 'rat' }, 
		success: function(data) { $('#reitinggo').hide(); $('#entermsg').html("<span class='msgbox-success'>Поздравляем! Вам зачислено +"+data+" рейтинга</span>"); setTimeout(function() { slow_eh() }, 3000); }  
		});
}
*/
function test_list_m_c(){$('#perf_b_us_list').fadeOut(300);}
function test_list_m(id)
{ 
	$.ajax({
		cache: false, type: 'POST', url: "infoall.php", data: "test_list_m=ok&id="+id,     
		success: function(data){ $('#perf_b_us_list').fadeIn(1).html(data); }
	});
}

//куки меню
function open_m_sf(id, title)
{
	var box = document.getElementById(id);

	if(box.style.display == 'none')
	{
		if(id == "m_bl2"){document.cookie="m_sf1=0; path=/;";}
		if(id == "m_bl3"){document.cookie="m_sf3=1; path=/;";}
		if(id == "m_bl4"){document.cookie="m_sf4=0; path=/;";}
		if(id == "m_bl5"){document.cookie="m_sf5=1; path=/;";}
		if(id == "m_bl2"){ $(title).removeClass('menu_us_tt_b').addClass('menu_us_t_b'); }else{ $(title).removeClass('menu_us_tt').addClass('menu_us_t'); }
		box.style.display = 'block'; if($(window).height() < 600){ var destination = jQuery('#sll_'+id).offset().top; jQuery("html,body").animate({scrollTop: destination}, 1000); }
	}else{
		if(id == "m_bl2"){document.cookie="m_sf1=1; path=/;";}
		if(id == "m_bl3"){document.cookie="m_sf3=0; path=/;";}
		if(id == "m_bl4"){document.cookie="m_sf4=1; path=/;";}
		if(id == "m_bl5"){document.cookie="m_sf5=0; path=/;";}
		if(id == "m_bl2"){ $(title).removeClass('menu_us_t_b').addClass('menu_us_tt_b'); }else{ $(title).removeClass('menu_us_t').addClass('menu_us_tt'); }
		box.style.display = 'none'; if($(window).height() < 600){ var destination = jQuery('#sll_m_bl2').offset().top; jQuery("html,body").animate({scrollTop: destination}, 1000); }
	}
}
//куки меню
function open_menu(id, m){
	var box = document.getElementById(id);
	if(box.style.display == 'none'){
		document.cookie=id+"=0; path=/;";
		box.style.display = 'block'; if($(window).height() < 600){ var destination = jQuery('#'+m).offset().top; jQuery("html,body").animate({scrollTop: destination}, 1000); }
	}else{
		document.cookie=id+"=1; path=/;";
		box.style.display = 'none'; if($(window).height() < 600){ var destination = jQuery('#sll_m_bl2').offset().top; jQuery("html,body").animate({scrollTop: destination}, 1000); }
	}
}
//Запуск онлайна
function online_s(t){ $.ajax({  type: 'POST', url: 'ajax/ajax_online.php', data: { 'sf' : 'online_s', 'type' : t } }); }
//Тест для выплат
function test_acc_pay(){
	$.ajax({ type: 'POST', url: 'ajax/ajax_profile.php', data: { 'sf' : 'test_acc_pay' }, success: function(data){ 
		if(data == 1){ location.reload(); }else if(data == 2){ $('#echo_pay_ok').html('Ваша заявка отправлена, ожидайте...<br/>Проверка займёт не более двух дней.'); }

	} });
}
//Анти бот
function go_verif_work(){
	$.ajax({ type: 'POST', url: 'ajax/ajax_rest_sf.php', data: { 'sf' : 'test_bot', 'am_vot_qp' : '2' }, success: function(data){ location.reload(); } });
}
//Часы
function clock_sf(){
	if($('#seconds').html() != null){
		clearInterval(clock_timer);
		sec+=1;
		if (sec>=60) { min+=1; sec=0; }
		if (min>=60) { hours+=1; min=0; }
		if (hours>=24) hours=0;
		if (sec<10)	{ sec2display = "0"+sec; }else{ sec2display = sec; }
		if (min<10) { min2display = "0"+min; }else{	min2display = min; }
		if (hours<10){ hour2display = "0"+hours; }else{ hour2display = hours; }

		$('#seconds').html(hour2display+":"+min2display+":"+sec2display);
		clock_timer = setTimeout("clock_sf();", 1000);
	}
}
//Калькулятор рефбека
function refbek_expect() 
{
	var price_r   = Number($.trim($('#price_r').val()));
	var refbek_r  = $.trim($('#refbek_r').val());
	var st_refe_r = $.trim($('#st_refe_r').val());
	var t_rek_r   = $.trim($('#t_rek_r').val());
	var ref_pro;
	
	if(price_r == '' || refbek_r == '' || st_refe_r == '' || t_rek_r == ''){ $('#refbek_expect').html('Не указан один из параметров.'); }else{
		if(price_r > 0){ 
			if(t_rek_r == 1){
				if(st_refe_r == 1){ ref_pro = 10; } //РАБОЧИЙ
				else if(st_refe_r == 2){ ref_pro = 20; } //ПРОДВИНУТЫЙ
				else if(st_refe_r == 3){ ref_pro = 30; } //БРИГАДИР
				else if(st_refe_r == 4){ ref_pro = 40; } //БИЗНЕСМЕН
				else if(st_refe_r == 5){ ref_pro = 50; } //МАГНАТ
				else if(st_refe_r == 6){ ref_pro = 60; } //ОЛИГАРХ
			}else if(t_rek_r == 2){
				if(st_refe_r == 1){ ref_pro = 1; } //РАБОЧИЙ
				else if(st_refe_r == 2){ ref_pro = 2; } //ПРОДВИНУТЫЙ
				else if(st_refe_r == 3){ ref_pro = 3; } //БРИГАДИР
				else if(st_refe_r == 4){ ref_pro = 5; } //БИЗНЕСМЕН
				else if(st_refe_r == 5){ ref_pro = 7; } //МАГНАТ
				else if(st_refe_r == 6){ ref_pro = 10; } //ОЛИГАРХ
			}
						
			//price_r  - цена ( например сёрфинг 0.022 )
			//refbek_r - установленый рефбек 
			//ref_pro  - процент отчисления ( берём с таблицы )

			refymoney   = price_r*ref_pro/100		 // сколько должен принести своему реферу 	
			refmoneyу   = refymoney*refbek_r/100; // возврат прибыли с того что принёс реферу 
			summaotrefb = refymoney-refmoneyу; // получит реферер
			my_money    = Number(((price_r*ref_pro/100)*refbek_r/100))+Number(price_r); // сколько получит исполнитель
			
			$('#refbek_expect').html('Вы получите: '+(my_money).toFixed(4)+' руб.<br/>Ваш реферер получит: '+(summaotrefb).toFixed(4)+'');
		}else{ $('#refbek_expect').html('Не указана цена, пример: 0.022'); }
	}
}
function refbek_expect2() 
{
	var price_r   = Number($.trim($('#price_r').val()));
	var refbek_r  = $.trim($('#refbek_r').val());
	var st_refe_r = $.trim($('#st_refe_r').val());
	var st_refe_my = $.trim($('#st_refe_my').val());
	var t_rek_r   = $.trim($('#t_rek_r').val());
	var ref_pro, ref_pro_my;
	
	if(price_r == '' || refbek_r == '' || st_refe_r == '' || t_rek_r == ''){ $('#refbek_expect').html('Не указан один из параметров.'); }else{
		if(price_r > 0){ 
			if(t_rek_r == 1){
				if(st_refe_r == 1){ ref_pro = 10; } //РАБОЧИЙ
				else if(st_refe_r == 2){ ref_pro = 20; } //ПРОДВИНУТЫЙ
				else if(st_refe_r == 3){ ref_pro = 30; } //БРИГАДИР
				else if(st_refe_r == 4){ ref_pro = 40; } //БИЗНЕСМЕН
				else if(st_refe_r == 5){ ref_pro = 50; } //МАГНАТ
				else if(st_refe_r == 6){ ref_pro = 60; } //ОЛИГАРХ
			}else if(t_rek_r == 2){
				if(st_refe_r == 1){ ref_pro = 1; } //РАБОЧИЙ
				else if(st_refe_r == 2){ ref_pro = 2; } //ПРОДВИНУТЫЙ
				else if(st_refe_r == 3){ ref_pro = 3; } //БРИГАДИР
				else if(st_refe_r == 4){ ref_pro = 5; } //БИЗНЕСМЕН
				else if(st_refe_r == 5){ ref_pro = 7; } //МАГНАТ
				else if(st_refe_r == 6){ ref_pro = 10; } //ОЛИГАРХ
			}
			
			if(t_rek_r == 1){
				if(st_refe_my == 1){ my_pro = 10; } //РАБОЧИЙ
				else if(st_refe_my == 2){ my_pro = 20; } //ПРОДВИНУТЫЙ
				else if(st_refe_my == 3){ my_pro = 30; } //БРИГАДИР
				else if(st_refe_my == 4){ my_pro = 40; } //БИЗНЕСМЕН
				else if(st_refe_my == 5){ my_pro = 50; } //МАГНАТ
				else if(st_refe_my == 6){ my_pro = 60; } //ОЛИГАРХ
			}else if(t_rek_r == 2){
				if(st_refe_my == 1){ my_pro = 1; } //РАБОЧИЙ
				else if(st_refe_my == 2){ my_pro = 2; } //ПРОДВИНУТЫЙ
				else if(st_refe_my == 3){ my_pro = 3; } //БРИГАДИР
				else if(st_refe_my == 4){ my_pro = 5; } //БИЗНЕСМЕН
				else if(st_refe_my == 5){ my_pro = 7; } //МАГНАТ
				else if(st_refe_my == 6){ my_pro = 10; } //ОЛИГАРХ
			}
			
			//if(my_pro > ref_pro){ my_pro = ref_pro; }
				/*
			refymoney   = price_r*ref_pro/100;  // сколько должен принести своему реферу  
			refmoneyу   = refymoney*(refbek_r/100)*my_pro/ref_pro; // возврат прибыли с того что принёс реферу 
			summaotrefb = refymoney-refmoneyу; // получит реферер
			my_money    = refmoneyу+price_r; // сколько получит исполнитель
			
			*/
			refmoneyу   = price_r*refbek_r*my_pro*0.0001; // рефбек от своего статуса
			my_money    = refmoneyу+price_r; // сколько получит исполнитель
			
			if(my_pro < ref_pro){ 
				summaotrefb = price_r*ref_pro/100-refmoneyу; 
			}else{
				summaotrefb = price_r*(ref_pro/100)*(1-refbek_r/100);
			}
			// получит реферер


			$('#refbek_expect2').html('Вы получите: '+(my_money).toFixed(4)+' руб.<br/>Ваш реферер получит: '+(summaotrefb).toFixed(4)+'<br/>Общая сумма: '+(my_money+summaotrefb).toFixed(4)+'');
		}else{ $('#refbek_expect2').html('Не указана цена, пример: 0.022'); }
	}

}
// Топ юзеров
function top_load(pam, page){ 
	$('#loading').show();
	var am;
	if(page == 1){ am = 9; }
	else if(page == 2){ am = 8; }
	$.ajax({ 
		type: "POST", url: "top.php", data: { 'sf' : 'top_load', 'pam' : pam, 'page' : page },
		success: function (data){ 
	
			for (i=1; i <= am; i++){ document.getElementById('top_'+i).className = 'muspt'; }
			document.getElementById(pam).className = 'muspt_a';
	
			$('#load_page_top').html(data); 
			$('#loading').hide();
			online_views();
			online_us_go();
		}
	});
}
//Вывод жалоб
function comp_views(id,titrek){
	if (document.getElementById('comp_views'+id).style.display == 'none'){
		$.post('ajax/ajax_manage_adv.php?sf=comp_views', { idrek: id, titrek: titrek }, function(data) { document.getElementById('comp_views'+id).style.display = ''; $('#comp_views'+id).html(data); });  
	}else{ document.getElementById('comp_views'+id).style.display = 'none'; }
}
//Загрузка цепочки
function load_chainlet(id){ 
	$.ajax({ type: 'POST', url: 'ajax/ajax_chainlet.php', data: { 'sf' : 'load_chainlet', 'id' : id }, success: function(data){ $('#load_chainlet_e').html(data); } });
}

function a_fix_surf(url){ 
	window.top.location.replace(url);
	//$.ajax({ type: 'POST', url: 'ajax/ajax_surfing.php', data: { 'sf' : 'a_fix_surf'}, success: function(data) { window.top.location.replace(url); } });  
	return false; 
}

/*КОНКУРСЫ ОТ РЕФЕРЕРА*/
//Запуск конкурса
function included_com(id)
{
	$.ajax({
		url: 'ajax/ajax_competition.php',  type: 'POST', data: { 'sf' : 'included_com', 'id_com' : id, }, 
		success: function (data){ 
			if($.trim(data) == "ok"){
				$('#included_com_e'+id).hide(); 
				$('#included_com'+id).html("<a class='start_cm_a'></a>"); 
				$('#included_com_n'+id).html("<span class='status_com1' title='Конкурс активен'>Активный</span>"); 
				$('#balance_com'+id).html($('#balance_com_s'+id).val());
			}else{
				document.getElementById('included_com_e'+id).style.display = '';
				$('#included_com_e'+id).html(data); 
			}
		}
	});
}
//Удалить конкурс
function del_competition(id){
	if(confirm('Вы уверены что хотите удалить конкурс ID:'+id+' с возвратом средств на рекламный счёт?')){
	$.ajax({
		url: 'ajax/ajax_competition.php',  type: 'POST', data: { 'sf' : 'del_competition', 'id_com' : id, }, success: function (data){ if(data == 0){ $('#del_com'+id).hide(); }else { $('#del_com'+id).html(data); } }
	});
	}
}
//Пополнение баланса
function payment_com(id){
	var price = $.trim($('#price'+id).val());
	var pay_m = $.trim($('#pay_m'+id).val());
	$.ajax({
		url: 'ajax/ajax_competition.php',  type: 'POST', data: { 'sf' : 'payment_com', 'id_com' : id, 'pay_m' : pay_m, 'price' : price }, 
		success: function (data){ if(data == 0){ $('#balance_com'+id).html(price); $('#button'+id).html(er_e+'Готово'+span); }else{ $('#info'+id).html(data); } }
	});
}
//Автозапуск
function repeat_ico(id){
	$.ajax({ type: "POST",url: "ajax/ajax_competition.php",data: { 'sf' : 'repeat_ico', 'id' : id },  success: function(data){ $('#repeat_ico_e'+id).html(data); } });
	return false;
}
//Калькуляторы
function calc_price(id, pr_r){
	var summ;
	var am    = $.trim($('#am'+id).val());
	summ = Number(am) * Number(pr_r);
	if(summ < 0){ summ = 0; }
	$('#price'+id).val(summ);
	$('#com_pross'+id).html(Number(summ) + (Number(summ) * (Number(com_pross)/ 100)));
}
function calc_amount(id, pr_r){
	var summ;
	var price = $.trim($('#price'+id).val());
	summ = RoundTo((Number(price) / Number(pr_r))-pr_r, 0);
	if(summ < 0){ summ = 0; }
	$('#am'+id).val(summ);
	$('#com_pross'+id).html(Number(price) + (Number(price) * (Number(com_pross)/ 100)));
}
//Выгрузка описания и результатов
function result_load(id, cat){ 
	if (document.getElementById('participants'+id).style.display == 'none') {
		document.getElementById('participants'+id).style.display = '';
		$('#descr_comp'+id).html(e_com[cat]);
		$.ajax({ type: "POST",url: "ajax/ajax_competition.php",data: { 'sf' : 'result_load', 'id' : id }, success: function(data){ $('#result_load'+id).html(data); } });
	}else{ document.getElementById('participants'+id).style.display = 'none'; } 
	return false;  
}
/*КОНЕЦ*/
/*ВЫКУП У РЕФЕРЕРА*/
function redemption(){ 
	var price_r = $.trim($('#price_r').val());
	if(price_r == ''){ alert('Укажите сумму выкупа...'); return false; }else{
		$.ajax({ type: 'POST', url: 'ajax/ajax_redemption.php', data: { 'sf' : 'redemption', 'price_r' : price_r } , success: function(data) { alert(data); } });
	}
}
function away_rem(id_rem){ 
	$.ajax({ 
		type: 'POST', url: 'ajax/ajax_redemption.php', 
		data: { 'sf' : 'away_rem', 'id_rem' : id_rem }, 
		success: function(data){ 
			if(data == 0){ $('#rem_list'+id_rem).hide(); }else{ alert(data); } 
			} 
		}); 
	}
function decline_rem(id_rem){ 

	var res_dec = window.prompt("Укажите причину","");

	$.ajax({ 
		type: 'POST', url: 'ajax/ajax_redemption.php', 
		data: { 'sf' : 'decline_rem', 'id_rem' : id_rem, 'res_dec' : res_dec }, 
		success: function(data){ 
			if(data == 0){ $('#rem_list'+id_rem).hide(); }else{ alert(data); } 
			} 
		}); 
	}
function approve_rem(id_rem){ 

	if (confirm('Вы уверены, что хотите одобрить заявку?'))
	{

	$.ajax({ 
		type: 'POST', url: 'ajax/ajax_redemption.php', 
		data: { 'sf' : 'approve_rem', 'id_rem' : id_rem }, 
		success: function(data){ 
			if(data == 0){ $('#rem_list'+id_rem).hide(); }else{ alert(data); } 
			} 
		}); 
	}
}
/*КОНЕЦ*/
/*Белый список заданий*/
function white_l(id, pam){ 

	if(id == '0'){ id_us = $.trim($('#id_white_l').val()) }
	else{ id_us = id } 

	$.ajax({ 
		type: "POST",url: "ajax/ajax_rest_sf.php", data: { 'sf' : 'white_l', 'pam' : pam, 'id_us' : id_us }, 
		success: function(data){ 
			if(pam == 0){ alert(data); }
			else if(pam == 1){ $('#white_l_d'+id_us).hide(); }
		} 
	});
}
/*КОНЕЦ*/
/*Блок IP площадок*/
function block_ip_add(ip, pam){
	var ip_block;
	if(pam == '0'){ ip_block = $.trim($('#ip_block').val()); }
	else if(pam == '1'){ ip_block = ip; }
	$.ajax({ 
		type: "POST",url: "ajax/ajax_rest_sf.php", data: { 'sf' : 'block_ip_add', 'ip_block' : ip_block, 'pam' : pam }, 
		success: function(data){
			if($.trim(data) == ''){
				list_block_ip();
			}else{
				alert(data);
			}
		} 
	});
}
/*Блок IDUS all*/
function block_user_all(id_b, idad, pam, type, type_a){ 
	$.ajax({ 
		type: "POST",url: "/ajax/ajax_manage_adv.php", data: { 'sf' : 'block_user_all', 'id_b' : id_b, 'idad' : idad, 'pam' : pam, 'type' : type, 'type_a' : type_a }, 
		success: function(data){ $('#button_y'+id_b).html(''); if($.trim(data) != ''){ alert(data); } } 
	}); 
}
/*КОНЕЦ*/

function auth_soc_add_c(){$('#infoall').fadeOut(300);}
function auth_soc_add(id){ $.ajax({ type: 'POST', url: "infoall.php", data: { 'sf' : 'auth_soc' }, success: function(data){ $('#infoall').fadeIn(1).html(data); } }); }

/*КАРУСЕЛЬ*/
//Обработка клика на стрелку вправо
$(document).on('click', ".carousel_button_right",function(){ 
	var carusel = $(this).parents('.carousel');
	right_carusel(carusel);
	return false;
});
//Обработка клика на стрелку влево
$(document).on('click',".carousel_button_left",function(){ 
	var carusel = $(this).parents('.carousel');
	left_carusel(carusel);
	return false;
});
function left_carusel(carusel){
   var block_width = $(carusel).find('.carousel_block').outerWidth();
   $(carusel).find(".carousel_items .carousel_block").eq(-1).clone().prependTo($(carusel).find(".carousel_items")); 
   $(carusel).find(".carousel_items").css({"left":"-"+block_width+"px"});
   $(carusel).find(".carousel_items .carousel_block").eq(-1).remove();    
   $(carusel).find(".carousel_items").animate({left: "0px"}, 500); 
   
}
function right_carusel(carusel){
   var block_width = $(carusel).find('.carousel_block').outerWidth();
   $(carusel).find(".carousel_items").animate({left: "-"+ block_width +"px"}, 500, function(){
	  $(carusel).find(".carousel_items .carousel_block").eq(0).clone().appendTo($(carusel).find(".carousel_items")); 
      $(carusel).find(".carousel_items .carousel_block").eq(0).remove(); 
      $(carusel).find(".carousel_items").css({"left":"0px"}); 
   }); 
}
//автоматическая прокрутка карусели
$(function(){ auto_right('.carousel:first'); });
// Автоматическая прокрутка
function auto_right(carusel){ setInterval(function(){ if (!$(carusel).is('.hover')) right_carusel(carusel); }, 2000); }
// Навели курсор на карусель
$(document).on('mouseenter', '.carousel', function(){ $(this).addClass('hover') });
//Убрали курсор с карусели
$(document).on('mouseleave', '.carousel', function(){ $(this).removeClass('hover') });
/*КОНЕЦ*/
/*статистика действий по заработку*/

function statistics_actions(){
	if('https://seo-fast.ru/' == document.location.href || 'https://seo-fast.ru/main' == document.location.href){
		$.ajax({ 
			type: "POST", url: "ajax/ajax_statistics.php", 
			data: { 'sf' : 'statistics_actions' }, success: function(res){ $('#statistics_actions').html(res); setTimeout(statistics_actions,5000); }
		});
	}else{
		clearTimeout(statistics_actions);
	}
}

/*статистика денег*/
function statistics_money(){
	if('https://seo-fast.ru/' == document.location.href || 'https://seo-fast.ru/main' == document.location.href){
		$.ajax({ 
			type: "POST", url: "ajax/ajax_statistics.php", 
			data: { 'sf' : 'statistics_money', 'sm_id' : $('#sm_id').val() },
			success: function(res){ 
				var r = JSON.parse(res);
				if(r.success){
					$('#sm_id').val(r.sm_id); 
					$("#statistics_money").prepend(r.text); 
				}else if(r.error){
					$('#sm_id').val(r.sm_id); 
				}
				setTimeout(statistics_money,5000);
			} 
		});
	}else{
		clearTimeout(statistics_money);
	}
}

/*КОНЕЦ*/
/*Модальные окна*/
var go_move = go_anima = false;
function closed_popup(pam){
	$('#load_popup, #popup').css('display','none');
	localStorage.removeItem('popup_t_'+pam);
	localStorage.removeItem('popup_l_'+pam);
	localStorage.removeItem('popup_see_'+pam);
	$('#popup').remove();
}
function popup_sf(title, animate, width, data_post, data_url){
	go_anima = (animate)? true : false ;
	var css_overflow = '';
	var pam_popup;
	var div_popup = 'load_popup';
	
	if(go_anima){
		$('#load_popup').stop().animate({opacity: 'show'}, 200, "linear"); 
	}else{ 
		$('#load_popup').css({display:'block'}); 
	}
	if($('div').is('#popup')){ $('#popup').remove(); }

	var popup = '<div id="popup" style="width:'+width+'px;">';
	popup += '<div class="block-popup">';
	popup += "<span class='closed-popup ico_16_delete' onclick=\"closed_popup('"+data_post+"');\"></span>";
	popup += '<div class="title-popup">'+title+'</div>';
	popup += '<div class="text-popup" id="js-popup">';
	popup += '<img src="/css/img/loader/ajax-loader.gif" class="load-popup">';
	popup += '</div>';
	popup += '</div>';
	popup += '</div>';
	
	$('body').append(popup);
	left_s = ($(window).width() - $('#popup').outerWidth())/2 + $(window).scrollLeft();
	$('#popup').css({position:'fixed', left: left_s + 'px',top: -700 + 'px', display:'block'});   
	$(window).resize(function(){
		
		var top_user  = localStorage.getItem('popup_t_'+data_post);
		var left_user = localStorage.getItem('popup_l_'+data_post);
		if(top_user != null && left_user != null){ 
			top_s = top_user;
			left_s = left_user;
		}else{
			left_s = ($(window).width() - $('#popup').outerWidth())/2 + $(window).scrollLeft();
			top_s = 50;	
		}
		
		if(go_anima){
			$('#popup').stop().animate({position:'fixed', top: top_s + "px", left: left_s + 'px'}, 200, "linear");
		}else{
			$('#popup').css({position:'fixed', top: top_s + "px", left: left_s + 'px'});
		}
	}); 
	$(window).resize();
	$.ajax({
		url: data_url, 
		type: 'POST', 
		data: data_post,
		error: function (){ $('#js-popup').html(js_e+'Не удалось выполнить запрос!'+span); },
		success: function (data){ $('#js-popup').html(data); }
	});
	$('#popup').on('mousedown', '.title-popup', function(e) {
		go_move = true;
		go_start_x = e.offsetX==undefined?e.layerX:e.offsetX;
		go_start_y = e.offsetY==undefined?e.layerY:e.offsetY;
		$('#popup').css('opacity','0.5');
		
	});
	$(document).bind('mousemove', function(e) {
		if(go_move){
			var x = $('#popup').position().left,
			pageY_p = e.pageY,
			pageX_p = e.pageX,
			y = $('#popup').position().top,
			margin_x = $('#popup').css('margin-left'),
			margin_y = $('#popup').css('margin-top');
			margin_x = parseInt(margin_x.replace(/\D+/g,""));
			margin_y = parseInt(margin_y.replace(/\D+/g,""));

			if(pageY_p > $(window).height()){ pageY_p = 50; }
			if(pageX_p > $(window).width()){ pageX_p = 400; }

			$('#popup').css({ left:(pageX_p-go_start_x-margin_x)+'px', top: (pageY_p-go_start_y-margin_y)+'px' });
			//console.log("Высота: "+pageY_p+" Ширина: "+pageX_p+" Высота: "+$(window).height()+" Ширина: "+$(window).width()+" >>>> "+go_start_x+" "+go_start_y+" "+margin_y+" "+margin_x);

			localStorage.setItem('popup_t_'+data_post, (pageY_p-go_start_y-margin_y));
			localStorage.setItem('popup_l_'+data_post, (pageX_p-go_start_x-margin_x));
		}
	});
	$(document).bind('mouseup', function() { go_move = false; $('#popup').css('opacity','1'); });
	localStorage.setItem('popup_see_'+data_post, true);
}
function closed_popup_list(pam){
	$('#load_popup_list, #popup_list').css('display','none');
	localStorage.removeItem('popup_t_'+pam);
	localStorage.removeItem('popup_l_'+pam);
	localStorage.removeItem('popup_see_'+pam);
	$('#popup_list').remove();
}
function popup_sf_list(title, animate, width, data_post, data_url){
	go_anima = (animate)? true : false ;
	var css_overflow = '';
	var pam_popup;
	var div_popup = 'load_popup_list';
	
	if(go_anima){
		$('#load_popup_list').stop().animate({opacity: 'show'}, 200, "linear"); 
	}else{ 
		$('#load_popup_list').css({display:'block'}); 
	}
	if($('div').is('#popup_list')){ $('#popup_list').remove(); }

	var popup = '<div id="popup_list" style="width:'+width+'px;">';
	popup += '<div class="block-popup">';
	popup += "<span class='closed-popup ico_16_delete' onclick=\"closed_popup_list('"+data_post+"');\"></span>";
	popup += '<div class="title-popup">'+title+'</div>';
	popup += '<div class="text-popup" id="popup_content_list">';
	popup += '<img src="/css/img/loader/ajax-loader.gif" class="load-popup">';
	popup += '</div>';
	popup += '</div>';
	popup += '</div>';
	
	$('body').append(popup);
	left_s = ($(window).width() - $('#popup_list').outerWidth())/2 + $(window).scrollLeft();
	$('#popup_list').css({position:'fixed', left: left_s + 'px',top: -700 + 'px', display:'block'});   
	$(window).resize(function(){
		
		var top_user  = localStorage.getItem('popup_t_'+data_post);
		var left_user = localStorage.getItem('popup_l_'+data_post);
		if(top_user != null && left_user != null){ 
			top_s = top_user;
			left_s = left_user;
		}else{
			left_s = ($(window).width() - $('#popup_list').outerWidth())/2 + $(window).scrollLeft();
			top_s = 50;	
		}
		
		if(go_anima){
			$('#popup_list').stop().animate({position:'fixed', top: top_s + "px", left: left_s + 'px'}, 200, "linear");
		}else{
			$('#popup_list').css({position:'fixed', top: top_s + "px", left: left_s + 'px'});
		}
	}); 
	$(window).resize();
	$.ajax({
		url: data_url, 
		type: 'POST', 
		data: data_post,
		error: function (){ $('#popup_content_list').html(js_e+'Не удалось выполнить запрос!'+span); },
		success: function (data){ $('#popup_content_list').html(data); }
	});
	$('#popup_list').on('mousedown', '.title-popup', function(e) {
		go_move = true;
		go_start_x = e.offsetX==undefined?e.layerX:e.offsetX;
		go_start_y = e.offsetY==undefined?e.layerY:e.offsetY;
		$('#popup_list').css('opacity','0.5');
		
	});
	$(document).bind('mousemove', function(e) {
		if(go_move){
			var x = $('#popup_list').position().left,
			pageY_p = e.pageY,
			pageX_p = e.pageX,
			y = $('#popup_list').position().top,
			margin_x = $('#popup_list').css('margin-left'),
			margin_y = $('#popup_list').css('margin-top');
			margin_x = parseInt(margin_x.replace(/\D+/g,""));
			margin_y = parseInt(margin_y.replace(/\D+/g,""));

			if(pageY_p > $(window).height()){ pageY_p = 50; }
			if(pageX_p > $(window).width()){ pageX_p = 400; }

			$('#popup_list').css({ left:(pageX_p-go_start_x-margin_x)+'px', top: (pageY_p-go_start_y-margin_y)+'px' });
			//console.log("Высота: "+pageY_p+" Ширина: "+pageX_p+" Высота: "+$(window).height()+" Ширина: "+$(window).width()+" >>>> "+go_start_x+" "+go_start_y+" "+margin_y+" "+margin_x);

			localStorage.setItem('popup_t_'+data_post, (pageY_p-go_start_y-margin_y));
			localStorage.setItem('popup_l_'+data_post, (pageX_p-go_start_x-margin_x));
		}
	});
	$(document).bind('mouseup', function() { go_move = false; $('#popup_list').css('opacity','1'); });
	localStorage.setItem('popup_see_'+data_post, true);
}
function closed_popup_chat_sf(pam){
	$('#load_popup_chat, #popup_chat_sf').css('display','none');
	localStorage.removeItem('popup_t_popup_chat_sf');
	localStorage.removeItem('popup_l_popup_chat_sf');
	localStorage.removeItem('popup_see_popup_chat_sf');
	$('#popup_chat_sf').remove();
}
function popup_chat_sf(){
	var pam_popup = 'popup_chat_sf';
	
	$('#load_popup_chat').css({display:'block'}); 
	if($('div').is('#popup_chat_sf')){ $('#popup_chat_sf').remove(); }

	var popup = '<div id="popup_chat_sf" style="width: 400px;">';
	popup += '<div class="block-popup">';
	popup += "<span class='closed-popup ico_16_delete' onclick=\"closed_popup_chat_sf('');\"></span>";
	popup += '<div class="title-popup">Чат (бета)</div>';
	popup += '<div class="text-popup" id="popup_content_chat_sf">';
	popup += '<img src="/css/img/loader/ajax-loader.gif" class="load-popup">';
	popup += '</div>';
	popup += '</div>';
	popup += '</div>';
	
	$('body').append(popup);
	left_s = ($(window).width() - $('#popup_chat_sf').outerWidth())/2 + $(window).scrollLeft();
	$('#popup_chat_sf').css({position:'fixed', left: left_s + 'px',top: -700 + 'px', display:'block'});   
	$(window).resize(function(){
		
		var top_user  = localStorage.getItem('popup_t_'+pam_popup);
		var left_user = localStorage.getItem('popup_l_'+pam_popup);
		if(top_user != null && left_user != null){ 
			top_s = top_user;
			left_s = left_user;
		}else{
			left_s = ($(window).width() - $('#popup_chat_sf').outerWidth())/2 + $(window).scrollLeft();
			top_s = 100;	
		}
		
		$('#popup_chat_sf').css({position:'fixed', top: top_s + "px", left: left_s + 'px'});
	}); 
	$(window).resize();
	$.ajax({
		url: '/chat_sf_popup.php', 
		type: 'POST', 
		data: { 'chat_sf' : 'ok' },
		error: function (){ $('#popup_content_chat_sf').html(js_e+'Не удалось выполнить запрос!'+span); },
		success: function (data){ $('#popup_content_chat_sf').html(data); }
	});
	$('#popup_chat_sf').on('mousedown', '.title-popup', function(e) {
		go_move = true;
		go_start_x = e.offsetX==undefined?e.layerX:e.offsetX;
		go_start_y = e.offsetY==undefined?e.layerY:e.offsetY;
		$('#popup_chat_sf').css('opacity','0.3');
		
	});
	$(document).bind('mousemove', function(e) {
		if(go_move){
			var x = $('#popup_chat_sf').position().left,
			pageY_p = e.pageY,
			pageX_p = e.pageX,
			y = $('#popup_chat_sf').position().top,
			margin_x = $('#popup_chat_sf').css('margin-left'),
			margin_y = $('#popup_chat_sf').css('margin-top');
			margin_x = parseInt(margin_x.replace(/\D+/g,""));
			margin_y = parseInt(margin_y.replace(/\D+/g,""));

			if(pageY_p > $(window).height()){ pageY_p = 100; }
			if(pageX_p > $(window).width()){ pageX_p = 400; }

			$('#popup_chat_sf').css({ left:(pageX_p-go_start_x-margin_x)+'px', top: (pageY_p-go_start_y-margin_y)+'px' });
			//console.log("Высота: "+pageY_p+" Ширина: "+pageX_p+" Высота: "+$(window).height()+" Ширина: "+$(window).width()+" >>>> "+go_start_x+" "+go_start_y+" "+margin_y+" "+margin_x);

			localStorage.setItem('popup_t_'+pam_popup, (pageY_p-go_start_y-margin_y));
			localStorage.setItem('popup_l_'+pam_popup, (pageX_p-go_start_x-margin_x));
		}
	});
	$(document).bind('mouseup', function() { go_move = false; $('#popup_chat_sf').css('opacity','0.9'); });
	localStorage.setItem('popup_see_'+pam_popup, true);
}
/*КОНЕЦ*/
/*ОТправка письма рефереру*/
function refesend(){ 
	$.ajax({ type: "POST",url: "ajax/ajax_rest_sf.php", data: { 'sf' : 'refesend', 'message' : $.trim($('#ref_message').val()) }, success: function(data) { $('#refesend').html(data); }	});
}
/*Поиск сайта в ЧС*/
function search_url(){ 
	$.ajax({ type: "POST", url: "ajax/ajax_rest_sf.php", data: { 'sf' : 'search_url', 'search_url' : $.trim($('#search_url').val()) }, success: function(data){ $('#hide_search_url').hide(); $('#echo_search_url').html(data); } });
}
/*Тест на запрет всплывающих окон*/
function window_adv(){
	c=window.open("https://seo-fast.ru");
	if(c){
		c.close(); // закрываю окно
		$.cookie('window_adv', true, { expires: 7, path: '/', }); //создаём куку
	}else{
		$('#window_adv').css('display','block');
		$('.list_rek_table').css('display','none');
	}
}

/*Куки*/
jQuery.cookie = function(name, value, options) {
    if (typeof value != 'undefined') { // name and value given, set cookie
        options = options || {};
        if (value === null) {
            value = '';
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
        }
        // CAUTION: Needed to parenthesize options.path and options.domain
        // in the following expressions, otherwise they evaluate to undefined
        // in the packed version for some reason...
        var path = options.path ? '; path=' + (options.path) : '';
        var domain = options.domain ? '; domain=' + (options.domain) : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else { // only name given, get cookie
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
};
(function () {
    document.addEventListener("DOMContentLoaded", function () {

        var showingTooltip;

        document.addEventListener("mouseover", function(e) {
            var target = e.target;

            var tooltip_sf = target.getAttribute('data-tooltip_sf');
            if (!tooltip_sf) {
                while (target != this) {
                    if (target.hasAttribute('data-tooltip_sf')) {
                        tooltip_sf = target.getAttribute('data-tooltip_sf');
                        break;
                    }
                    target = target.parentNode;
                }
            }

            if (!tooltip_sf) return;

            var tooltipElem = document.createElement('div');
            tooltipElem.className = 'tooltip_sf';
            tooltipElem.innerHTML = tooltip_sf;
            document.body.appendChild(tooltipElem);

            var coords = target.getBoundingClientRect();

            var left = coords.left + (target.offsetWidth - tooltipElem.offsetWidth) / 2;
            if (left < 0) left = 0; // не вылезать за левую границу окна
            var right = coords.right - (target.offsetWidth - tooltipElem.offsetWidth) / 2;
            if (right > document.documentElement.clientWidth) right = 0; // не вылезать за правую границу окна

            var top = coords.top - tooltipElem.offsetHeight - 5;
            if (top < 0) { // не вылезать за верхнюю границу окна
                top = coords.top + target.offsetHeight + 5;
            }

            if (right === 0) {
                tooltipElem.style.right = right + 'px';
            } else if (left === 0 || left > 0) {
                tooltipElem.style.left = left + 'px';
            }
            tooltipElem.style.top = top + 'px';

            showingTooltip = tooltipElem;
        });

        document.addEventListener("mouseout", function(e) {

            if (showingTooltip) {
                document.body.removeChild(showingTooltip);
                showingTooltip = null;
            }

        });
        document.addEventListener("click", function(e) {

            if (showingTooltip) {
                document.body.removeChild(showingTooltip);
                showingTooltip = null;
            }

        });
    });
}());