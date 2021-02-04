  var status_form = select_status = 0;
var go_move = go_anima = false;
var funcjs = [];


function hideserfaddblock(bname)
{
    if (document.getElementById(bname).style.display == 'none')
	{
        document.getElementById(bname).style.display = '';
    }
	else
	{
        document.getElementById(bname).style.display = 'none';
    }
    return false;
}

            function tack(x){ 
if (document.getElementById(x).style.display == 'none') { 
document.getElementById(x).style.display = ''; 
} else { document.getElementById(x).style.display = 'none'; } 
return false;  
}
  
  
  
  $(document).ready(function(){
	
    $('.cash-start').unbind().click(function(){
	  var system = $(this).data('system'),
	      dopsystem = $(this).data('dop');
	  
	  if(system > 0){
	    $('#inp-system').val(system);  	
	  }
	  
	  if(dopsystem){
	    $('#dop-inp-system').val(dopsystem);  	
	  }else{
	    $('#dop-inp-system').val('');	
	  }
	  
	  $('.aj-form-add, #aj-form-banner').submit();
	  return false;
	  	
	});
	
	$('#2').unbind();
    $('#1').unbind();
    
    $('body').on('click','#2',function(){ $('#country').fadeIn('slow'); });
    $('body').on('click','#1',function(){ $('#country').fadeOut('slow'); $('input[name="country"]').attr('value',''); });
    	
    
    $('input[name="gorod[]"]').unbind();
    
    $('body').on('click','input[name="gorod[]"]',function(){
      var par = $(this).parent().children('input[name="country[]"]');
      par.prop({"checked":false});	
    });

    $('input[name="country[]"]').unbind();
    
    $('body').on('click','input[name="country[]"]',function(){
      var par = $(this).parent().children('input[name="gorod[]"]');
      par.prop({"checked":false});	
    });
    
    $(".plus").unbind().click( function() {
      if($('.vopros4').css('display') == 'none'){
        $('.vopros4').animate({ opacity: 'show' }, "slow", function(){funcjs['calczacaz']($('form[name="formzakaz"]')[0]);});
      }else{
        $('.vopros5').animate({ opacity: 'show' }, "slow", function(){funcjs['calczacaz']($('form[name="formzakaz"]')[0]);});
        $('.tr-plus').css('display','none');
      }
      return false;
    });
  
    $(".minus").unbind().click( function() {
      if($('.vopros5').css('display') == 'none'){
        $('.vopros4, .vopros4_dop').animate({ opacity: 'hide' }, "slow", function(){funcjs['calczacaz']($('form[name="formzakaz"]')[0]);});
        $('.vopros4 input, .vopros4_dop input').val('');
      }else{
        $('.vopros5, .vopros5_dop').animate({ opacity: 'hide' }, "slow", function(){$('.tr-plus').css('display','');funcjs['calczacaz']($('form[name="formzakaz"]')[0]);});
        $('.vopros5 input, .vopros5_dop input').val('');
      }
      return false;
    });

  });
  
  funcjs['appendtag'] = function(text1, text2){
     if ((document.selection)){
         document.formzakaz.ask_desc.focus();
         document.formzakaz.document.selection.createRange().text = text1+document.formzakaz.document.selection.createRange().text+text2;
     } else if(document.formzakaz.ask_desc.selectionStart != undefined) {
         var element    = document.formzakaz.ask_desc;
         var str     = element.value;
         var start    = element.selectionStart;
         var length    = element.selectionEnd - element.selectionStart;
         element.value = str.substr(0, start) + text1 + str.substr(start, length) + text2 + str.substr(start + length);
    } else document.formzakaz.ask_desc.value += text1+text2;
  }
  
  funcjs['descchange'] = function(elem){
     if (elem.value.length > 3000) {
        elem.value = elem.value.substr(0,3000);
     }
     document.forms['formzakaz'].scount.value = 'Осталось '+(3000-elem.value.length)+' символов';
  }
  
  funcjs['workexpert'] = function(frm) {
    if (frm.ask_expert.value == 1) {
      document.getElementById('work-expert').style.display = '';
    } else {
      document.getElementById('work-expert').style.display = 'none';
    }
    return false;
  }
              
  funcjs['worktr_plus'] = function(element) {
    if ($(element).css('display') != 'none') { 
      $(element).hide();
      $(element+' input').val('');
    } else {
      $(element).show();
    }
    return false;
  }
   
  funcjs['worktimechange'] = function(frm) {
    if (frm.ask_mnog.value > 0) {
        document.getElementById('worktimeline').style.display = '';
    } else {
        document.getElementById('worktimeline').style.display = 'none';
    }
    return false;
  }

  funcjs['workinter'] = function(frm) {
    if (frm.ask_inter.value == 2) {
        document.getElementById('work-auto-inter').style.display = '';
        document.getElementById('work-ruch-inter').style.display = 'none';
    } else if(frm.ask_inter.value == 1) {
        document.getElementById('work-auto-inter').style.display = 'none';
        document.getElementById('work-ruch-inter').style.display = '';
    } else {
       document.getElementById('work-auto-inter').style.display = 'none';
       document.getElementById('work-ruch-inter').style.display = 'none';
    }
    return false;
  }

  funcjs['workproverca'] = function(frm) {
    if (frm.ask_proverca.value > 0) {
        document.getElementById('work-auto').style.display = '';
        document.getElementById('work-auto-1').style.display = '';
        document.getElementById('info-uk-1').style.display = 'none';
        document.getElementById('info-uk-2').style.display = 'none';
    } else {
       document.getElementById('work-auto').style.display = 'none';
       document.getElementById('work-auto-1').style.display = 'none';
       document.getElementById('info-uk-1').style.display = '';
       document.getElementById('info-uk-2').style.display = '';
    }
    return false;
  }
  
  funcjs['button_pay'] = function(money){
  	
	$('.cash-start').each(function(){
	  var money_all = money,
	 	  sys = $(this).data('system'),
	 	  text_c = 'руб.',
	 	  proc = $(this).data('proc'),
	 	  prtext = '',
	 	  proctext = $(this).data('prtext'),
	 	  proc_p = money/100*proc;
	 	  
	  if(proc > 0)money_all = money_all*1+proc_p*1;
	  if(proctext)prtext = ' '+proctext;
	 	  
	  if(sys == 5){
	    text_c = 'USD';
	 	money_all = money_all*1/pop_curs;
	  }
	 	  
	  money_all = Math.ceil(money_all*100)/100;
	 	  
	  $(this).find('.line-green').html(money_all+' '+text_c+''+prtext);
	 	  
    });	
    
  }
  
  funcjs['back_button'] = function(id){
	$('#load-zacaz-op').css('display','none');
	$('#load-zacaz').css('display','block');
  }
  
  funcjs['showhide'] = function(bid) {
    if (document.getElementById('cattitle'+bid).className == 'cattitle-open')
      document.getElementById('cattitle'+bid).className = 'cattitle-close'; else
      document.getElementById('cattitle'+bid).className = 'cattitle-open';
      $('#catblock'+bid).slideToggle('fast');
  }
  
  funcjs['take_ch'] = function(v)	{ $('input[name="country[]"], input[name="gorod[]"]').prop({"checked":false}); }
  funcjs['put_ch'] = function(v) { $('input[name="country[]"]').prop({"checked":true});$('input[name="gorod[]"]').prop({"checked":false}); }
  
  
  funcjs['calc_serf'] = function(id) {
    var price = document.getElementById('money_kl_'+id).value*1,
	    plan = document.getElementById('plan_'+id).value,
	    text_sys = $('#inp-system-'+id).val() == '0' ? 'рекламного' : 'основного' ;
				
	if(price > 0){
	  $('#ads_price_'+id).html((Math.round((price*plan)*10000)/10000) + ' руб.');
	  $('#mess-text-'+id).val('Вы уверены что хотите пополнить баланс площадки ID: [b]'+id+'[/b][br] С вашего [b]'+text_sys+'[/b] счета будет списана сумма в размере [b]'+ (Math.round((price*plan)*10000)/10000) + ' руб.[/b]');
	}	
  } 
  
  funcjs['go_balans'] = function(system, id, hash, url) {
	        
    $('#inp-system-'+id).val(system);
	funcjs['calc_serf'](id);
	        console.log(system);
	popup_w(
	  'Подтверждение!!', 
	  false, 
	  500, 
	  'func=z_money&l='+url+'&d='+$('#mess-text-'+id).val()+'&f=money&id='+id+'&hash='+hash+'&plan='+$('#plan_'+id).val()+'&system='+system, 
	  '../ajax/ajax_task.php'
	);
	        
	return false;
	          	
  }	
 /////////////////////////////////// 

 
 
    /*funcjs['go_perevod'] = function(system, id, url) {
	        
    $('#inp-system-'+id).val(system);
	funcjs['calc_serf'](id);
	        console.log(system);
	popup_w(
	  'Подтверждение!!', 
	  false, 
	  500, 
	  'func=z_perevod&l='+url+'&d='+$('#mess-text-'+id).val()+'&f=money&id='+id+'&plan='+$('#plan_'+id).val()+'&system='+system, 
	  '../ajax/ajax_task.php'
	);
	        
	return false;
	          	
  }*/
 /////////////////////////////////// 
  funcjs['shodow_abuse'] = function(element, id, aj_html, func) {
	        
    if($(element).css('display') == 'none'){
	  $(aj_html).html('<img src="img/load-offers1.gif" style="display:block;margin:0 auto 20px auto;">');
	  js_post_z(element, '../ajax/ajax_task.php', 'func='+func+'&id='+id, 'html', aj_html);
	  $(element).css('display','');	
	}else{
	  $(aj_html).html('');	
	  $(element).css('display','none');	
	}
	        
	return false;
	          	
  }
  
  
  
  
  funcjs['delblockactivate'] = function(dnum){
    document.getElementById('btns'+dnum).style.display = 'none'; 
    document.getElementById('delcomment'+dnum).style.display = '';
    return false;
  }

  funcjs['dorblockactivate'] = function(dnum){
    document.getElementById('btns'+dnum).style.display = 'none';
    document.getElementById('dorabotka'+dnum).style.display = '';
    return false;
  }
      
  	
  function js_post(e, link, zapros, type_in = 'script', elem = '') {
  
  e_js_post = $(e);
  
  if(status_form == '0'){
    status_form = 1;
    $.ajax({      	
      url: link, type: 'POST', data: zapros, 
	  //dataType: type_in,
      error: function (infa){status_form = 0;console.log(infa);},
      success: function (infa){
	  var data = JSON.parse(infa);
	  if(data['status'] == 'ok')
	  {
		$(e).toggleClass(function() {
  if ( $( this ).hasClass( "serfcontrol-pause" ) ) {
  $(this).attr('class','serfcontrol-play');
  var oncl = $(this).attr('onclick');
$(this).closest('table').find('.scon-edit, .scon-backmoney, .scon-perevod').hide();
  $(this).attr('onclick',oncl.replace('z_play', 'z_pause')); 
    return "";
  } else {
  $(this).closest('table').find('.scon-edit, .scon-backmoney, .scon-perevod').show();
  $(this).attr('class','serfcontrol-play');
  var oncl = $(this).attr('onclick');
    $(this).attr('onclick',oncl.replace('z_pause', 'z_play'));
  $(this).attr('class','serfcontrol-pause');
    return "";
  }
});
		//$(elem).append(data['html']);	
		$(e).closest('table').find('.taskmsg').show().html(data['html']).hide(3000);
	  }else{ 
		$(e).closest('table').find('.taskmsg').show().html(data['html']).hide(3000);   
	  }
	  status_form = 0;
	  //console.log(infa);
	  //alert(infa);
        /*status_form = 0;
        if(type_in == 'html'){
		  $(elem).html(infa);	
		}*/
      }
    });
  }
    
  return false;
  
}







  function js_post_task(e, link, zapros, type_in = 'script', elem = '') {
  
  e_js_post = $(e);
  
  if(status_form == '0'){
    status_form = 1;
    $.ajax({      	
      url: link, type: 'POST', data: zapros, 
	  //dataType: type_in,
      error: function (infa){status_form = 0;console.log(infa);},
      success: function (infa){
	  var data = JSON.parse(infa);
	  if(data['status'] == 'ok')
	  {
		$(e).toggleClass(function() {
  if ( $( this ).hasClass( "btn blue task-favorite-r" ) ) {
 // $(this).attr('class','btn green task-favorite-r');
  //var oncl = $(this).attr('onclick');
//$(this).closest('table').find('.scon-ok').hide();
$(this).closest('table').find('.scon-del').hide();
$(this).closest('table').find('.scon-ok').show();
 // $(this).attr('onclick',oncl.replace('add_izb_task', 'del_izb_task')); 
  //$(this).attr('onclick',oncl.replace('del_izb_task', 'add_izb_task'));
    return "";
  } else {
$(this).closest('table').find('.scon-ok').hide();
$(this).closest('table').find('.scon-del').show();
  //$(this).attr('class','btn blue task-favorite-r');
 // var oncl = $(this).attr('onclick');
    //$(this).attr('onclick',oncl.replace('del_izb_task', 'add_izb_task'));
	//$(this).attr('onclick',oncl.replace('add_izb_task', 'del_izb_task')); 
  //$(this).attr('class','btn green task-favorite-r');
    return "";
  }
});
		//$(elem).append(data['html']);	
		$(e).closest('table').find('.taskmsg').show().html(data['html']).hide(3000);
	  }else{ 
		$(e).closest('table').find('.taskmsg').show().html(data['html']).hide(3000);   
	  }
	  status_form = 0;
	  //console.log(infa);
	  //alert(infa);
        /*status_form = 0;
        if(type_in == 'html'){
		  $(elem).html(infa);	
		}*/
      }
    });
  }
    
  return false;
  
}








  
  function js_post_z(e, link, zapros, type_in = 'script', elem = '') {
  
  e_js_post = $(e);
  
  if(status_form == '0'){
    status_form = 1;
    $.ajax({      	
      url: link, type: 'POST', data: zapros, dataType: type_in,
      error: function (infa){status_form = 0;console.log(infa);},
      success: function (infa){
        status_form = 0;
        if(type_in == 'html'){
		  $(elem).html(infa);	
		}
      }
    });
  }
    
  return false;
   
}
  
  
  
   $('body').on('submit', '.aj-form_mass', function() {


		var url = $(this).attr('action'),
    arrInfa = $(this).data('infa').split(/\s*,\s*/);	
    if(status_form == '0'){
      status_form = 1;
      $.ajax({      	
        url: url,
      	type: 'POST',
      	data: $(this).serialize(),
		
        error: function (infa){
		status_form = 0;
		
		},
        success: function (infa){
		
//console.log(infa);
        //status_form = 0;
		status_form = 0;
		var data = JSON.parse(infa);
		var e= arrInfa[1];
		$($("#otchet-"+e)).closest('table').find('.taskmsg_otchet').show().html(data['html']).hide(3000);
		if(data['status'] == 'ok'){
		function func_zz() {
			$($("#otchet-"+e)).css('display','none');
        }
		setTimeout(func_zz, 3000);
		}
		  
        }
      });
    }
    
    return false;

  });
  
  
  
  $('body').on('submit', '.aj-form', function() {
    var url = $(this).attr('action'),
    arrInfa = $(this).data('infa').split(/\s*,\s*/);	
    if(status_form == '0'){
      status_form = 1;
      $.ajax({      	
        url: url,
      	type: 'POST',
      	data: $(this).serialize(),
      	

        error: function (infa){
		status_form = 0; 
		
		},
        success: function (infa){
		var data = JSON.parse(infa);
		status_form = 0; 
		var e= arrInfa[1];
		$($("#zaiavca-"+e)).closest('table').find('.taskmsg_ot').show().html(data['html']).hide(3000);
		if(data['status'] == 'ok'){
		function func_zz() {
			$($("#zaiavca-"+e)).css('display','none');
        }
		setTimeout(func_zz, 3000);
		}

        }
      });
    }    
    return false;
  });
  
  
   $('body').on('submit', '.aj-form_z', function() {
    var url = $(this).attr('action'),
    arrInfa = $(this).data('infa').split(/\s*,\s*/);	
    if(status_form == '0'){
      status_form = 1;
      $.ajax({      	
        url: url,
      	type: 'POST',
      	data: $(this).serialize(),
        error: function (infa){
		status_form = 0; 
		
		},
        success: function (infa){
		var data = JSON.parse(infa);
		status_form = 0; 
		
		var e= arrInfa[1];
		
	//	$($("#ot_task-"+e)).closest('table').find('.taskmsg_zz').show().html(data['html']).hide(3000);
		$('#load,#popup').css('display','none');
		$('#otch_r').css('display','none');
		//$('#new_otch').css('display','block');
		

		$('#popup').remove();
		
		if(data['status'] == 'ok_del')	{	
		$(".blockwaittask").css('display','none');
		$($("#otc-"+e)).css('display','none');
		//$($("#blockwaittask")).css('display','none');
		
		
		//$($("#blockwaittask")).css('display','none');
		$('#new_otch').css('display','block');
		
		}
		$($("#ot_task-"+e)).closest('table').find('.taskmsg_zz').show().html(data['html']).hide(5000);
        }
      });
    }    
    return false;
  }); 
  
  
  
  $('body').on('submit', '.aj-form_u', function() {
    var url = $(this).attr('action'),
    arrInfa = $(this).data('infa').split(/\s*,\s*/);	
    if(status_form == '0'){
      status_form = 1;
      $.ajax({      	
        url: url,
      	type: 'POST',
      	data: $(this).serialize(),
        error: function (infa){
		status_form = 0; 
		},
        success: function (infa){
		var data = JSON.parse(infa);
		status_form = 0; 
		var e= arrInfa[1];
		
		$('#otch_r').css('display','none');
		//$('#new_otch').css('display','none');
		//$('#ok_otch').css('display','block');
		
		$('#popup').remove();
		if(data['status'] == 'ok_del')	{	
		$(".blockwaittask").css('display','none');
		$($("#otc-"+e)).css('display','none');
		//$($("#blockwaittask")).css('display','none');

		//$($("#blockwaittask")).css('display','none');
		$('#new_otch').css('display','block');
		
		}		
		if(data['status'] == 'ok_ot')	{	
		$($("#otch_r")).css('display','none');
		//$('#otcb').css('display','block');
		$($("#otc-"+e)).css('display','block');
		$('#ok_otch').css('display','block');
		
		}
		$($("#ot_task-"+e)).closest('table').find('.taskmsg_zz').show().html(data['html']).hide(5000);
        }
      });
    }    
    return false;
  }); 
  
  
  
  function task_fun_del(e, link, zapros, type_in = 'script', elem = '') {
  e_js_post = $(e);

  if(status_form == '0'){
    $.ajax({      	
      url: link, type: 'POST', data: zapros, 
      error: function (infa){status_form = 0;console.log(infa);},
      success: function (infa){
	  var data = JSON.parse(infa); 
	 // console.log(infa);
	  if(data['status'] == 'ok')
	  {
		$(e).find('.taskmsg').show().html(data['html']).hide(4000);
		function func_zz() {
			$(e).css('display','none');
        }
		setTimeout(func_zz, 1000);
	  }else{ 
		$(e).find('.taskmsg').show().html(data['html']).hide(4000);   
	  }
		$('#load,#popup').css('display','none');
		$('#popup').remove();
      }
    });
  }   
  return false;
}
  
  
  
  
  
  function task_fun(e, link, zapros, type_in = 'script', elem = '') {
  e_js_post = $(e);
  
  
  if(status_form == '0'){
    $.ajax({      	
      url: link, type: 'POST', data: zapros, 
      error: function (infa){status_form = 0;console.log(infa);},
      success: function (infa){
	  status_form = 0
	  var data = JSON.parse(infa); 
	 // console.log(infa);
	  if(data['status'] == 'ok')
	  {
		$(e).find('.taskmsg').show().html(data['html']).hide(4000);

	  }else{ 
		$(e).find('.taskmsg').show().html(data['html']).hide(4000);   
	  }
		$('#load,#popup').css('display','none');
		$('#popup').remove();
      }
    });
  }   
  return false;
}

