<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}

$system_pay[-1] = "Пакет";
$system_pay[0] = "Админка";
$system_pay[1] = "WebMoney";
$system_pay[2] = "RoboKassa";
$system_pay[3] = "Wallet One";
$system_pay[4] = "InterKassa";
$system_pay[5] = "Payeer";
$system_pay[6] = "Qiwi";
$system_pay[7] = "PerfectMoney";
$system_pay[8] = "YandexMoney";
$system_pay[9] = "MegaKassa";
$system_pay[20] = "FreeKassa";
$system_pay[10] = "Рекл. счет";

$mysqli->query("UPDATE `tb_ads_tests` SET `status`='3', `date_edit`='".time()."' WHERE `status`>'0' AND `status`<'4' AND `balance`<`cena_advs`");
$mysqli->query("UPDATE `tb_ads_tests` SET `status`='1', `date_edit`='".time()."' WHERE `status`='3' AND `balance`>=`cena_advs`");

echo '<div id="LoadModalClaimsTest" style="display:none;"></div>';

$id = ( isset($_GET["id"])  && preg_match("|^[\d]{1,11}$|", intval(limpiar(trim($_GET["id"])))) ) ? intval(limpiar(trim($_GET["id"]))) : false;
$option = ( isset($_GET["option"])  && preg_match("|^[a-zA-Z0-9\-_-]{1,20}$|",  limpiar(trim($_GET["option"]))) ) ? limpiar(trim($_GET["option"])) : false;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='tests_cena_hit' AND `howmany`='1'");
$tests_cena_hit = number_format($sql->fetch_object()->price, 4, ".", "");

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='tests_comis_del' AND `howmany`='1'");
$tests_comis_del = number_format($sql->fetch_object()->price, 0, ".", "");

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='tests_nacenka' AND `howmany`='1'");
$tests_nacenka = number_format($sql->fetch_object()->price, 0, ".", "");

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='tests_min_pay' AND `howmany`='1'");
$tests_min_pay = number_format($sql->fetch_object()->price, 2, ".", "");

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='tests_cena_quest' AND `howmany`='1'");
$tests_cena_quest = number_format($sql->fetch_object()->price, 4, ".", "");

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='tests_cena_color' AND `howmany`='1'");
$tests_cena_color = number_format($sql->fetch_object()->price, 4, ".", "");

for($i=1; $i<=4; $i++) {
	$tests_cena_revisit[0] = 0;
	$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='tests_cena_revisit' AND `howmany`='$i'");
	$tests_cena_revisit[$i] = number_format($sql->fetch_object()->price, 4, ".", "");
}

for($i=1; $i<=2; $i++) {
	$tests_cena_unic_ip[0] = 0;
	$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='tests_cena_unic_ip' AND `howmany`='$i'");
	$tests_cena_unic_ip[$i] = number_format($sql->fetch_object()->price, 4, ".", "");
}

