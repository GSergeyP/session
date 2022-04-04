//////////////////////////
////      SESSION     ////
//// Автор – GSergeyP ////
////   Версия 1.0.0   ////
//////////////////////////
var url = './session.php'; //Адрес хранения процедур sessionAction
var sessionAction, id_session;
id_session = localStorage.getItem('id_session'); //Получение значение id_session из localStorage по ключу