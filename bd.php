<?php
// Отправляем браузеру правильную кодировку,
// файл index.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // В суперглобальном массиве $_GET PHP хранит все параметры, переданные в текущем запросе через URL.
    if (!empty($_GET['save'])) {
        // Если есть параметр save, то выводим сообщение пользователю.
        print('Спасибо, результаты сохранены!');
    }
    // Включаем содержимое файла index.php.
    include('index.php');
    // Завершаем работу скрипта.
    exit();
}
// Иначе, если запрос был методом POST, т.е. нужно проверить данные и сохранить их в XML-файл.
// Проверяем ошибки.
$errors = FALSE;
if (empty($_POST['fio'])) {
    print('заполни фио.<br/>');
    $errors = TRUE;
}
if (!preg_match("/@/",$_POST['tel'])) {
    print('заполни телефон.<br/>');
    $errors = TRUE;
}
if (!preg_match("/@/",$_POST['email'])) {
    print('заполни email.<br/>');
    $errors = TRUE;
}
if ($_POST['date'] == "date") {
    print('выбери дату рождения.<br/>');
    $errors = TRUE;
}
if (empty($_POST['gender'])) {
    print('выбери пол.<br/>');
    $errors = TRUE;
}
if (empty($_POST['plang'])) {
    print('выбери любимый язык программирования.<br/>');
    $errors = TRUE;
}
if (empty($_POST['bio'])) {
    print('расскажи о себе.<br/>');
    $errors = TRUE;
}
if (empty($_POST['check'])) {
    print('ознакомься с политикой конфиденциальности.<br/>');
    $errors = TRUE;
}

if ($errors) {
    // При наличии ошибок завершаем работу скрипта.
    exit();
}

$user = 'u70422';
$pass = '4545635';
$db = new PDO('mysql:host=localhost;dbname=u47560', $user, $pass, array(PDO::ATTR_PERSISTENT => true));

// Подготовленный запрос. Не именованные метки.
try {
    $stmt = $db->prepare("INSERT INTO my_table SET fio = ?, email = ?, date = ?, gender = ?, plang = ?, bio = ?");
    $stmt->execute(array(
         $_POST['fio'],
         $_POST['email'],
         $_POST['date'],
         $_POST['gender'],
         $_POST['plang'],
         $_POST['bio'],
    ));  
   
    $stmt = $db->prepare("INSERT INTO plang SET fio = ?");
    $stmt->execute(array(
        $_POST['plang'] = implode(', ', $_POST['plang']),
    ));
} 
catch (PDOException $e) {
    print('Error : ' . $e->getMessage());
    exit();
}

//  stmt - это "дескриптор состояния".

// Делаем перенаправление.
// Если запись не сохраняется, но ошибок не видно, то можно закомментировать эту строку чтобы увидеть ошибку.
// Если ошибок при этом не видно, то необходимо настроить параметр display_errors для PHP.
header('Location: ?save=1');
?>