if(isset($option) && $option=="edit" && isset($id) && $id>0) {
	echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Редактирование теста № '.$id.'</b></h3>';
	$page = (isset($_GET["page"]) && preg_match("|^[0-9\-]{1,11}$|", trim($_GET["page"]))) ? intval(trim($_GET["page"])) : "1";

	$sql = $mysqli->query("SELECT * FROM `tb_ads_tests` WHERE `id`='$id' AND `status`>'0'") or die($mysqli->error);
	if($sql->num_rows>0) {
		$row = $sql->fetch_assoc();
		$id = $row["id"];
		$status = $row["status"];
		$questions = unserialize($row["questions"]);
		$answers = unserialize($row["answers"]);
		$geo_targ = (isset($row["geo_targ"]) && trim($row["geo_targ"])!=false) ? explode(", ", $row["geo_targ"]) : array();

		if($status==1) {
			echo '<span class="msg-error">Перед редактированием необходимо приостановить рекламную кампанию!</span>';
		}else{
			?>
			<script type="text/javascript" language="JavaScript">
			function ShowHideBlock(id) {
				if ( $("#adv-title"+id).attr("class") == "adv-title-open" ) {
					$("#adv-title"+id).attr("class", "adv-title-close");
				} else {
					$("#adv-title"+id).attr("class", "adv-title-open");
				}
				$("#adv-block"+id).slideToggle("fast");
			}

			function SetChecked(type){
				var nodes = document.getElementsByTagName("input");
				for (var i = 0; i < nodes.length; i++) {
					if (nodes[i].name == "country[]") {
						if(type == "paste") nodes[i].checked = true;
						else  nodes[i].checked = false;
					}
				}
			}

			function add_quest() {
				if ( $("#block_quest4").css("display") == "none" ) {
					$("#block_quest4").fadeIn("fast", function(){
						$("#quest4").val(""); $("#answ41").val(""); $("#answ42").val(""); $("#answ43").val("");
						if ( $("#block_quest5").css("display") == "" | $("#block_quest5").css("display") == "block" ) $("#block_add_quest").hide();
						$("#block_quest4").show();
						PlanChange();
					});
				} else if ( $("#block_quest5").css("display") == "none" ) {
					$("#block_quest5").fadeIn("fast", function(){
						$("#quest5").val(""); $("#answ51").val(""); $("#answ52").val(""); $("#answ53").val("");
						if ( $("#block_quest4").css("display") == "" | $("#block_quest4").css("display") == "block" ) $("#block_add_quest").hide();
						$("#block_quest4").show();
						PlanChange();
					});
				}
			}

			function del_quest() {
				if ( $("#block_quest5").css("display") == "" | $("#block_quest5").css("display") == "block" ) {
					$("#block_quest5").fadeOut("fast", function(){
						$("#quest5").val(""); $("#answ51").val(""); $("#answ52").val(""); $("#answ53").val("");
						$("#block_quest5").hide();
						$("#block_add_quest").show();
						PlanChange();
					});
				} else if ( $("#block_quest4").css("display") == "" | $("#block_quest4").css("display") == "block" ) {
					$("#block_quest4").fadeOut("fast", function(){
						$("#quest4").val(""); $("#answ41").val(""); $("#answ42").val(""); $("#answ43").val("");
						$("#block_quest4").hide();
						$("#block_add_quest").show();
						PlanChange();
					});
				}
			}

			function EditAds(id, type, op) {
				$("#info-msg-cab").html("").hide();

				var title = $.trim($("#title").val());
				var description = $.trim($("#description").val());
				var url = $.trim($("#url").val());
				var revisit = $.trim($("#revisit").val());
				var color = $.trim($("#color").val());
				var unic_ip_user = $.trim($("#unic_ip_user").val());
				var date_reg_user = $.trim($("#date_reg_user").val());
				var sex_user = $.trim($("#sex_user").val());
				var country = $('input[id="country[]"]:checked').map(function(){return $(this).val();}).get();

				var quest1 = $.trim($("#quest1").val()); var answ11 = $.trim($("#answ11").val()); var answ12 = $.trim($("#answ12").val()); var answ13 = $.trim($("#answ13").val());
				var quest2 = $.trim($("#quest2").val()); var answ21 = $.trim($("#answ21").val()); var answ22 = $.trim($("#answ22").val()); var answ23 = $.trim($("#answ23").val());
				var quest3 = $.trim($("#quest3").val()); var answ31 = $.trim($("#answ31").val()); var answ32 = $.trim($("#answ32").val()); var answ33 = $.trim($("#answ33").val());
				var quest4 = $.trim($("#quest4").val()); var answ41 = $.trim($("#answ41").val()); var answ42 = $.trim($("#answ42").val()); var answ43 = $.trim($("#answ43").val());
				var quest5 = $.trim($("#quest5").val()); var answ51 = $.trim($("#answ51").val()); var answ52 = $.trim($("#answ52").val()); var answ53 = $.trim($("#answ53").val());

				if (title == "") {
					$("html, body").animate({scrollTop: $("#Save").offset().top+100}, 700);
					$("#info-msg-cab").html('<span class="msg-error">Вы не указали заголовок теста!</span>').slideToggle(300);
					HideMsg("info-msg-cab", 3000);
					return false;
				} else if (description == "") {
					$("html, body").animate({scrollTop: $("#Save").offset().top+100}, 700);
					$("#info-msg-cab").html('<span class="msg-error">Вы не описали инструкцию к выполнению теста!</span>').slideToggle(300);
					HideMsg("info-msg-cab", 3000);
					return false;
				} else if ((url == '') | (url == 'http://') | (url == 'https://')) {
					$("html, body").animate({scrollTop: $("#Save").offset().top+100}, 700);
					$("#info-msg-cab").html('<span class="msg-error">Вы не указали URL-адрес сайта!</span>').slideToggle(300);
					HideMsg("info-msg-cab", 3000);
					return false;
				} else if (quest1 == "") {
					$("html, body").animate({scrollTop: $("#Save").offset().top+100}, 700);
					$("#info-msg-cab").html('<span class="msg-error">Вы не указали первый вопрос!</span>').slideToggle(300);
					HideMsg("info-msg-cab", 3000);
					return false;
				} else if (answ11 == "" | answ12 == "" | answ13 == "") {
					$("html, body").animate({scrollTop: $("#Save").offset().top+100}, 700);
					$("#info-msg-cab").html('<span class="msg-error">Вы не указали варианты ответа на первый вопрос!</span>').slideToggle(300);
					HideMsg("info-msg-cab", 3000);
					return false;
				} else if (quest2 == "") {
					$("html, body").animate({scrollTop: $("#Save").offset().top+100}, 700);
					$("#info-msg-cab").html('<span class="msg-error">Вы не указали второй вопрос!</span>').slideToggle(300);
					HideMsg("info-msg-cab", 3000);
					return false;
				} else if (answ21 == "" | answ22 == "" | answ23 == "") {
					$("html, body").animate({scrollTop: $("#Save").offset().top+100}, 700);
					$("#info-msg-cab").html('<span class="msg-error">Вы не указали варианты ответа на второй вопрос!</span>').slideToggle(300);
					HideMsg("info-msg-cab", 3000);
					return false;
				} else if (quest3 == "") {
					$("html, body").animate({scrollTop: $("#Save").offset().top+100}, 700);
					$("#info-msg-cab").html('<span class="msg-error">Вы не указали третий вопрос!</span>').slideToggle(300);
					HideMsg("info-msg-cab", 3000);
					return false;
				} else if (answ31 == "" | answ32 == "" | answ33 == "") {
					$("html, body").animate({scrollTop: $("#Save").offset().top+100}, 700);
					$("#info-msg-cab").html('<span class="msg-error">Вы не указали варианты ответа на третий вопрос!</span>').slideToggle(300);
					HideMsg("info-msg-cab", 3000);
					return false;
				} else {
					$.ajax({
						type: "POST", url: "ajax_admin/ajax_json_adv.php?rnd="+Math.random(), 
						data: {
							'op':op, 'type':type, 'id':id, 
							'title':title, 'description':description, 'url':url, 
							'quest1':quest1, 'answ11':answ11, 'answ12':answ12, 'answ13':answ13, 
							'quest2':quest2, 'answ21':answ21, 'answ22':answ22, 'answ23':answ23, 
							'quest3':quest3, 'answ31':answ31, 'answ32':answ32, 'answ33':answ33, 
							'quest4':quest4, 'answ41':answ41, 'answ42':answ42, 'answ43':answ43, 
							'quest5':quest5, 'answ51':answ51, 'answ52':answ52, 'answ53':answ53, 
							'revisit':revisit, 'color':color, 'unic_ip_user':unic_ip_user, 'date_reg_user':date_reg_user, 
							'sex_user':sex_user, 'country[]':country
						}, 
						dataType: 'json',
						error: function() {
							$("#loading").slideToggle();
							$("html, body").animate({scrollTop: $("#Save").offset().top+100}, 700);
							$("#info-msg-cab").html('<span class="msg-error">Ошибка обработки данных ajax/json!</span>').slideToggle(300);
							HideMsg("info-msg-cab", 10000);
							return false;
						}, 
						beforeSend: function() { $("#loading").slideToggle(); }, 

						success: function(data) {
							$("#loading").slideToggle();

							if (data.result == "OK") {
								$("html, body").animate({scrollTop: $("html, body").offset().top-10}, 700);
								$("#Save").remove();
								$("#BlockForm").html("").hide();
								$("#BlockForm").html('<span class="msg-ok">Рекламная площадка № '+id+' успешно отредактирована!</span>').slideToggle("slow");
								setTimeout(function() {document.location.href = "<?php echo "?op=$op".($page>1 ? "&page=$page" : false);?>";}, 3500);
							}else{
								if(data.message) {
									$("html, body").animate({scrollTop: $("#Save").offset().top+100}, 700);
									$("#info-msg-cab").html('<span class="msg-error">'+data.message+'</span>').slideToggle(300);
									HideMsg("info-msg-cab", 5000);
									return false;
								} else if(data) {
									$("html, body").animate({scrollTop: $("#Save").offset().top+100}, 700);
									$("#info-msg-cab").html('<span class="msg-error">'+data+'</span>').slideToggle(300);
									HideMsg("info-msg-cab", 5000);
									return false;
								} else {
									$("html, body").animate({scrollTop: $("#Save").offset().top+100}, 700);
									$("#info-msg-cab").html('<span class="msg-error">Ошибка обработки данных!</span>').slideToggle(300);
									HideMsg("info-msg-cab", 5000);
									return false;
								}
							}
						}
					});
				}
			}

			function CtrlEnter(event) {
				var event = event || window.event;
				if( ( (event.ctrlKey) && ((event.keyCode == 0xA) || (event.keyCode == 0xD)) ) ) {
					$("#Save").click();
				}
			}
			</script>
			<?php

			echo '<div id="BlockForm" style="display:block;">';
			echo '<div id="newform" onkeypress="CtrlEnter(event);">';
				echo '<table class="tables" style="border:none; margin:0; padding:0; width:100%;">';
				echo '<thead><tr>';
					echo '<th align="center" class="top">Параметр</th>';
					echo '<th align="center" class="top" colspan="2">Значение</th>';
				echo '</thead></tr>';
				echo '<tr>';
					echo '<td align="left" width="220"><b>Заголовок теста</b></td>';
					echo '<td align="left"><input type="text" id="title" maxlength="60" value="'.$row["title"].'" class="ok"></td>';
					echo '<td align="center" width="16" style="background: #EDEDED;"><span id="hint1" class="hint-quest"></span></td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td align="left" colspan="3"><b>Инструкции для тестирования &darr;</b></td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td align="left" colspan="2">';
						echo '<span class="bbc-bold" style="float:left;" title="Выделить жирным" onClick="javascript:InsertTags(\'[b]\',\'[/b]\', \'description\'); return false;">Ж</span>';
						echo '<span class="bbc-italic" style="float:left;" title="Выделить курсивом" onClick="javascript:InsertTags(\'[i]\',\'[/i]\', \'description\'); return false;">К</span>';
						echo '<span class="bbc-uline" style="float:left;" title="Выделить подчёркиванием" onClick="javascript:InsertTags(\'[u]\',\'[/u]\', \'description\'); return false;">Ч</span>';
						echo '<span class="bbc-tline" style="float:left;" title="Перечеркнутый текст" onClick="javascript:InsertTags(\'[s]\',\'[/s]\', \'description\'); return false;">ST</span>';
						echo '<span class="bbc-left" style="float:left;" title="Выровнять по левому краю" onClick="javascript:InsertTags(\'[left]\',\'[/left]\', \'description\'); return false;"></span>';
						echo '<span class="bbc-center" style="float:left;" title="Выровнять по центру" onClick="javascript:InsertTags(\'[center]\',\'[/center]\', \'description\'); return false;"></span>';
						echo '<span class="bbc-right" style="float:left;" title="Выровнять по правому краю" onClick="javascript:InsertTags(\'[right]\',\'[/right]\', \'description\'); return false;"></span>';
						echo '<span class="bbc-justify" style="float:left;" title="Выровнять по ширине" onClick="javascript:InsertTags(\'[justify]\',\'[/justify]\', \'description\'); return false;"></span>';
						echo '<span class="bbc-url" style="float:left;" title="Выделить URL" onClick="javascript:InsertTags(\'[url]\',\'[/url]\', \'description\'); return false;">URL</span>';
						echo '<span id="count1" style="display: block; float:right; color:#696969; margin-top:2px; margin-right:3px;">Осталось символов: 1000</span>';
						echo '<br>';
						echo '<div style="display: block; clear:both; padding-top:4px">';
							echo '<textarea id="description" class="ok" style="height:120px; width:99%;" onKeyup="descchange(\'1\', this, \'1000\');" onKeydown="descchange(\'1\', this, \'1000\');" onClick="descchange(\'1\', this, \'1000\');">'.$row["description"].'</textarea>';
						echo '</div>';
					echo '</td>';
					echo '<td align="center" width="16" style="background: #EDEDED;"><span id="hint2" class="hint-quest"></span></td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td align="left"><b>URL сайта</b> (включая http://)</td>';
					echo '<td align="left"><input type="text" id="url" maxlength="300" value="'.$row["url"].'" class="ok"></td>';
					echo '<td align="center" width="16" style="background: #EDEDED;"><span id="hinturl" class="hint-quest"></span></td>';
				echo '</tr>';

				for($i=1; $i<=3; $i++){
					echo '<tr><td align="center" colspan="3" style="background: #DCE7EA; color: #00649E; font:13px Tahoma, Arial, sans-serif; font-weight:bold; text-shadow:1px 1px 1px #FFF;">Вопрос №'.$i.'</td></tr>';

					echo '<tr align="left">';
						echo '<td align="left" width="220"><b>Содержание вопроса</b></td>';
						echo '<td align="left"><input type="text" id="quest'.$i.'" maxlength="300" value="'.$questions[$i].'" class="ok"></td>';
						if($i==1) {
							echo '<td align="center" width="16" rowspan="4" style="background: #EDEDED;"><span id="hint3" class="hint-quest"></span></td>';
						}else{
							echo '<td align="center" width="16" rowspan="4" style="background: #EDEDED;"></td>';
						}
					echo '</tr>';
					for($y=1; $y<=3; $y++){
						echo '<tr>';
							echo '<td align="left">Вариант ответа '.($y==1 ? '<span style="color: #009125;">(правильный)</span>' : '<span style="color: #FF0000;">(ложный)</span>').'</td>';
							echo '<td align="left"><input type="text" id="answ'.$i.$y.'" maxlength="30" value="'.$answers[$i][$y].'" class="ok" style="color: '.($y==1 ? "#009125;" : "#FF0000;").'"></td>';
						echo '</tr>';
					}
				}
				echo '</table>';

				for($i=4; $i<=5; $i++){
					echo '<div id="block_quest'.$i.'" style="'.( (isset($questions[$i]) && $questions[$i]!="") ? "display:block;" : "display:none;" ).'">';
						echo '<table class="tables" style="margin:0; padding:0;">';
						echo '<tr><td align="center" colspan="3" style="background: #DCE7EA; color: #00649E; font:13px Tahoma, Arial, sans-serif; font-weight:bold; text-shadow:1px 1px 1px #FFF;">Дополнительный вопрос</td></tr>';
						echo '<tr align="left">';
							echo '<td align="left" width="220"><b>Содержание вопроса</b></td>';
							echo '<td align="left"><input type="text" id="quest'.$i.'" maxlength="300" value="'.( (isset($questions[$i]) && $questions[$i]!="") ? $questions[$i] : false ).'" class="ok"></td>';
							echo '<td align="center" width="16" rowspan="4" style="background: #EDEDED;"><img src="/img/error2.gif" onClick="del_quest();" style="float: none; width:14px; cursor:pointer; margin:0; padding:0" title="Удалить вопрос"></td>';
						echo '</tr>';
						for($y=1; $y<=3; $y++){
							echo '<tr>';
								echo '<td align="left">Вариант ответа '.($y==1 ? '<span style="color: #009125;">(правильный)</span>' : '<span style="color: #FF0000;">(ложный)</span>').'</td>';
								echo '<td align="left"><input type="text" id="answ'.$i.$y.'" maxlength="30" value="'.( (isset($answers[$i][$y]) && $answers[$i][$y]!="") ? $answers[$i][$y] : false ).'" class="ok" style="color: '.($y==1 ? "#009125;" : "#FF0000;").'"></td>';
							echo '</tr>';
						}
						echo '</table>';
					echo '</div>';
				}

				echo '<div id="block_add_quest" style="'.(count($questions)==5 ? "display:none;" : "display:block;").'">';
					echo '<table class="tables" style="margin:0; padding:0;">';
					echo '<tr><td align="center" style="padding: 3px 0;">';
						echo '<span class="sub-click" onClick="add_quest();" style="padding-left:40px; padding-right:40px;">Добавить ещё вопрос '.($tests_cena_quest>0 ? "(+ ".p_floor($tests_cena_quest, 4)." руб.)" : false).'</span>';
					echo '</td></tr>';
					echo '</table>';
				echo '</div>';

				echo '<span id="adv-title1" class="adv-title-'.(($row["revisit"]>0 | $row["color"]>0 | $row["unic_ip_user"]>0 | $row["date_reg_user"]>0 | $row["sex_user"]>0) ? "open" : "close").'" onclick="ShowHideBlock(1);">Дополнительные настройки</span>';
				echo '<div id="adv-block1" style="'.(($row["revisit"]>0 | $row["color"]>0 | $row["unic_ip_user"]>0 | $row["date_reg_user"]>0 | $row["sex_user"]>0) ? "display:;" : "display:none;").'">';
					echo '<table class="tables" style="margin:0; padding:0;">';
					echo '<tr>';
						echo '<td align="left" width="220">Технология тестирования</td>';
						echo '<td align="left">';
							echo '<select id="revisit" class="ok" onChange="PlanChange();" onClick="PlanChange();">';
								echo '<option value="0" '.($row["revisit"]==0 ? 'selected="selected"' : false).'>Доступно всем каждые 24 часа</option>';
								echo '<option value="1" '.($row["revisit"]==1 ? 'selected="selected"' : false).'>Доступно всем каждые 3 дня '.($tests_cena_revisit[1]>0 ? "(+ ".p_floor($tests_cena_revisit[1], 4)." руб.)" : false).'</option>';
								echo '<option value="2" '.($row["revisit"]==2 ? 'selected="selected"' : false).'>Доступно всем каждую неделю '.($tests_cena_revisit[2]>0 ? "(+ ".p_floor($tests_cena_revisit[2], 4)." руб.)" : false).'</option>';
								echo '<option value="3" '.($row["revisit"]==3 ? 'selected="selected"' : false).'>Доступно всем каждые 2 недели '.($tests_cena_revisit[3]>0 ? "(+ ".p_floor($tests_cena_revisit[3], 4)." руб.)" : false).'</option>';
								echo '<option value="4" '.($row["revisit"]==4 ? 'selected="selected"' : false).'>Доступно всем каждый месяц '.($tests_cena_revisit[4]>0 ? "(+ ".p_floor($tests_cena_revisit[4], 4)." руб.)" : false).'</option>';
							echo '</select>';
						echo '</td>';
						echo '<td align="center" width="16" style="background: #EDEDED;"><span id="hint4" class="hint-quest"></span></td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td align="left">Выделить тест</td>';
						echo '<td align="left">';
							echo '<select id="color" class="ok" onChange="PlanChange();" onClick="PlanChange();">';
								echo '<option value="0" '.($row["color"]==0 ? 'selected="selected"' : false).'>Нет</option>';
								echo '<option value="1" '.($row["color"]==1 ? 'selected="selected"' : false).'>Да '.($tests_cena_color>0 ? "(+ ".p_floor($tests_cena_color, 4)." руб.)" : false).'</option>';
							echo '</select>';
						echo '</td>';
						echo '<td align="center" width="16" style="background: #EDEDED;"><span id="hint5" class="hint-quest"></span></td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td align="left">Уникальный IP</td>';
						echo '<td align="left">';
							echo '<select id="unic_ip_user" class="ok" onChange="PlanChange();" onClick="PlanChange();">';
								echo '<option value="0" '.($row["unic_ip_user"]==0 ? 'selected="selected"' : false).'>Нет</option>';
								echo '<option value="1" '.($row["unic_ip_user"]==1 ? 'selected="selected"' : false).'>Да, 100% совпадение '.($tests_cena_unic_ip[1]>0 ? "(+ ".p_floor($tests_cena_unic_ip[1], 4)." руб.)" : false).'</option>';
								echo '<option value="2" '.($row["unic_ip_user"]==2 ? 'selected="selected"' : false).'>Усиленный по маске до 2 чисел '.($tests_cena_unic_ip[2]>0 ? "(+ ".p_floor($tests_cena_unic_ip[2], 4)." руб.)" : false).'</option>';
							echo '</select>';
						echo '</td>';
						echo '<td align="center" width="16" style="background: #EDEDED;"><span id="hint6" class="hint-quest"></span></td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td align="left">По дате регистрации</td>';
						echo '<td align="left">';
							echo '<select id="date_reg_user" class="ok" onChange="PlanChange();" onClick="PlanChange();">';
								echo '<option value="0" '.($row["date_reg_user"]==0 ? 'selected="selected"' : false).'>Все пользователи проекта</option>';
								echo '<option value="1" '.($row["date_reg_user"]==1 ? 'selected="selected"' : false).'>До 7 дней с момента регистрации</option>';
								echo '<option value="2" '.($row["date_reg_user"]==2 ? 'selected="selected"' : false).'>От 7 дней с момента регистрации</option>';
								echo '<option value="3" '.($row["date_reg_user"]==3 ? 'selected="selected"' : false).'>От 30 дней с момента регистрации</option>';
								echo '<option value="4" '.($row["date_reg_user"]==4 ? 'selected="selected"' : false).'>От 90 дней с момента регистрации</option>';
							echo '</select>';
						echo '</td>';
						echo '<td align="center" width="16" style="background: #EDEDED;"><span id="hint7" class="hint-quest"></span></td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td align="left">По половому признаку</td>';
						echo '<td align="left">';
							echo '<select id="sex_user" class="ok" onChange="PlanChange();" onClick="PlanChange();">';
								echo '<option value="0" '.($row["sex_user"]==0 ? 'selected="selected"' : false).'>Все пользователи проекта</option>';
								echo '<option value="1" '.($row["sex_user"]==1 ? 'selected="selected"' : false).'>Только мужчины</option>';
								echo '<option value="2" '.($row["sex_user"]==2 ? 'selected="selected"' : false).'>Только женщины</option>';
							echo '</select>';
						echo '</td>';
						echo '<td align="center" width="16" style="background: #EDEDED;"><span id="hint8" class="hint-quest"></span></td>';
					echo '</tr>';
					echo '</table>';
				echo '</div>';

				echo '<span id="adv-title2" class="adv-title-'.(count($geo_targ)>0 ? "open" : "close").'" onclick="ShowHideBlock(2);">Настройки геотаргетинга</span>';
				echo '<div id="adv-block2" style="'.(count($geo_targ)>0 ? "display:;" : "display:none;").'">';
					echo '<table class="tables" style="margin:0; padding:0;">';
					echo '<tr>';
						echo '<td colspan="2" align="center" style="border-right:none;"><a onclick="SetChecked(\'paste\');" style="width:100%; color:#008B00; font-weight:bold; cursor:pointer;"><center>Отметить все</center></a></td>';
						echo '<td colspan="2" align="center" style="border-left:none;"><a onclick="SetChecked();" style="width:100%; color:#FF0000; font-weight:bold; cursor:pointer;"><center>Снять все</center></a></td>';
					echo '</tr>';
					include(DOC_ROOT."/advertise/func_geotarg_edit.php");
					echo '</table>';
				echo '</div>';

				echo '<table class="tables" style="margin:0; padding:0;">';
				echo '<tr><td align="center" colspan="3" style="background: #DCE7EA; color: #00649E; font:13px Tahoma, Arial, sans-serif; font-weight:bold; text-shadow:1px 1px 1px #FFF;">Информация</td></tr>';
				echo '<tr>';
					echo '<td align="left" width="220" height="23px"><b>Вознаграждение</b></td>';
					echo '<td align="left" id="price_user"></td>';
					echo '<td align="center" width="16" style="background: #EDEDED;"><span id="hint12" class="hint-quest"></span></td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td align="left" height="23px"><b>Цена одного теста</b></td>';
					echo '<td align="left" id="price_one"></td>';
					echo '<td align="center" width="16" style="background: #EDEDED;"><span id="hint13" class="hint-quest"></span></td>';
				echo '</tr>';
				echo '</table>';

			echo '</div>';
			echo '</div>';

			echo '<br>';
			echo '<div id="info-msg-cab" style="display:none;"></div>';
			echo '<div align="center"><span id="Save" onClick="EditAds('.$id.', \'tests\', \'EditAds\');" class="sub-blue160" style="float:none; width:160px;">Сохранить</span></div>';

			?><script language="JavaScript">PlanChange(); descchange(1, description, 1000);</script><?php
		}
	}else{
		echo '<span class="msg-error">Рекламная площадка № '.$id.' не найдена</span>';
	}

	exit();

}elseif(isset($option) && $option=="statistics" && isset($id) && $id>0) {
	echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Список исполнителей теста № '.$id.'</b></h3>';

	$sql = $mysqli->query("SELECT * FROM `tb_ads_tests` WHERE `id`='$id' AND `status`>'0'") or die($mysqli->error);
	if($sql->num_rows>0) {
		$row = $sql->fetch_assoc();
		$id = $row["id"];
		$status = $row["status"];

		include(DOC_ROOT."/geoip/geoipcity.inc");
		include(DOC_ROOT."/geoip/geoipregionvars.php");
		$gi = geoip_open(DOC_ROOT."/geoip/GeoLiteCity.dat", GEOIP_STANDARD);

		require("navigator/navigator.php");
		$perpage = 25;
		$sql_p = $mysqli->query("SELECT `id` FROM `tb_ads_tests_visits` WHERE `ident`='$id' AND `money`>'0'") or die($mysqli->error);
		$count = $sql_p->num_rows;
		$pages_count = ceil($count / $perpage);
		$page = (isset($_GET["page"]) && preg_match("|^[0-9\-]{1,11}$|", trim($_GET["page"]))) ? intval(trim($_GET["page"])) : "1";
		if ($page > $pages_count | $page<0) $page = $pages_count;
		$start_pos = ($page - 1) * $perpage;
		if($start_pos<0) $start_pos = 0;

		if($count>$perpage) {universal_link_bar($count, $page, $_SERVER["PHP_SELF"], $perpage, 10, '&page=', "?op=$op&option=statistics&id=$id");}
		echo '<table class="adv-cab" style="margin:1px auto;">';
		echo '<thead><tr align="center">';
			echo '<th>Логин</th>';
			echo '<th>ID</th>';
			echo '<th>Дата</th>';
			echo '<th>Выполнялся</th>';
			echo '<th width="125">IP</th>';
			echo '<th width="70">Статус</th>';
		echo '</tr></thead>';

		$sql = $mysqli->query("SELECT * FROM `tb_ads_tests_visits` WHERE `ident`='$id' ORDER BY `id` DESC LIMIT $start_pos, $perpage") or die($mysqli->error);
		if($sql->num_rows>0) {
			while ($row_v = $sql->fetch_array()) {
				echo '<tr align="center">';
					echo '<td align="center" nowrap="nowrap" style="border-right:solid 1px #DDDDDD;">';
						echo '<span style="font-size:12px; color: #1874CD; cursor: pointer;" onClick="BanIdGet('.$row_v["user_id"].');"><b>'.$row_v["user_name"].'</b></span>';
					echo '</td>';

					echo '<td align="center" nowrap="nowrap" style="border-left:solid 2px #FFFFFF;">';
						echo '<span style="font-size:12px; color: #1874CD; cursor: pointer;">'.$row_v["user_id"].'</span>';
					echo '</td>';

					echo '<td nowrap="nowrap" align="center">';
						if( DATE("d.m.Y", $row_v["time_end"]) == DATE("d.m.Y", time()) ) {
							echo '<span style="color:#006400;">Сегодня</span>, '.DATE("в H:i", $row_v["time_end"]);
						}elseif( DATE("d.m.Y", $row_v["time_end"]) == DATE("d.m.Y", (time()-24*60*60)) ) {
							echo '<span style="color:#000080;">Вчера</span>, '.DATE("в H:i", $row_v["time_end"]);
						}else{
							echo '<span style="color:#363636;">'.DATE("d.m.Y", $row_v["time_end"]).'</span> '.DATE("H:i", $row_v["time_end"]);;
						}
					echo '</td>';

					echo '<td nowrap="nowrap" align="center">';
						echo date_ost(($row_v["time_end"]-$row_v["time_start"]), 1);
					echo '</td>';

					$record = geoip_record_by_addr($gi, $row_v["ip"]);

					if($record==false) {
						$country_code="";
					}else{
						$country_code = @$record->country_code;
					}

					echo '<td align="left" style=""><img src="//'.$_SERVER["HTTP_HOST"].'/img/flags/'.strtolower($country_code).'.gif" alt="'.get_country($country_code).'" title="'.get_country($country_code).'" align="absmiddle" width="16" height="11" style="margin:0; padding:0; padding-left:8px; border:none;" />&nbsp;'.$row_v["ip"].'</td>';

					echo '<td align="center">'.($row_v["status"]==-1 ? '<span style="color: #E32636;">Провалил</span>' : '<span style="color: #03C03C;">Прошёл</span>').'</td>';
				echo '</tr>';
			}
		}else{
			echo '<tr align="center">';
				echo '<td colspan="6"><b>Нет данных!</b></td>';
			echo '</tr>';
		}
		echo '</table>';

		if($count>$perpage) {universal_link_bar($count, $page, $_SERVER["PHP_SELF"], $perpage, 10, '&page=', "?op=$op&option=statistics&id=$id");}
	}else{
		echo '<span class="msg-error">Рекламная площадка № '.$id.' не найдена</span>';
	}

	exit();
}else{
	echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Редактирование оплачиваемых тестов</b></h3>';
}

