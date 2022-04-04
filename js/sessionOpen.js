//////////////////////////
////      SESSION     ////
//// Автор – GSergeyP ////
////   Версия 1.0.0   ////
//////////////////////////
//Страница авторизованного пользователя, Закрытая часть
function sessionOpen(){
    sessionActionDelete();
    
    session = document.getElementById('sessionAction');
    session.insertAdjacentHTML('beforeEnd', '<div class="sessionOpen"><button type="submit" id="sessionUpdate" onClick = "sessionUpdate(this); return false;">Обнавить сессию</button><button type="submit" id="sessionClose" onClick = "sendDataServer(this); return false;">Выйти</button></div>'); 
    
    sessionСonfirmation(); //Запуск проверки существования сессии, Запуск таймера
}