# HTML-форма с использованием CSS

Вот пример HTML-кода для формы, а также базовый CSS для оформления. Вы можете использовать этот код как основу.

## HTML-код

```html
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Форма</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Регистрационная форма</h1>
        <form action="submit.php" method="POST">
            <label for="fio">ФИО:</label>
            <input type="text" id="fio" name="fio" required maxlength="150">

            <label for="phone">Телефон:</label>
            <input type="tel" id="phone" name="phone" required>

            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" required>

            <label for="dob">Дата рождения:</label>
            <input type="date" id="dob" name="dob" required>

            <label>Пол:</label>
            <input type="radio" id="male" name="gender" value="Мужской" required>
            <label for="male">Мужской</label>
            <input type="radio" id="female" name="gender" value="Женский">
            <label for="female">Женский</label>

            <label for="programming-languages">Любимый язык программирования:</label>
            <select id="programming-languages" name="languages[]" multiple required>
                <option value="Pascal">Pascal</option>
                <option value="C">C</option>
                <option value="C++">C++</option>
                <option value="JavaScript">JavaScript</option>
                <option value="PHP">PHP</option>
                <option value="Python">Python</option>
                <option value="Java">Java</option>
                <option value="Haskell">Haskell</option>
                <option value="Clojure">Clojure</option>
                <option value="Prolog">Prolog</option>
                <option value="Scala">Scala</option>
                <option value="Go">Go</option>
            </select>

            <label for="bio">Биография:</label>
            <textarea id="bio" name="bio" rows="4" required></textarea>

            <label>
                <input type="checkbox" name="agreement" required>
                С контрактом ознакомлен(а)
            </label>

            <button type="submit">Сохранить</button>
        </form>
    </div>
</body>
</html>
```

## CSS-код (style.css)

```css
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    color: #333;
    margin: 0;
    padding: 0;
}

.container {
    width: 50%;
    margin: 0 auto;
    padding: 20px;
    background: #fff;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    border-radius: 5px;
}

h1 {
    text-align: center;
    color: #4CAF50;
}

label {
    display: block;
    margin: 10px 0 5px;
}

input, select, textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

button {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
}

button:hover {
    background-color: #45a049;
}
```

## Пошаговая инструкция по созданию базы данных и серверной части

1. **Установка MariaDB**  
   Убедитесь, что у вас установлен MariaDB. Если нет, установите его с помощью вашего пакетного менеджера (например, `apt` для Ubuntu).

2. **Создание базы данных**  
   Войдите в MariaDB и создайте базу данных и таблицы. Откройте терминал и выполните:
   ```sql
   CREATE DATABASE registration_db;
   USE registration_db;

   CREATE TABLE users (
       id INT AUTO_INCREMENT PRIMARY KEY,
       fio VARCHAR(150) NOT NULL,
       phone VARCHAR(20),
       email VARCHAR(100),
       dob DATE,
       gender ENUM('Мужской', 'Женский') NOT NULL,
       bio TEXT,
       agreement TINYINT(1),
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );

   CREATE TABLE programming_languages (
       id INT AUTO_INCREMENT PRIMARY KEY,
       user_id INT,
   language VARCHAR(50),
       FOREIGN KEY (user_id) REFERENCES users(id)
   );
   ```

3. **Создание PHP-скрипта для обработки формы**  
   Создайте файл `submit.php` для обработки данных формы и записи в базу данных.
   ```php
   <?php
   $conn = new mysqli('localhost', 'username', 'password', 'registration_db');

   if ($conn->connect_error) {
       die("Connection failed: " . $conn->connect_error);
   }

   $fio = $_POST['fio'];
   $phone = $_POST['phone'];
   $email = $_POST['email'];
   $dob = $_POST['dob'];
   $gender = $_POST['gender'];
   $bio = $_POST['bio'];
   $agreement = isset($_POST['agreement']) ? 1 : 0;
   $languages = $_POST['languages'];

   // Валидация данных
   if (!preg_match("/^[а-яА-ЯёЁ\s]+$/u", $fio) || strlen($fio) > 150) {
       die("Ошибка: ФИО некорректно.");
   }

   $stmt = $conn->prepare("INSERT INTO users (fio, phone, email, dob, gender, bio, agreement) VALUES (?, ?, ?, ?, ?, ?, ?)");
   $stmt->bind_param("ssssssi", $fio, $phone, $email, $dob, $gender, $bio, $agreement);
   $stmt->execute();

   $user_id = $stmt->insert_id;

   if ($user_id) {
       $stmt = $conn->prepare("INSERT INTO programming_languages (user_id, language) VALUES (?, ?)");
       foreach ($languages as $language) {
           $stmt->bind_param("is", $user_id, $language);
           $stmt->execute();
       }
       echo "Данные успешно сохранены.";
   }

   $stmt->close();
   $conn->close();
   ?>
   ```

4. **Настройка параметров подключения**  
   Замените `username` и `password` в `submit.php` на ваши учетные данные для подключения к базе данных.

5. **Запуск веб-сервера**  
   Убедитесь, что ваш веб-сервер (например, Apache или Nginx) запущен, и скрипты PHP могут быть обработаны.

6. **Тестирование**  
   Откройте в браузере страницу с формой, заполните ее и убедитесь, что данные сохраняются в вашей базе данных. 

Важно обеспечить надежную валидацию и обработку ошибок для безопасной работы приложения.