$metode = false;
$search = false;
$operator = 0;
$WHERE_ADD = false;
$WHERE_ADD_to_get = false;

if(isset($_POST["search"]) && isset($_POST["metode"])) {
	$metode = isset($_POST["metode"]) ? $mysqli->real_escape_string(trim($_POST["metode"])) : false;
	$search = isset($_POST["search"]) ? $mysqli->real_escape_string(trim($_POST["search"])) : false;
	$operator = isset($_POST["operator"]) ? intval($mysqli->real_escape_string(trim($_POST["operator"]))) : 0;

	if($metode != "" && $search != false) {
		if($operator == "0") {
			$WHERE_ADD = " AND `$metode`='$search'";
		}else{
			$WHERE_ADD = " AND `$metode` LIKE '%$search%'";
		}
		$WHERE_ADD_to_get = "&metode=$metode&operator=$operator&search=$search";
	}
}
if(isset($_GET["search"]) && isset($_GET["metode"])) {
	$metode = isset($_GET["metode"]) ? $mysqli->real_escape_string(trim($_GET["metode"])) : false;
	$search = isset($_GET["search"]) ? $mysqli->real_escape_string(trim($_GET["search"])) : false;
	$operator = isset($_GET["operator"]) ? intval($mysqli->real_escape_string(trim($_GET["operator"]))) : false;

	if($metode != "" && $search != false) {
		if($operator == "0") {
			$WHERE_ADD = " AND `$metode`='$search'";
		}else{
			$WHERE_ADD = " AND `$metode` LIKE '%$search%'";
		}
		$WHERE_ADD_to_get = "&metode=$metode&operator=$operator&search=$search";
	}
}

