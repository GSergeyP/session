<?php
//////////////////////////
////      SESSION     ////
//// Автор – GSergeyP ////
////   Версия 1.0.0   ////
//////////////////////////
require_once('cfg.php'); //Подключение к БД

$jsonData = file_get_contents('php://input'); //Поимка json строки
$data = json_decode($jsonData); //Преобразование строки JSON в переменную PHP

$sessionAction =  $data -> sessionAction; //Получение переменной sessionAction
$id_session = $data -> id_session; //Получение переменной id_session

class session{  
////Генератор случайных чисел, создание $id_session
    function randomNumber($length){				
        $chars = 'qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP'; 
        $size = strlen($chars) - 1; 
        $randomNumber = ''; 
        while($length--) {
            $randomNumber .= $chars[random_int(0, $size)]; 
        }
        return $randomNumber;
    }
////Получение IP клиента, создание $clientIp
    function ip(){
        $client  = @$_SERVER['HTTP_CLIENT_IP']; //определение IP пользователя, использующего прокси
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR']; //вторая попытка, в случае если первый способ не смог определить IP
        $remote  = @$_SERVER['REMOTE_ADDR']; //Получение реального IP-адрес клиента
        
        if(filter_var($client, FILTER_VALIDATE_IP)) $ip = $client;
        elseif(filter_var($forward, FILTER_VALIDATE_IP)) $ip = $forward;
        else $ip = $remote;

        return $ip;
    }
////Получение информации о типе и версии браузера и операционной системы клиента, создание $clientBrowser
    function userAgent(){
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    return $userAgent;
    }
////Ввод данных
    function dataEnter($id_session, $clientIp, $clientBrowser){
        global $conn;

        //Формирование SQL-запроса на создание сессии
        $result = mysqli_query($conn,"REPLACE INTO `session` SET `id_session` = '".$id_session."', `sessionCreationTime` = NOW(), `clientBrowser` = '".$clientBrowser."', `clientIp` = '".$clientIp."'");
        if(!$result) echo "Произошла ошибка: " . mysqli_error($conn);
    }
////Удаление данных
    function dataDelete($id_session){
        global $conn;

        //Создание шаблона запроса отправляемого на сервер MySQL с плейсхолдером
        //Формирование SQL-запроса на удаление сессии
        $stmt = $conn -> prepare("DELETE FROM `session` WHERE `id_session` = ?");

        //Передача значения в подготовленный запрос (плейсхолдер) 
        $stmt -> bind_param('s', $id_session);  

        //Запуск подготовленного запроса на выполнение
        $result = $stmt -> execute();       
        if(!$result) echo "Произошла ошибка: " .mysqli_error($conn);
    }
////Проверка наличия данных
    function dataHave($id_session){
        global $conn;

        //Создание шаблона запроса отправляемого на сервер MySQL с плейсхолдером
        //Формирование SQL-запроса на проверку существования сессии
        $stmt = $conn -> prepare("SELECT COUNT(*) FROM `session` WHERE `id_session` = ?");
        //Передача значения в подготовленный запрос (плейсхолдер) 
        $stmt -> bind_param('s', $id_session);
        //Запуск подготовленного запроса на выполнение
        $stmt -> execute();
        //Получение результата
        $result = $stmt->get_result();

        if($result){
            //Проверка наличая записи в БД
            $resultFinal = mysqli_fetch_row($result);
            $total = $resultFinal[0]; // всего записей
            return $total;
        }
        else echo "Произошла ошибка: " .mysqli_error($conn);
    }
////Проверка устаривания данных
    function dataAging($id_session){
        global $conn;

        //Создание шаблона запроса отправляемого на сервер MySQL с плейсхолдером
        //Формирование SQL-запроса на проверку устаривания сессии 
        $stmt = $conn -> prepare("SELECT COUNT(*) FROM `session` WHERE `id_session` = ? AND `sessionCreationTime` > DATE_SUB(NOW(), INTERVAL 1 MINUTE)"); //Указывается интервал проверки
        //Передача значения в подготовленный запрос (плейсхолдер) 
        $stmt -> bind_param('s', $id_session);
        //Запуск подготовленного запроса на выполнение
        $stmt -> execute();
        //Получение результата
        $result = $stmt->get_result();

        if($result){
            //Проверка наличая записи в БД
            $resultFinal = mysqli_fetch_row($result);
            $total = $resultFinal[0]; // всего записей
            return $total;
        }
        else echo "Произошла ошибка: " .mysqli_error($conn);
    }
////Удаление мусорных данных  
    function dataСleaning(){
        global $conn;

        //Формирование SQL-запроса на удаление мусорных данных
        $result = mysqli_query($conn, "DELETE FROM `session` WHERE `sessionCreationTime` < DATE_SUB(NOW(), INTERVAL 1 MINUTE)");
      
        if(!$result) echo "Произошла ошибка: " .mysqli_error($conn);
    }
////Создание сессии//////////////////////////////////////////////////////////////////////////////////////////
    public function sessionOpen(){ 
        global $conn;
        //Обращение к функции генератор случайных чисел
        $id_session = session::randomNumber(30); //Задание количества символов
        //Обращение к функции получение IP клиента
        $clientIp = session::ip();
        //Получение информации о типе и версии браузера и операционной системы клиента
        $clientBrowser = session::userAgent();

        //Запуск функции ввод данных
        session::dataEnter($id_session, $clientIp, $clientBrowser);

        //Формирование массива id, Идентификация подключившегося к сессии
        $id = array('id_session' => $id_session);

        //Формирование строки json
        echo json_encode($id);


        //Получение случайного числа
        $randomNumber = mt_rand(0, 10);

        if($randomNumber == 5) session::dataСleaning(); //Запуск функции удаление мусорных данных  

        //Отключение от ДБ
        $conn -> close();
    }
////Удаление сессии//////////////////////////////////////////////////////////////////////////////////////////
    public function sessionClose($id_session){ 
        global $conn; 
        //Запуск функции удаление данных
        session::dataDelete($id_session);

        //Формирование массива id, Идентификация подключившегося к сессии
        $id = array('id_session' => '');

        //Формирование строки json
        echo json_encode($id);

        //Отключение от ДБ
        $conn -> close();
    }
////Принудительное обнавление сессии/////////////////////////////////////////////////////////////////////////
    public function sessionUpdate($id_session){
        global $conn;
        //Проверка наличая данных
        $total = session::dataHave($id_session); 
        if($total == 1){
            //Проверка устаривания данных
            $total = session::dataAging($id_session); 
            if($total == 1) {
                //Запуск функции удаление данных
                session::dataDelete($id_session);

                //Обращение к функции генератор случайных чисел
                $id_session = session::randomNumber(30); //Задание количества символов
                //Обращение к функции получение IP клиента
                $clientIp = session::ip();
                //Получение информации о типе и версии браузера и операционной системы клиента
                $clientBrowser = session::userAgent();

                //Запуск функции ввод данных
                session::dataEnter($id_session, $clientIp, $clientBrowser);

                //Формирование массива id, Идентификация подключившегося к сессии
                $id = array('id_session' => $id_session);

                //Формирование строки json
                echo json_encode($id);
            }
            else{
               //Запуск функции удаление данных
               session::dataDelete($id_session); 

                //Формирование массива id, Идентификация подключившегося к сессии
                $id = array('id_session' => '');
                //Формирование строки json
                echo json_encode($id);
            }
        }
        else {
            //Формирование массива id, Идентификация подключившегося к сессии
            $id = array('id_session' => '');
            //Формирование строки json
            echo json_encode($id);
        }
        //Отключение от ДБ
        $conn -> close();
    }
////Проверка существования сессии////////////////////////////////////////////////////////////////////////////
    public function sessionСonfirmation($id_session){
        global $conn;
        //Проверка наличая данных
        $total = session::dataHave($id_session); 
        if($total == 1){
            //Проверка устаревания данных
            $total = session::dataAging($id_session); 
            if($total != 1){
                //Запуск функции удаление данных
                session::dataDelete($id_session);

                //Формирование массива id, Идентификация подключившегося к сессии
                $id = array('id_session' => '');
                //Формирование строки json
                echo json_encode($id);
            }
            else{
                //Формирование массива id, Идентификация подключившегося к сессии
                $id = array('id_session' => 'timerUpdate');
                //Формирование строки json
                echo json_encode($id);
            }
        }
        else {
            //Формирование массива id, Идентификация подключившегося к сессии
            $id = array('id_session' => '');
            //Формирование строки json
            echo json_encode($id);
        }
        //Отключение от ДБ
        $conn -> close();
    }
}
$session = new session();
$session -> $sessionAction($id_session);
?>