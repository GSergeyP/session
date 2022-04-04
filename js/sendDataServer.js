//////////////////////////
////      SESSION     ////
//// Автор – GSergeyP ////
////   Версия 1.0.0   ////
//////////////////////////
function sendDataServer(element){
    sessionAction = element.id; //Получение id по клику
    //Отправка запроса sessionAction
    fetch(url, {  
        method: 'post',   
        body: '{"sessionAction":"'+sessionAction+'","id_session":"'+id_session+'"}',
        headers: new Headers()   
    }).then(function (response){
        return response.json();
    }).then(function repeat(data){
        id_session = data.id_session; //Получение переменной id_session

        if(id_session){
            localStorage.setItem('id_session', id_session); //Запись/Перезапись в localStorage
            sessionOpen();
        }
        else{
            localStorage.clear(); //Удаление в localStorage
            sessionClose();
        }
    })  
    .catch(function (error) {  
        console.log('Запрос не выполнен', error);  
    });
} 