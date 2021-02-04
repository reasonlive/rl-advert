<?PHP

$user_id = $_SESSION['userID'];
$db->Query("SELECT * FROM tb_users WHERE id=$user_id ");
$prof_data = $db->FetchAssoc();

$ava = $prof_data['photo_big'] ? $prof_data['photo_big'] : $prof_data['avatar']; 
 
//echo $ava;

?>


<aside class='user-menu'>
  <div >
    <div style='display:flex;flex-direction: row;justify-content: space-between;'>

     <img class='ava' width=100 height=100 src="<?php echo $ava; ?>"> <p><?php echo $prof_data['username']; ?><br><br>  ID:<?php echo $prof_data['id']; ?></p>
     </div>
      <hr>
      <textarea style='width:200px;resize:none;' disabled><?php echo $_SERVER['HTTP_HOST']."/?r=". $prof_data['id']; ?></textarea>
    
    <br>
    

    <p>Основной счет: <span><?php echo $prof_data['money']; ?></span></p>
    <p>Рекламный счет: <span><?php echo $prof_data['money_rb']; ?></span></p>
    <p>Бонусы: <span><?php echo $prof_data['money_bonus']; ?></span></p>
    <hr>
  </div>
  <div class='menu-options'>
    
      <p class='menu-select'>Аккаунт <span class='mark'>&#9660;</span></p>
      <div class='link-opener' data-state='close'>
      <p><a href="/account/profile">Мой профиль</a></p>
      <p><a href="/account/money">Денежные операции</a></p>
      <p><a href="/account/members">Моя статистика</a></p>
      <p><a href="/account/history">История операций</a></p>
      <p><a href="/account/notes">Блокнот</a></p>
      <p><a style='color:red;' href="/account/holiday"><b>Уйти в отпуск</b></a></p>
      </div>

      <p class='menu-select'>Работа с рефералами <span class='mark'>&#9660;</span></p>
      <div class='link-opener' data-state='close'>

      <p><a href="">Рекламные материалы</a></p>
      <p><a href="">Мои рефералы</a></p>
      <p><a href="">Приветсвие для рефералов</a></p>
      <p><a href=""><b>Реф.Стена</b></a></p>
      <p><a href="">Рассылка рефералам</a></p>
      <p><a href="">Бонусы рефералам</a></p>
      <p><a href="">Выкуп у реферера</a></p>
      <p><a href="">Свободные пользователи</a></p>
      </div>

      <p class='menu-select'>Конкурсы для рефералов <span class='mark'>&#9660;</span></p>
      <div class='link-opener' data-state='close'>
      <p><a href="">Конкурсы от реферера</a></p>
     <p><a href="">Конкурсы для рефералов</a></p>
      </div>

      <p class='menu-select'>Биржа рефералов <span class='mark'>&#9660;</span></p>
      <div class='link-opener' data-state='close'>
      <p><a href="">Купить рефералов</a></p>
     <p><a href="">Продать рефералов</a></p>
      </div>

     

      <p class='menu-select'>Мои сообщения <span class='mark'>&#9660;</span></p>
      <div class='link-opener' data-state='close'>
      <p><a href="">Новое</a></p>
      <p><a href="">Входящие</a></p>
      <p><a href="">Исходящие</a></p>
      <p><a href="">Техподдержка</a></p>
      </div>

      <p class='menu-select'>Информация <span class='mark'>&#9660;</span></p>
      <div class='link-opener' data-state='close'>
      <p><a href="">Нарушители</a></p>
      <p><a href="">Пользователи по странам</a></p>
      </div>

      <p class='menu-select'>Копилка <span class='mark'>&#9660;</span></p>
      <div class='link-opener' data-state='close'>
      <p style='height:60px;font-size: 14px;color:white'>За каждые полные пополненные 1 руб. получите 3 руб. на SN счет для рекламы</p>
      <p><img src="" alt='pig'></p>
      <p>плюс</p>
      <p>минус</p>
      <p>пополнить копилку</p>
      <p><button>Пополнили</button> <span>+</span><button>Что это?</button></p>
      </div>
      <hr>
      <p><a href="/account/cabinet_ads">Управление рекламой</a></p>
      <p><a href="/account/1serfnet">Коды и ссылки</a></p>
      <p><a href="/account/hit">Статистика Трафа</a></p>
      <p><a href="/account/top10">Топ 10</a></p>
      <p><a href="/account/referals1">Рефералы</a></p>
      <p><a href="/account/reflinks">Баннеры</a></p>
      <p><a href="/account/historyvm">История</a></p>
      <p><a href="/account/wall?uid=<?php echo $user_id; ?>">Стена</a></p>
      
    </div>
  


</aside>

<script type="text/javascript">
  let openerElems = document.getElementsByClassName('menu-select');
  let heights = {};
  for(let i=0;i<openerElems.length;i++){
    let elem = openerElems[i].nextElementSibling;

    let fullHeight = window.getComputedStyle(elem).height;
    heights[i] = fullHeight;
    elem.style.height = `0px`;
  }

  //console.log(getComputedStyle(openerElems[0].nextElementSibling).height)


  function toggleMenu(e,pos){
    let elem = e.target.nextElementSibling;

    if(elem.dataset.state == 'open'){
          elem.style.height = `0px`;
          elem.dataset.state = 'close';
          document.getElementsByClassName('mark')[pos].innerHTML = '&#9660;'
    }else{
         elem.style.height = `${heights[pos]}`;
          elem.dataset.state = 'open';
          document.getElementsByClassName('mark')[pos].innerHTML = '&#9650;'
    }
  }

  
    for(let i=0;i<openerElems.length;i++){
      openerElems[i].addEventListener('click', function(e){
        toggleMenu(e,i);
      })
    }
  

  

</script>