<?php 

session_start();

$id = $_SESSION['userID'];

if(!$id or !is_int($id)){
	session_destroy();
	header("Location: /");
	exit;
}


spl_autoload_register(function($name){
  include($_SERVER['DOCUMENT_ROOT']. "/classes/_class.".$name.".php");
});

$func = new func;
$config = new config;
$db = new db($config->HostDB, $config->UserDB, $config->PassDB, $config->BaseDB);

$date = date('Y.m.d', time());
$ip = $func->UserIP;
$country_code = $func->CountryCode;

//include_once($_SERVER['DOCUMENT_ROOT'] . "/pay/auto_pay_req/wmxml.inc.php");


if(isset($_POST)){

//Удаление профиля
         if(isset($_POST['delete']) and preg_match('/^delete$/', $_POST['delete'])){

         $res =  $db->query("DELETE FROM `tb_users` WHERE id=$id  ");

         session_destroy();

         echo "success";
         exit;

         


          }

        //Изменение пароля

	if(isset($_POST['pass0'])){
		$received = (string)$_POST['pass0'];

		$db->query("SELECT password FROM `tb_users` WHERE id=$id and online=1 ");
		if($db->NumRows() < 1){
			echo "no such user";
			exit;
		}
		$pass = $db->FetchRow();

		$pass0 = $func->md5Password($received);

		if($pass0 == $pass){
			echo 'success';
			exit;
		}else{
			echo 'different passwords';
			exit;
		}

	}

	if(isset($_POST['pass2'])){
		$received = (string)$_POST['pass2'];
		
		$pass2 = $func->md5Password($received);
		
		$db->query("UPDATE tb_users SET password='$pass2' WHERE id=$id ");

		echo "success";
		exit;
	}

// Изменение личной информации
	if(isset($_POST['email']) or isset($_POST['username']) or isset($_POST['city']) or isset($_FILES['ava'])){

		

		$new_name = $_POST['username'] ? (string)$_POST['username'] : NULL;
		$new_city = $_POST['city'] ? (string)$_POST['city'] : NULL;
		$mail = isset($_POST['email']) ?  (string)$_POST['email'] : NULL;




		$db->query("SELECT username, city_title, avatar FROM `tb_users` WHERE id=$id and online=1 ");
		if($db->NumRows() < 1){
			echo 'fail';
			exit;
		}else{

			if($new_name and $new_city ){
				$db->query("UPDATE tb_users SET city_title='$new_city', username='$new_name' WHERE id=$id ");
			}elseif($new_name){
				$db->query("UPDATE tb_users SET username='$new_name' WHERE id=$id ");
			}elseif($new_city){
				$db->query("UPDATE tb_users SET city_title='$new_city' WHERE id=$id ");
			}

                        if($mail) $db->query("UPDATE tb_users SET email='$mail' WHERE id=$id ");
                                
                       //echo "success";
                      // exit;


		}

                  //если загружена аватарка
		if (isset($_FILES['ava']) and $_FILES['ava']['tmp_name']) {
			  $image = $_FILES['ava'];
			  // Получаем нужные элементы массива "image"
			  $fileTmpName = $_FILES['ava']['tmp_name'];
			  $errorCode = $_FILES['ava']['error'];
			  // Проверим на ошибки
			  if ($errorCode !== UPLOAD_ERR_OK || !is_uploaded_file($fileTmpName)) {
			    // Массив с названиями ошибок
			    $errorMessages = [
			      UPLOAD_ERR_INI_SIZE   => 'Размер файла превысил значение upload_max_filesize в конфигурации PHP.',
			      UPLOAD_ERR_FORM_SIZE  => 'Размер загружаемого файла превысил значение MAX_FILE_SIZE в HTML-форме.',
			      UPLOAD_ERR_PARTIAL    => 'Загружаемый файл был получен только частично.',
			      UPLOAD_ERR_NO_FILE    => 'Файл не был загружен.',
			      UPLOAD_ERR_NO_TMP_DIR => 'Отсутствует временная папка.',
			      UPLOAD_ERR_CANT_WRITE => 'Не удалось записать файл на диск.',
			      UPLOAD_ERR_EXTENSION  => 'PHP-расширение остановило загрузку файла.',
			    ];
			    // Зададим неизвестную ошибку
			    $unknownMessage = 'При загрузке файла произошла неизвестная ошибка.';
			    // Если в массиве нет кода ошибки, скажем, что ошибка неизвестна
			    $outputMessage = isset($errorMessages[$errorCode]) ? $errorMessages[$errorCode] : $unknownMessage;
			    // Выведем название ошибки
			    echo $outputMessage;
			    die($outputMessage);
			    exit;
			  } else {


			  		//Создадим ресурс FileInfo
					$fi = finfo_open(FILEINFO_MIME_TYPE);
	 
						// Получим MIME-тип
						$mime = (string) finfo_file($fi, $fileTmpName);
	 
					// Проверим ключевое слово image (image/jpeg, image/png и т. д.)
					if (strpos($mime, 'image') === false) die('Можно загружать только изображения.');


					// Результат функции запишем в переменную
					$image = getimagesize($fileTmpName);
					 
					// Зададим ограничения для картинок
					$limitBytes  = 1024 * 1024 * 5;
					$limitWidth  = 1280;
					$limitHeight = 768;
					 
					// Проверим нужные параметры
					//if (filesize($fileTmpName) > $limitBytes) die('Размер изображения не должен превышать 5 Мбайт.');
					//if ($image[1] > $limitHeight)             die('Высота изображения не должна превышать 768 точек.');
					//if ($image[0] > $limitWidth)              die('Ширина изображения не должна превышать 1280 точек.');


					$name = md5_file($fileTmpName);
	 
					// Сгенерируем расширение файла на основе типа картинки
					$extension = image_type_to_extension($image[2]);
					 
					// Сократим .jpeg до .jpg
					$format = str_replace('jpeg', 'jpg', $extension);

					$fullname = '/img/new/'. $name . $format;
					 
					// Переместим картинку с новым именем и расширением в папку /img/new/
					if (!move_uploaded_file($fileTmpName, $_SERVER['DOCUMENT_ROOT'] . $fullname)) {
						echo "При записи изображения на диск произошла ошибка.";
					  die('При записи изображения на диск произошла ошибка.');
					  exit;
					}else{

					 $db->query("UPDATE tb_users SET avatar='$fullname' WHERE id=$id ");
					 echo "success";
					 exit;
					}







			    
			  }
		};

          

         echo "success";
         exit;

	}
	
	$arr = $_POST;
	$record_vals = array();
	
	foreach($arr as $i => $item){
		switch($i){
			case "wm": $record_vals[$i.'_purse'] = $item;break; //webmoney
			case "wmid": $record_vals[$i] = $item;break; //wmid
			case "ym": $record_vals[$i.'_purse'] = $item;break; //yandexmoney
			case "py": $record_vals[$i.'_purse'] = $item;break; //payeer
			case "pm": $record_vals[$i.'_purse'] = $item;break; //perfectmoney
			case "qw": $record_vals[$i.'_purse'] = $item;break; //qiwi
			case "ac": $record_vals[$i.'_purse'] = $item;break; //advcash
			case "me": $record_vals[$i.'_purse'] = $item;break; //maestro
			case "ms": $record_vals[$i.'_purse'] = $item;break; //mastercard
			case "vs": $record_vals[$i.'_purse'] = $item;break; //visa
			case "be": $record_vals[$i.'_purse'] = $item;break; //beeline
			case "mt": $record_vals[$i.'_purse'] = $item;break; //mts
			case "mg": $record_vals[$i.'_purse'] = $item;break; //megafon
			case "tl": $record_vals[$i.'_purse'] = $item;break; //tele2
			default: $record_vals = array();
		}
		exit;
	}

	$str = "";
	foreach($record_vals as $purse => $val){
		$str .= $purse. "='".$val."'," ;
	}

	$str = substr($str, 0,-1);

	
	if(count($record_vals) > 0){

	$db->query("UPDATE `tb_users` SET $str WHERE id=$id ");
	echo "success";
	exit;

	}else{
		echo "fail";
		exit;
	}


}


function getRandomFileName($path)
{
  $path = $path ? $path . '/' : '';
  do {
    $name = md5(microtime() . rand(0, 9999));
    $file = $path . $name;
  } while (file_exists($file));
 
  return $name;
}




?>