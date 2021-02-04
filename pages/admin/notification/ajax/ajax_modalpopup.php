<?php
@session_start();
error_reporting (E_ALL);
header("Content-type: text/html; charset=utf-8");
if(!DEFINED("ROOT_DIR")) DEFINE("ROOT_DIR", $_SERVER["DOCUMENT_ROOT"]);
require(ROOT_DIR."/config.php");
require(ROOT_DIR."/funciones.php");
require_once(ROOT_DIR."/bbcode/bbcode.lib.php");
sleep(0);

if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest") {
	if(isset($_SESSION["userLog"]) && isset($_SESSION["userPas"])) {
		$username = (isset($_SESSION["userLog"]) && preg_match("|^[a-zA-Z0-9\-_-]{3,20}$|", trim($_SESSION["userLog"]))) ? uc($_SESSION["userLog"]) : false;
		$option = ( isset($_POST["op"]) && preg_match("|^[a-zA-Z0-9\-_]{3,20}$|", limpiar($_POST["op"])) ) ? limpiar($_POST["op"]) : false;
		$id = ( isset($_POST["id"]) && preg_match("|^[\d]{1,11}$|", trim($_POST["id"])) ) ? intval(limpiar(trim($_POST["id"]))) : false;

		function desc_bb($desc) {
			$desc = new bbcode($desc);
			$desc = $desc->get_html();
			$desc = str_replace("&amp;", "&", $desc);
			return $desc;
		}

		if($option=="LoadNot" && $id!=false) {
			$sql = $mysqli->query("SELECT * FROM `tb_notification` WHERE `id`='$id'");
			if($sql->num_rows>0) {
				$row = $sql->fetch_assoc();

				echo '<div class="box-modal" id="ModalNot" style="text-align:justify;">';
					echo '<div class="box-modal-title">'.$row["title"].'</div>';
					echo '<div class="box-modal-close modalpopup-close"></div>';
					echo '<div class="box-modal-content">';
						echo '<div align="center"><img src="'.$row["url_img"].'" alt="" title="" border="0" style="cursor:pointer;" onClick="GoNot(\''.$row["url"].'\'); return false;" /></div>';
						echo '<br>';
						echo desc_bb($row["description"]);
						echo '<br><br>';
						echo '<div class="sub-gray" onClick="GoNot(\''.$row["url"].'\'); return false;">Подробнее</div>';
					echo '</div>';
				echo '</div>';
			
				?><script type="text/javascript">
				document.getElementById("LoadModalNot").style.display = '';

				function GoNot(url) {
					$('#LoadModalNot').modalpopup('close');
					window.open(url, '_blank');
				}

				$('#LoadModalNot').modalpopup({
					closeOnEsc: false,
					closeOnOverlayClick: false,
					beforeClose: function(data, el) {
						document.getElementById("LoadModalNot").style.display = 'none';
						return true;
					}
				});
				</script><?
			}
		}
	}
}

?>