require("navigator/navigator.php");
$perpage = 25;
$sql_p = $mysqli->query("SELECT `id` FROM `tb_ads_tests` WHERE `status`>'0' $WHERE_ADD") or die($mysqli->error);
$count = $sql_p->num_rows;
$pages_count = ceil($count / $perpage);
$page = (isset($_GET["page"]) && preg_match("|^[0-9\-]{1,11}$|", trim($_GET["page"]))) ? intval(trim($_GET["page"])) : "1";
if ($page > $pages_count | $page<0) $page = $pages_count;
$start_pos = ($page - 1) * $perpage;
if($start_pos<0) $start_pos = 0;

?>
<script type="text/javascript" language="JavaScript">
var s_h = false;
var new_id = false;

function LoadInfo(id, type, op) {
	if(s_h==(id + op)) {
		s_h = false;
		$("#load-info"+id).hide();
		$("#mess-info"+id).html("");
	} else {
		if(s_h && new_id) {
			$("#load-info"+new_id).hide();
			$("#mess-info"+new_id).html("");
		}
		$.ajax({
			type: "POST", url: "ajax_admin/ajax_json_adv.php?rnd="+Math.random(), 
			data: { 'op': op, 'type': type, 'id': id }, 
			dataType: 'json',
			beforeSend: function() { $("#loading").slideToggle(); }, 
			error: function() {
				$("#loading").slideToggle();
				new_id = id; s_h = id + op;

				$("html, body").animate({scrollTop: $("#adv_dell"+id).offset().top-3}, 700);

				$("#load-info"+id).show(); 
				$("#mess-info"+id).html('<span class="msg-error">Ошибка обработки данных!</span>');
				return false;
			}, 
			success: function(data) { 
				$("#loading").slideToggle();

				if(op == "GoEdit") {
					new_id = id; s_h = id;

					if (data.result == "OK") {
						document.location.href = "<?php echo $_SERVER["PHP_SELF"]."?op=$op&option=edit&id=";?>"+id+"<?php echo ($page>1 ? "&page=$page" : false);?>";
					} else {
						$("html, body").animate({scrollTop: $("#adv_dell"+id).offset().top-3}, 700);
						$("#load-info"+id).show();
						if(data.message) { $("#mess-info"+id).html('<span class="msg-error">' + data.message + '</span>'); }
						else { $("#mess-info"+id).html('<span class="msg-error">Ошибка обработки данных!</span>'); }
					}
				} else {
					if(op == "GoDel") {
						new_id = id; s_h = id;
					}else{
						new_id = id; s_h = id + op;
					}

					$("html, body").animate({scrollTop: $("#adv_dell"+id).offset().top-3}, 700);
					$("#load-info"+id).show();

					if (data.result == "OK") {
						if(data.message) { $("#mess-info"+id).html(data.message); }
						else { $("#mess-info"+id).html('<span class="msg-error">Ошибка обработки данных!</span>'); }
					} else {
						if(data.message) { $("#mess-info"+id).html('<span class="msg-error">' + data.message + '</span>'); }
						else { $("#mess-info"+id).html('<span class="msg-error">Ошибка обработки данных!</span>'); }
					}
				}
			}
		});
	}
	return false;
}

