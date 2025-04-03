Создание базы данных для формы регистрации
Я помогу вам создать базу данных MySQL (MariaDB) для вашей формы регистрации. Вот пошаговая инструкция:

1. Создание структуры базы данных
Вам нужно создать 3 таблицы в соответствии с требованиями 3-й нормальной формы:

applications - основная таблица с данными заявок

programming_languages - справочник языков программирования

application_languages - таблица связи между заявками и языками (один ко многим)

SQL-запросы для создания таблиц
sql
Copy
-- Создаем таблицу для заявок
CREATE TABLE applications (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    full_name VARCHAR(150) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    birth_date DATE NOT NULL,
    gender ENUM('male', 'female') NOT NULL,
    biography TEXT NOT NULL,
    contract_agreed BOOLEAN NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

-- Создаем таблицу для языков программирования
CREATE TABLE programming_languages (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    PRIMARY KEY (id)
);

-- Создаем таблицу связи между заявками и языками
CREATE TABLE application_languages (
    application_id INT UNSIGNED NOT NULL,
    language_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (application_id, language_id),
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
    FOREIGN KEY (language_id) REFERENCES programming_languages(id)
);
Заполнение справочника языков программирования
sql
Copy
INSERT INTO programming_languages (name) VALUES 
('Pascal'), ('C'), ('C++'), ('JavaScript'), ('PHP'), 
('Python'), ('Java'), ('Haskell'), ('Clojure'), 
('Prolog'), ('Scala'), ('Go');
2. Модификация PHP-скрипта для обработки формы
Вам нужно создать файл form.php для обработки данных формы:

php
Copy
<?php
header('Content-Type: application/json');

// Параметры подключения к БД
$host = 'localhost';
$dbname = 'ваш_логин'; // замените на ваш логин
$user = 'ваш_логин';   // замените на ваш логин
$pass = 'ваш_пароль';   // замените на ваш пароль

try {
    // Подключение к БД
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Валидация данных
    $errors = [];
    
    // ФИО: только буквы и пробелы, до 150 символов
    $full_name = trim($_POST['full_name'] ?? '');
    if (!preg_match('/^[a-zA-Zа-яА-ЯёЁ\s]+$/u', $full_name)) {
        $errors['full_name'] = 'ФИО должно содержать только буквы и пробелы';
    }
    if (mb_strlen($full_name) > 150) {
        $errors['full_name'] = 'ФИО должно быть не длиннее 150 символов';
    }
    
    // Телефон: проверка формата
    $phone = trim($_POST['phone'] ?? '');
    if (!preg_match('/^\+?[\d\s\-\(\)]{10,20}$/', $phone)) {
        $errors['phone'] = 'Неверный формат телефона';
    }
    
    // Email: проверка формата
    $email = trim($_POST['email'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Неверный формат email';
    }
    
    // Дата рождения: проверка что дата в прошлом
    $birth_date = $_POST['birth_date'] ?? '';
    if (!strtotime($birth_date) || strtotime($birth_date) > time()) {
        $errors['birth_date'] = 'Неверная дата рождения';
    }
    
    // Пол: только допустимые значения
    $gender = $_POST['gender'] ?? '';
    if (!in_array($gender, ['male', 'female'])) {
        $errors['gender'] = 'Неверно указан пол';
    }
    
    // Языки программирования: хотя бы один выбран
    $languages = $_POST['languages'] ?? [];
    if (empty($languages)) {
        $errors['languages'] = 'Выберите хотя бы один язык программирования';
    } else {
        // Проверка что все выбранные языки существуют в БД
        $stmt = $pdo->prepare("SELECT id FROM programming_languages WHERE name = ?");
        foreach ($languages as $lang) {
            $stmt->execute([$lang]);
            if (!$stmt->fetch()) {
                $errors['languages'] = 'Выбран недопустимый язык программирования';
                break;
            }
        }
    }
    
    // Биография: не пустая
    $biography = trim($_POST['biography'] ?? '');
    if (empty($biography)) {
        $errors['biography'] = 'Заполните биографию';
    }
    
    // Согласие с контрактом
    $contract_agreed = isset($_POST['contract_agreed']) && $_POST['contract_agreed'] === 'on';
    if (!$contract_agreed) {
        $errors['contract_agreed'] = 'Необходимо согласие с контрактом';
    }
    
    // Если есть ошибки - возвращаем их
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit;
    }
    
    // Начинаем транзакцию
    $pdo->beginTransaction();
    
    try {
        // Вставляем основную информацию о заявке
        $stmt = $pdo->prepare("
            INSERT INTO applications 
            (full_name, phone, email, birth_date, gender, biography, contract_agreed)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $full_name, 
            $phone, 
            $email, 
            $birth_date, 
            $gender, 
            $biography, 
            $contract_agreed
        ]);
        
        // Получаем ID вставленной заявки
        $application_id = $pdo->lastInsertId();
        
        // Вставляем выбранные языки программирования
        $stmt = $pdo->prepare("
            INSERT INTO application_languages (application_id, language_id)
            SELECT ?, id FROM programming_languages WHERE name = ?
        ");
        
        foreach ($languages as $lang) {
            $stmt->execute([$application_id, $lang]);
        }
        
        // Фиксируем транзакцию
        $pdo->commit();
        
        // Возвращаем успешный ответ
        echo json_encode(['success' => true, 'message' => 'Данные успешно сохранены']);
        
    } catch (Exception $e) {
        // Откатываем транзакцию при ошибке
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Ошибка при сохранении данных']);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ошибка подключения к базе данных']);
}
3. Модификация HTML-формы
Вам нужно обновить вашу HTML-форму, чтобы она отправляла данные на ваш серверный скрипт:

