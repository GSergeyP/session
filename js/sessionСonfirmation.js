//////////////////////////
////      SESSION     ////
//// Автор – GSergeyP ////
////   Версия 1.0.0   ////
//////////////////////////
//Проверка существования сессии
function sessionСonfirmation(){
    setTimeout(function() { //Запуск таймера
        id_session = localStorage.getItem('id_session'); //Получение значение id_session из localStorage по ключу
        fetch(url, {  
            method: 'post',   
            body: '{"sessionAction":"sessionСonfirmation","id_session":"'+id_session+'"}',
            headers: new Headers()   
        }).then(function (response){
            return response.json();
        }).then(function repeat(data){
            id_session = data.id_session; //Получение переменной id_session
            if(!id_session){
                localStorage.clear(); //Удаление в localStorage
                sessionClose();

            }
            else sessionСonfirmation(); //Запуск проверки существования сессии, Запуск таймера
        })  
        .catch(function (error) {  
            console.log('Запрос не выполнен', error);  
        });

    }, 70000); //Установка времени
}