function PlayPause(id, type, op, lock) {
	if (lock && !confirm("Разблокировать рекламную площадку ID: "+id+" ?")) {
		return false;
	}

	$.ajax({
		type: "POST", url: "ajax_admin/ajax_json_adv.php?rnd="+Math.random(), 
		data: { 'op': op, 'type': type, 'id': id }, 
		dataType: 'json', 
		beforeSend: function() { $("#loading").slideToggle(); }, 
		error: function() { $("#loading").slideToggle(); alert("Ошибка обработки данных AJAX!"); return false; }, 
		success: function(data) { 
			$("#loading").slideToggle();
			if (data.result == "OK") { 
				$("#playpauseimg"+id).html(data.status);
				if(data.message) { alert(data.message); }
				if(lock) {
					$("#lock-"+id).attr({class: "adv-lock", title: "Заблокировать тест"});
					$("#load-info"+id).hide();
					$("#mess-info"+id).html("");
					s_h = false; new_id = false;
				}
			} else { 
				if(data.message) { alert(data.message); }
				else { alert("Ошибка обработки данных!"); return false; }
			}
			return false;
		}
	});
}

function ClearStat(id, type, op, count1, count2) {
	if (count1 == 0 && count2 == 0) {
		alert("Счётчик этой площадки уже равен 0");
	} else if (confirm("Обнулить счетчик просмотров рекламной площадки ID: "+id+" ?")) {
		$.ajax({
			type: "POST", url: "ajax_admin/ajax_json_adv.php?rnd="+Math.random(), 
			data: { 'op': op, 'type': type, 'id': id }, 
			dataType: 'json', 
			beforeSend: function() { $("#loading").slideToggle(); }, 
			error: function() { $("#loading").slideToggle(); alert("Ошибка обработки данных AJAX!"); return false; }, 
			success: function(data) { 
				$("#loading").slideToggle();
				if (data.result == "OK") {
					$("#g_stat"+id).html('0');
					$("#b_stat"+id).html('0');
					alert("Счетчик просмотров рекламной площадки ID: "+id+" успешно сброшен!");
					return false;
				} else {
					if(data.message) { alert(data.message); }
					else { alert("Ошибка обработки данных!"); return false; }
				}
			}
		});
	}
}

