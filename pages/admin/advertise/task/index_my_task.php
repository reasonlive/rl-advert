<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}


$username = (isset($_SESSION["userLog"])) ? uc($_SESSION["userLog"]) : false;
$partnerid = ( isset($_SESSION["partnerid"]) && intval(($_SESSION["partnerid"]))>0 ) ? intval(($_SESSION["partnerid"])) : "0";

if(isset($_GET["page"]) && limpiar($_GET["page"])!="") {
	if(limpiar($_GET["page"])=="add_task") {
		require("my_task/add_task.php");
	}

	if(limpiar($_GET["page"])=="edit_task") {
		require("my_task/edit_task.php");
	}

	if(limpiar($_GET["page"])=="task_view") {
		require("my_task/view_task.php");
	}

	if(limpiar($_GET["page"])=="del_task") {
		require("my_task/del_task.php");
	}

	if(limpiar($_GET["page"])=="addmoney_task") {
		require("my_task/addmoney_task.php");
	}

	if(limpiar($_GET["page"])=="active_task") {
		require("my_task/active_task.php");
	}

	if(limpiar($_GET["page"])=="pause_task") {
		require("my_task/pause_task.php");
	}

	if(limpiar($_GET["page"])=="up_task") {
		require("my_task/up_task.php");
	}

	if(limpiar($_GET["page"])=="task_stat") {
		require("my_task/view_task_stat.php");
	}

	if(limpiar($_GET["page"])=="task_get") {
		require("my_task/view_task_pay.php");
	}

	if(limpiar($_GET["page"])=="task_mod") {
		require("my_task/view_task_mod.php");
	}
}
?>