html
Copy
<!-- Измените форму в вашем HTML-файле: -->
<form id="registrationForm" method="POST" action="form.php">
    <!-- Поле ФИО -->
    <div class="form-row">
        <label for="full_name">ФИО:</label>
        <input type="text" name="full_name" id="full_name" placeholder="Иванов Иван Иванович" required>
    </div>
    
    <!-- Поле Телефон -->
    <div class="form-row">
        <label for="phone">Номер телефона:</label>
        <input type="tel" name="phone" id="phone" placeholder="Введите ваш номер" required>
    </div>
    
    <!-- Поле Email -->
    <div class="form-row">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" placeholder="Введите вашу почту" required>
    </div>
    
    <!-- Поле Дата рождения -->
    <div class="form-row">
        <label for="birth_date">Дата рождения:</label>
        <input type="date" name="birth_date" id="birth_date" required>
    </div>
    
    <!-- Поле Пол -->
    <div class="form-row">
        <label>Пол:</label>
        <div class="gender-container">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="gender" id="radio-male" value="male" required>
                <label class="form-check-label" for="radio-male">Мужской</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="gender" id="radio-female" value="female" required>
                <label class="form-check-label" for="radio-female">Женский</label>
            </div>
        </div>
    </div>
    
    <!-- Поле Языки программирования -->
    <div class="form-row">
        <label for="languages">Любимый язык программирования:</label>
        <select name="languages[]" id="languages" multiple required>
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
    </div>
    
    <!-- Поле Биография -->
    <div class="form-row">
        <label for="biography">Биография:</label>
        <textarea name="biography" id="biography" rows="3" placeholder="Расскажите о себе" required></textarea>
    </div>
    
    <!-- Чекбокс согласия -->
    <div class="form-row form-group">
        <input type="checkbox" name="contract_agreed" id="contract_agreed" required>
        <label for="contract_agreed">С контрактом ознакомлен(а)</label>
    </div>
    
    <button type="submit">Сохранить</button>
</form>
Run HTML
4. Обновление JavaScript для обработки отправки формы
Замените ваш JavaScript-код для отправки формы на этот:

javascript
Copy
registrationForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(registrationForm);
    
    try {
        const response = await fetch('form.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            registrationForm.reset();
            closePopup();
            alert('Данные успешно сохранены!');
        } else {
            // Показываем ошибки валидации
            if (result.errors) {
                let errorMessages = '';
                for (const [field, message] of Object.entries(result.errors)) {
                    errorMessages += `${message}\n`;
                }
                alert(`Ошибки заполнения формы:\n${errorMessages}`);
            } else {
                alert(result.message || 'Произошла ошибка');
            }
        }
    } catch (error) {
        alert('Ошибка сети или сервера');
    }
});
5. Пошаговый план действий
Подключитесь к MySQL через SSH:

Copy
mysql -u<ваш_логин> -p<ваш_пароль>
Выберите вашу базу данных:

sql
Copy
USE <ваш_логин>;
Создайте таблицы, выполнив SQL-запросы из раздела 1.

Заполните таблицу языков программирования, выполнив INSERT-запрос.

Создайте файл form.php с содержимым из раздела 2.

Обновите HTML-форму в соответствии с разделом 3.

Обновите JavaScript в соответствии с разделом 4.

Проверьте работу формы:

Откройте форму в браузере

Заполните все поля

Отправьте форму

Убедитесь, что данные сохраняются в БД

Проверьте обработку ошибок валидации

6. Проверка данных в БД
После отправки формы вы можете проверить, что данные сохранились:

sql
Copy
-- Посмотреть все заявки
SELECT * FROM applications;

-- Посмотреть языки программирования для конкретной заявки
SELECT pl.name 
FROM application_languages al
JOIN programming_languages pl ON al.language_id = pl.id
WHERE al.application_id = 1;
Это полное решение, соответствующее всем требованиям задания:

Используется 3 таблицы в 3-й нормальной форме

Реализована валидация всех полей на сервере

Данные сохраняются с помощью подготовленных запросов

Языки программирования хранятся в отдельной таблице

Есть обработка ошибок и успешного сохранения