function AddMoney(id, type, op){
	$("#info-msg-addmoney").html("").hide();

	var money_add = $.trim($("#money_add").val());
	money_add = str_replace(",", ".", money_add);
	money_add = money_add.match(/(\d+(\.)?(\d){0,2})?/);
	money_add = money_add[0] ? money_add[0] : '';
	$("#money_add").val(money_add);
	money_add = number_format(money_add, 2, ".", "");

	if (id != undefined && type != undefined && op != undefined) {
		if (money_add < <?=$tests_min_pay;?>) {
			$("#info-msg-addmoney").html('<span class="msg-error">Минимальная сумма пополнения - <?=$tests_min_pay;?> руб.</span>').slideToggle(300);
			HideMsg("info-msg-addmoney", 3000);
			return false;
		} else {
			$.ajax({
				type: "POST", url: "ajax_admin/ajax_json_adv.php?rnd="+Math.random(), 
				data: { 'op': op, 'type': type, 'id': id, 'money_add':money_add }, 
				dataType: 'json', 
				beforeSend: function() { $("#loading").slideToggle(); }, 
				error: function() { $("#loading").slideToggle(); alert("Ошибка обработки данных AJAX!"); return false; }, 

				success: function(data) {
					$("#loading").slideToggle();

					if (data.result == "OK") {
						$("#S_H_M"+id).attr("class", "add-money");
						if(data.money) $("#all_sum_in"+id).html(data.money); 
						if(data.totals) $("#count_totals"+id).html(data.totals);
						if(data.balance) $("#count_balance"+id).html(data.balance);
						if(data.goods_out) $("#g_stat"+id).html(data.goods_out);
						if(data.bads_out) $("#b_stat"+id).html(data.bads_out);

						$("#info-msg-addmoney").html('<span class="msg-ok">Бюджет рекламной площадки успешно пополнен!</span>').slideToggle(300);

						setTimeout(function() {
							$("#info-msg-addmoney").html("").hide();
							$("#S_H_M"+id).click();
						}, 1500);

						return false;
					} else {
						if(data.message) {
							$("#info-msg-addmoney").html('<span class="msg-error">' + data.message + '</span>').slideToggle(300);
							HideMsg("info-msg-addmoney", 3000);
							return false;
						} else {
							$("#info-msg-addmoney").html('<span class="msg-error">Ошибка обработки данных!</span>').slideToggle(300);
							HideMsg("info-msg-addmoney", 3000);
							return false;
						}
					}
				}
			});
		}
	}
}