function desk_limit(elem, limit, out){
  var elem_num = $(elem)[0], num = elem_num.value.length;    
  if (num > limit) { elem_num.value = elem_num.value.substr(0,limit); }
  $(out).html((limit - num));
} 
  
  
  
function openbox(id){
    display = document.getElementById(id).style.display;
    if(display=='none'){

       document.getElementById(id).style.display='block';

    }else{

       document.getElementById(id).style.display='none';

    }

}


function popup_w(title, animate, width, data_post, data_url){
  
  go_anima = (animate)? true : false ;
  
  var css_overflow = '';
  
  if(go_anima){
  	$('#load').stop().animate({opacity: 'show'}, 200, "linear"); 
  }else{ 
    $('#load').css({display:'block'});	
  }
  
  if($('div').is('#popup')){ $('#popup').remove(); }
  
  var popup = '<div id="popup" style="width:'+width+'px;">';
      popup += '<span class="closed-popup" onclick="closed_popup();">Закрыть</span>';
      popup += '<div class="title-popup">'+title+'</div>';
      popup += '<div class="text-popup" id="js-popup">';
      popup += '<img src="img/load-offers1.gif" class="load-popup">';
      popup += '</div>';
      popup += '</div>';
  
  
  $('body').append(popup);
  
  left_s = ($(window).width() - $('#popup').outerWidth())/2 + $(window).scrollLeft();
      
  $('#popup').css({position:'absolute', left: left_s + 'px',top: -700 + 'px', display:'block'});	  
  
  $(window).resize(function(){
    left_s = ($(window).width() - $('#popup').outerWidth())/2 + $(window).scrollLeft(); 
    top_s = 100 + $(window).scrollTop();
    if(go_anima){
      $('#popup').stop().animate({position:'absolute', top: top_s + "px", left: left_s + 'px'}, 200, "linear");
    }else{
      $('#popup').css({position:'absolute', top: top_s + "px", left: left_s + 'px'});
    }
  }); 
  
  $(window).resize();
  
  $.ajax({
  	url: data_url, 
  	type: 'POST', 
  	data: data_post,
    error: function (){ $('#js-popup').html('<div class="msg-error" style="margin:0;">Не удалось выполнить запрос!<br>Если ошибка повторяется, обратитесь к Администратору проекта.</div>'); },
    success: function (data){ $('#js-popup').html(data); }
  });
  
  $('#popup').on('mousedown', '.title-popup', function(e) {
  	go_move = true;
  	go_start_x = e.offsetX==undefined?e.layerX:e.offsetX;
  	go_start_y = e.offsetY==undefined?e.layerY:e.offsetY;
  	$('#popup').css('opacity','0.3');
  });
  
  $(document).bind('mousemove', function(e) {
  	if(go_move){
  	  var x = $('#popup').position().left,
  	      y = $('#popup').position().top,
  	      margin_x = $('#popup').css('margin-left'),
  	      margin_y = $('#popup').css('margin-top');
  	      margin_x = parseInt(margin_x.replace(/\D+/g,""));
  	      margin_y = parseInt(margin_y.replace(/\D+/g,""));
  	  
  	  $('#popup').css({
  	  	left:(e.pageX-go_start_x-margin_x)+'px', top: (e.pageY-go_start_y-margin_y)+'px'
  	  });
  	}
  });
  
  $(document).bind('mouseup', function() { go_move = false; $('#popup').css('opacity','1'); });
  
}


function show_window(id){
  if($(id).css('display') == 'none'){
	$(id).slideDown(300);	
  }else{ 
	$(id).slideUp(300);	
  }
}

function error_start(text,elem,time){
  if(!time) time = 3000;
  $(elem).html(text).animate({ opacity: 'show' }, 600);
  setTimeout(function(){
  	$(elem).animate({ opacity: 'hide' }, 600,
  	  function(){$(elem).html('');
  	});
  },time);
}

function closed_popup(animate){
  if(go_anima){
  	$('#load').stop().animate({opacity: 'hide'}, 200, "linear");
	$('#popup').stop().animate({position:'absolute', top: -700 + "px"}, 100, "linear", function(){$('#popup').remove();});
  }else{
  	$('#load,#popup').css('display','none');
	$('#popup').remove();
  }
}
  
funcjs['task-start'] = function(eid){
  $($("#new_otch")).css('display','none'); 
  $('#otch_r').css('display','block');
		
  document.cookie="viewtask="+eid;
  document.forms['goform'].submit();
  
  setTimeout(function(){load_site('',true);},1500)
  return false;
}  