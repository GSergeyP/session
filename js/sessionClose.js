//////////////////////////
////      SESSION     ////
//// Автор – GSergeyP ////
////   Версия 1.0.0   ////
//////////////////////////
//Базовая страница, Окно ввода логина и пароля
function sessionClose(){
    sessionActionDelete();
    if (!id_session){
        session = document.getElementById('sessionAction');
        session.insertAdjacentHTML('beforeEnd', '<div class="sessionClose"><label>Авторизация</label><br /><br /><button type="submit" id="sessionOpen" onClick = "sendDataServer(this); return false;">Авторизация</button></div>'); 
    }
    else sessionOpen();  
}

//Запуск базовой страницы, Окна ввода логина и пароля
document.addEventListener('DOMContentLoaded', sessionClose);