function DelCash(id, type, op){
	var cashback = $("#cashback").prop("checked") == true ? 1 : 0;

	if (confirm("Вы уверены что хотите удалить рекламну площадку ID: "+id+" ?")) {
		$.ajax({
			type: "POST", url: "ajax_admin/ajax_json_adv.php?rnd="+Math.random(), 
			data: { 'op': op, 'type': type, 'id': id, 'cashback':cashback }, 
			dataType: 'json', 
			beforeSend: function() { $("#loading").slideToggle(); }, 
			error: function() { $("#loading").slideToggle(); alert("Ошибка обработки данных AJAX!"); return false; }, 
			success: function(data) { 
				$("#loading").slideToggle();
				if (data.result == "OK") {
					$("#adv_dell"+id).hide(); $("#load-info"+id).hide(); $("#mess-info"+id).html("");
					if(data.message) { alert(data.message); }
				} else {
					if(data.message) { alert(data.message); }
					else { alert("Ошибка обработки данных!"); return false; }
				}
			}
		});
	}
}

function Lock(id, type, op){
	var msg_lock = $.trim($("#msg_lock").val());

	if (!msg_lock | msg_lock == false) {
		$("#msg_lock").focus().attr("class", "err");
		alert("Укажите причину блокировки!");
	} else {
		$.ajax({
			type: "POST", url: "ajax_admin/ajax_json_adv.php?rnd="+Math.random(), 
			data: { 'op': op, 'type': type, 'id': id, 'msg_lock':msg_lock }, 
			dataType: 'json', 
			beforeSend: function() { $("#loading").slideToggle(); }, 
			error: function() { $("#loading").slideToggle(); alert("Ошибка обработки данных AJAX!"); return false; }, 
			success: function(data) { 
				$("#loading").slideToggle();
				if (data.result == "OK") {
					$("#load-info"+id).hide();
					$("#mess-info"+id).html("");
					$("#playpauseimg"+id).html('<span class="adv-block" title="Рекламная площадка заблокирована" onClick="PlayPause('+id+', \'tests\', \'PlayPause\', \'1\');"></span>');
					$("#lock-"+id).attr({class: "adv-unlock", title: "Просмотр информации о блокировки"});
					s_h = false; new_id = false;

					//if(data.message) { alert(data.message); }
				} else {
					if(data.message) { alert(data.message); }
					else { alert("Ошибка обработки данных!"); return false; }
				}
			}
		});
	}
}

function DelClaims(id, type, op) {
	if (confirm("Удалить жалобу?")) {
		$.ajax({
			type: "POST", url: "ajax_admin/ajax_json_adv.php?rnd="+Math.random(), 
			data: { 'op': op, 'type': type, 'id': id }, 
			dataType: 'json', 
			success: function(data) {
				if (data.result=="OK") {
					if(data.count_claims==0) {
						$("#LoadModalClaimsTest").modalpopup('close');
						$("#dellclaims-"+data.ident).remove();
						$("#viewclaims-"+data.ident).remove();
					}
				}else{
					alert(data.message);
				}
			}
		});
	}
}

function DelAllClaims(id, type, op) {
	if (confirm("Удалить все жалобы?")) {
		$.ajax({
			type: "POST", url: "ajax_admin/ajax_json_adv.php?rnd="+Math.random(), 
			data: { 'op': op, 'type': type, 'id': id }, 
			dataType: 'json', 
			success: function(data) {
				if (data.result=="OK") {
					$("#dellclaims-"+id).remove();
					$("#viewclaims-"+id).remove();
				}else{
					alert(data.message);
				}
			}
		});
	}
}

function ViewClaims(id, type, op) {
	$.ajax({
		type: "POST", url: "ajax_admin/ajax_json_adv.php?rnd="+Math.random(), 
		data: { 'op': op, 'type': type, 'id': id }, 
		dataType: 'json', 
		success: function(data) {
			if (data.message) {
				$("#LoadModalClaimsTest").html(data.message).show();
				StartModal();
				WinHeight = Math.ceil($.trim($(window).height()));
				ModalHeight = Math.ceil($.trim($("#table-content").height()));
				if((ModalHeight+100) >= WinHeight) ModalHeight = WinHeight-100;
				$("#table-content").css("height", ModalHeight+"px");
			}
		}
	});
}

function StartModal() {
	$("#LoadModalClaimsTest").modalpopup({
		closeOnEsc: false,
		closeOnOverlayClick: false,
		beforeClose: function(data, el) {
			$("#LoadModalClaimsTest").hide();
			return true;
		}
	});
}

</script>
<?php

$sql_tests = $mysqli->query("SELECT * FROM `tb_ads_tests` WHERE `status`>'0' $WHERE_ADD ORDER BY `id` DESC LIMIT $start_pos, $perpage") or die($mysqli->error);
$all_tests = $sql_tests->num_rows;

echo '<table class="adv-cab" style="margin:0; padding:0; margin-bottom:1px;"><tr>';
echo '<td align="left" width="230" valign="middle" style="border-right:solid 1px #DDDDDD;">';
	if($WHERE_ADD=="") {
		echo 'Тестов всего: <b>'.$count.'</b><br>Показано записей на странице: <b>'.$all_tests.'</b> из <b>'.$count.'</b>';
	}else{
	 	echo 'Найдено тестов: <b>'.$count.'</b><br>Показано записей на странице: <b>'.$all_tests.'</b> из <b>'.$count.'</b>';
	}
echo '</td>';
echo '<td align="center" valign="middle" style="border-left:solid 2px #FFFFFF;">';
	echo '<form method="post" action="'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'" id="newform">';
	echo '<table class="adv-cab" style="width:auto; margin:0; padding:0;">';
	echo '<tr align="center">';
		echo '<td nowrap="nowrap" width="60" style="margin:0; padding:2px; border:none;"><b>Поиск по:</b></td>';
		echo '<td nowrap="nowrap" width="100" align="center" style="margin:0; padding:2px; border:none;">';
			echo '<select name="metode" style="text-align:left; padding-left:3px;">';
				echo '<option value="id" '.("id" == $metode ? 'selected="selected"' : false).'>ID</option>';
				echo '<option value="merch_tran_id" '.("merch_tran_id" == $metode ? 'selected="selected"' : false).'>№ счета</option>';
				echo '<option value="status" '.("status" == $metode ? 'selected="selected"' : false).'>Статус</option>';
				echo '<option value="wmid" '.("wmid" == $metode ? 'selected="selected"' : false).'>WMID</option>';
				echo '<option value="username" '.("username" == $metode ? 'selected="selected"' : false).'>Логин</option>';
			echo '</select>';
		echo '</td>';

		echo '<td nowrap="nowrap" width="100" align="center" style="margin:0; padding:2px; border:none;">';
			echo '<select name="operator" style="text-align:center;">';
				echo '<option value="0" '.($operator == "0" ? 'selected="selected"' : false).' style="text-align:center;">=</option>';
				echo '<option value="1" '.($operator == "1" ? 'selected="selected"' : false).' style="text-align:center;">содержит</option>';
			echo '</select>';
		echo '</td>';

		echo '<td nowrap="nowrap" width="135" align="center" style="margin:0; padding:2px; border:none;"><input type="text" class="ok" style="height:18px; text-align:center;" name="search" value="'.$search.'"></td>';
		echo '<td nowrap="nowrap" width="85"  align="center" style="margin:0; padding:3px 0px 2px 6px; border:none;"><input type="submit" value="Поиск" class="sub-green" style="float:none;"></td>';
	echo '</tr>';

	echo '</table>';
	echo '</form>';
echo '</td>';

