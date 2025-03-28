Конечно! 😊 Давайте поэтапно разберем, как создать серверное приложение, которое будет сохранять данные из вашей формы в базу данных Oracle. Мы будем использовать язык PHP для выполнения серверной логики. Вот основные шаги:

### 1. Установите и настройте Oracle Database

- Убедитесь, что у вас установлен Oracle Database и вы можете к нему подключиться.
- Создайте пользователя и базу данных, если это еще не сделано.

### 2. Создание таблиц в Oracle Database

Вам необходимо создать три таблицы: для заявок, языков программирования и связи между ними. Вот SQL-запросы для создания таблиц:

```sql
-- Таблица заявок
CREATE TABLE applications (
    id NUMBER GENERATED BY DEFAULT AS IDENTITY PRIMARY KEY,
    name VARCHAR2(150) NOT NULL,
    phone VARCHAR2(15) NOT NULL,
    email VARCHAR2(100) NOT NULL,
    birthdate DATE NOT NULL,
    gender VARCHAR2(10) CHECK (gender IN ('male', 'female')),
    biography CLOB NOT NULL
);

-- Таблица языков программирования
CREATE TABLE programming_languages (
    id NUMBER GENERATED BY DEFAULT AS IDENTITY PRIMARY KEY,
    name VARCHAR2(50) NOT NULL
);

-- Таблица связи между заявками и языками программирования
CREATE TABLE application_languages (
    application_id NUMBER,
    language_id NUMBER,
    PRIMARY KEY (application_id, language_id),
    FOREIGN KEY (application_id) REFERENCES applications(id),
    FOREIGN KEY (language_id) REFERENCES programming_languages(id)
);
```

### 3. Подключение к базе данных Oracle в PHP

Убедитесь, что у вас установлен драйвер OCI8 для PHP. Затем создайте файл `config.php`, чтобы хранить параметры подключения:

```php
<?php
$host = 'localhost'; // или IP адрес вашей БД
$port = '1521'; // Порт по умолчанию
$sid = 'your_sid'; // SID вашей БД
$username = 'your_username'; // Имя пользователя
$password = 'your_password'; // Пароль

try {
    $conn = oci_connect($username, $password, "$host:$port/$sid");
    if (!$conn) {
        $e = oci_error();
        echo "Ошибка подключения: " . $e['message'];
    }
} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage();
}
?>
```

### 4. Создание обработчика формы (`form.php`)

Создайте файл `form.php` для обработки отправленных данных формы:

```php
<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Получаем данные из формы и проводим валидацию
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $birthdate = $_POST['birthdate'];
    $gender = $_POST['gender'];
    $biography = trim($_POST['biography']);
    $languages = $_POST['languages']; // Массив выбранных языков

    // Валидация данных (пример)
    if (strlen($name) > 150 || !preg_match("/^[а-яА-ЯёЁА-Za-z0-9\s]+$/u", $name)) {
        die("Неверное ФИО.");
    }
    // Дополнительные проверки на корректность других полей...

    // Запись данных в таблицу заявок
    $sql = "INSERT INTO applications (name, phone, email, birthdate, gender, biography)
            VALUES (:name, :phone, :email, TO_DATE(:birthdate, 'YYYY-MM-DD'), :gender, :biography)";
    $stmt = oci_parse($conn, $sql);
    
    oci_bind_by_name($stmt, ":name", $name);
    oci_bind_by_name($stmt, ":phone", $phone);
    oci_bind_by_name($stmt, ":email", $email);
    oci_bind_by_name($stmt, ":birthdate", $birthdate);
    oci_bind_by_name($stmt, ":gender", $gender);
    oci_bind_by_name($stmt, ":biography", $biography);
    
    if (oci_execute($stmt)) {
        // Получение последнего вставленного ID
        $application_id = oci_insert_id($stmt);
        
        // Запись выбранных языков
        foreach ($languages as $language_id) {
            $sql_lang = "INSERT INTO application_languages (application_id, language_id) VALUES (:application_id, :language_id)";
            $stmt_lang = oci_parse($conn, $sql_lang);
            oci_bind_by_name($stmt_lang, ":application_id", $application_id);
            oci_bind_by_name($stmt_lang, ":language_id", $language_id);
            oci_execute($stmt_lang);
        }
        
        echo "Данные успешно сохранены.";
    } else {
        echo "Ошибка при сохранении данных.";
    }
    
    oci_free_statement($stmt);
    oci_close($conn);
}
?>
```

### 5. Модификация HTML формы

Замените ваш `<select>` для языков программирования так, чтобы он отправлял данные в виде массива:

```html
<label for="field-languages">Любимый язык программирования:</label>
<select name="languages[]" id="field-languages" multiple required>
    <option value="1">Pascal</option>
    <option value="2">C</option>
    <option value="3">C++</option>
    <option value="4">JavaScript</option>
    <option value="5">PHP</option>
    <option value="6">Haskell</option>
    <option value="7">Clojure</option>
    <option value="8">Prolog</option>
    <option value="9">Scala</option>
</select>
```

### 6. Тестирование

Теперь вы можете протестировать вашу форму. Убедитесь, что все данные правильно передаются и сохраняются в базе данных. Если что-то не сработает, проверьте логи на наличие ошибок.

### Заключение

Это базовый пример, показывающий, как реализовать сохранение данных из формы в базу данных Oracle с помощью PHP. Не забывайте улучшать проверки данных и обрабатывать ошибки. Успехов! 🚀