echo '<td align="center" width="160">';
	echo '<form method="get" action="">';
		echo '<input type="hidden" name="op" value="'.limpiar($_GET["op"]).'">';
		echo '<input type="submit" value="Очистить поиск" class="sub-blue160" style="float:none;">';
	echo '</form>';
echo '</td>';

echo '</tr>';
echo '</table>';
echo '<div align="center" style="margin-bottom:20px;">Для поиска по <b>статусу</b> указать: <b>1</b> [активные ссылки], <b>2</b> [на паузе], <b>3</b> [завершили показ], <b>4</b> [заблокированные]</div>';

if($count>$perpage) {universal_link_bar($count, $page, $_SERVER["PHP_SELF"], $perpage, 10, '&page=', "?op=$op$WHERE_ADD_to_get");}
echo '<table class="adv-cab" style="margin:1px auto;">';
echo '<tbody>';

if($sql_tests->num_rows>0) {
	while ($row = $sql_tests->fetch_assoc()) {

		echo '<tr id="adv_dell'.$row["id"].'">';
			echo '<td align="center" width="35">';
				echo '<div id="playpauseimg'.$row["id"].'">';
					if($row["status"]=="0") {
						echo '<span class="adv-play" title="Запустить показ рекламной площадки" onClick="alert_nostart();"></span>';
					}elseif($row["status"]=="1") {
						echo '<span class="adv-pause" title="Приостановить показ рекламной площадки" onClick="PlayPause('.$row["id"].', \'tests\', \'PlayPause\');"></span>';
					}elseif($row["status"]=="2") {
						echo '<span class="adv-play" title="Запустить показ рекламной площадки" onClick="PlayPause('.$row["id"].', \'tests\', \'PlayPause\');"></span>';
					}elseif($row["status"]=="3") {
						echo '<span class="adv-play" title="Запустить показ рекламной площадки" onClick="PlayPause('.$row["id"].', \'tests\', \'PlayPause\');"></span>';
					}elseif($row["status"]=="4") {
						echo '<span class="adv-block" title="Рекламная площадка заблокирована" onClick="PlayPause('.$row["id"].', \'tests\', \'PlayPause\', \'1\');"></span>';
					}
				echo '</div>';
			echo '</td>';

			echo '<td align="left">';
				echo '<div id="all_sum_in'.$row["id"].'" title="Сумма пополнений" style="float:right; display:block; font-weight:bold; color:#828282; cursor:pointer; margin-bottom:5px;">{'.number_format($row["money"], 2, ".", "`").'}</div>';

				echo '<img width="16" height="16" border="0" alt="" title="" style="margin:0; padding:0; padding-bottom:2px; padding-right:5px;" src="http://www.google.com/s2/favicons?domain='.@gethost($row["url"]).'" align="absmiddle" />';
				echo '<a class="'.($row["color"]==1 ? "adv-red" : "adv").'" href="'.$row["url"].'" target="_blank" title="'.$row["url"].'">'.$row["title"].'</a><br>';

				echo '<span class="info-text">';
					echo 'ID, Счет:&nbsp;<b>'.$row["id"].'</b>, <b>'.$row["merch_tran_id"].'</b>;&nbsp;&nbsp;';
					echo 'Цена за тест:&nbsp;<b>'.$row["cena_advs"].'</b>&nbsp;руб.;&nbsp;&nbsp;';
					//echo 'Сумма пополнений:&nbsp;<b>'.number_format($row["money"], 2, ".", "`").'</b>&nbsp;руб.;<br>';
					echo 'Осталось:&nbsp;<span id="count_totals'.$row["id"].'" title="Осталось" style="font-weight:bold;">'.number_format(floor(bcdiv($row["balance"],$row["cena_advs"])), 0, ".", "`").'</span>;&nbsp;&nbsp;';
					echo 'Пройдено:&nbsp;<span id="g_stat'.$row["id"].'" title="Пройдено" style="font-weight:bold;">'.number_format($row["goods_out"], 0, ".", "`").'</span>;&nbsp;&nbsp;';
					echo 'Провалено:&nbsp;<span id="b_stat'.$row["id"].'" title="Провалено" style="font-weight:bold;">'.number_format($row["bads_out"], 0, ".", "`").'</span><br>';

					echo 'Способ оплаты: <b>'.$system_pay[$row["method_pay"]].'</b>;&nbsp;&nbsp;';
					echo 'Рекламодатель: '.($row["wmid"]!=false ? ($row["username"]!=false ? "WMID: <b>".$row["wmid"]."</b>, Логин: <b>".$row["username"]."</b>" : "WMID: <b>".$row["wmid"]."</b>") : ($row["username"]!=false ? "Логин: <b>".$row["username"]."</b>" : "<span style=\"color:#CCC;\">не опеределен</span>"));

					if($row["claims"] > 0) echo ';&nbsp;&nbsp;Количество жалоб: <span style="color:#FF0000;">'.$row["claims"].'</span>';
				echo '</span>';

				echo '<span class="adv-dell" title="Удалить тест" onClick="LoadInfo('.$row["id"].', \'tests\', \'GoDel\');"></span>';

				echo '<span id="lock-'.$row["id"].'" class="adv-'.($row["status"]=="4" ? "unlock" : "lock").'" title="'.($row["status"]=="4" ? "Просмотр информации о блокировки" : "Заблокировать тест").'" onClick="LoadInfo('.$row["id"].', \'tests\', \'GoLock\');"></span>';

				echo '<span class="adv-edit" title="Редактировать тест" onClick="LoadInfo('.$row["id"].', \'tests\', \'GoEdit\');"></span>';
				echo '<span class="adv-erase" title="Сброс статистики" onClick="ClearStat('.$row["id"].', \'tests\', \'ClearStat\', '.$row["goods_out"].', '.$row["bads_out"].');"></span>';
				echo '<span class="adv-statistics" onClick="document.location.href=\'?op='.$op.'&option=statistics&id='.$row["id"].'\';" title="Список исполнителей"></span>';

				if($row["claims"] > 0) {
					echo '<span id="dellclaims-'.$row["id"].'" class="clear-claims" title="Удалить все жалобы" onClick="DelAllClaims(\''.$row["id"].'\', \'tests\', \'DelAllClaims\');"></span>';
					echo '<span id="viewclaims-'.$row["id"].'" class="view-claims" title="Просмотр жалоб" onClick="ViewClaims(\''.$row["id"].'\', \'tests\', \'ViewClaims\');"></span>';
				}

				echo '<span class="adv-info" title="Посмотреть подробное описание" onClick="LoadInfo('.$row["id"].', \'tests\', \'GetInfo\');"></span>';

			echo '</td>';
		echo '</tr>';

		echo '<tr id="hide'.$row["id"].'" style="display: none;"><td align="center" colspan="3"></td></tr>';

		echo '<tr id="load-info'.$row["id"].'" style="display: none;">';
			echo '<td align="center" colspan="3" class="ext-text">';
				echo '<div id="mess-info'.$row["id"].'"></div>';
			echo '</td>';
		echo '</tr>';

	}
}else{
	echo '<tr align="center">';
		echo '<td colspan="2"><b>Реклама не найдена!</b></td>';
	echo '</tr>';
}
echo '</tbody>';
echo '</table>';
if($count>$perpage) {universal_link_bar($count, $page, $_SERVER["PHP_SELF"], $perpage, 10, '&page=', "?op=$op$WHERE_ADD_to_get");}

?>