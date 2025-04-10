
First, let's fix the PHP validation and database handling:

php
Copy
<?php
header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!empty($_GET['save'])) {
        print('Спасибо, результаты сохранены!');
    }
    include('index.php');
    exit();
}

// Validate inputs
$errors = FALSE;
if (empty($_POST['fio']) || !preg_match('/^[а-яА-ЯёЁa-zA-Z\s]+$/u', $_POST['fio']) || strlen($_POST['fio']) > 150) {
    print('Пожалуйста, введите корректное ФИО (только буквы и пробелы, не более 150 символов).<br/>');
    $errors = TRUE;
}

if (empty($_POST['tel']) || !preg_match('/^[\d\s\-\+\(\)]+$/', $_POST['tel'])) {
    print('Пожалуйста, введите корректный номер телефона.<br/>');
    $errors = TRUE;
}

if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    print('Пожалуйста, введите корректный email.<br/>');
    $errors = TRUE;
}

if (empty($_POST['date']) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['date'])) {
    print('Пожалуйста, выберите корректную дату рождения.<br/>');
    $errors = TRUE;
}

if (empty($_POST['gender']) || !in_array($_POST['gender'], ['male', 'female'])) {
    print('Пожалуйста, выберите пол.<br/>');
    $errors = TRUE;
}

if (empty($_POST['plang']) || !is_array($_POST['plang'])) {
    print('Пожалуйста, выберите хотя бы один язык программирования.<br/>');
    $errors = TRUE;
}

if (empty($_POST['bio']) || strlen($_POST['bio']) > 500) {
    print('Пожалуйста, напишите биографию (не более 500 символов).<br/>');
    $errors = TRUE;
}

if (empty($_POST['check'])) {
    print('Пожалуйста, подтвердите ознакомление с контрактом.<br/>');
    $errors = TRUE;
}

if ($errors) {
    exit();
}

// Database connection
$user = 'u70422'; // replace with your username
$pass = '4545635'; // replace with your password
$dbname = 'u70422'; // replace with your database name

try {
    $db = new PDO("mysql:host=localhost;dbname=$dbname", $user, $pass, [
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    // Insert main application data
    $stmt = $db->prepare("INSERT INTO applications (fio, tel, email, birth_date, gender, bio) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['fio'],
        $_POST['tel'],
        $_POST['email'],
        $_POST['date'],
        $_POST['gender'],
        $_POST['bio']
    ]);
    
    $applicationId = $db->lastInsertId();
    
    // Insert programming languages
    $stmt = $db->prepare("INSERT INTO application_languages (application_id, language_id) VALUES (?, ?)");
    foreach ($_POST['plang'] as $language) {
        // First, get or insert language to get its ID
        $langStmt = $db->prepare("SELECT id FROM programming_languages WHERE name = ?");
        $langStmt->execute([$language]);
        $langId = $langStmt->fetchColumn();
        
        if (!$langId) {
            $langStmt = $db->prepare("INSERT INTO programming_languages (name) VALUES (?)");
            $langStmt->execute([$language]);
            $langId = $db->lastInsertId();
        }
        
        $stmt->execute([$applicationId, $langId]);
    }
    
    header('Location: ?save=1');
} catch (PDOException $e) {
    print('Error: ' . $e->getMessage());
    exit();
}
2. Here are the SQL commands to create the database tables:

sql
Copy
-- Create applications table
CREATE TABLE applications (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    fio VARCHAR(150) NOT NULL,
    tel VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    birth_date DATE NOT NULL,
    gender ENUM('male', 'female') NOT NULL,
    bio TEXT NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create programming_languages table
CREATE TABLE programming_languages (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create application_languages junction table
CREATE TABLE application_languages (
    application_id INT UNSIGNED NOT NULL,
    language_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (application_id, language_id),
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
    FOREIGN KEY (language_id) REFERENCES programming_languages(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert initial programming languages
INSERT INTO programming_languages (name) VALUES 
('Pascal'), ('C'), ('C++'), ('JavaScript'), ('PHP'), 
('Python'), ('Java'), ('Haskell'), ('Clojure'), 
('Prolog'), ('Scala'), ('Go');
3. Fixes to your HTML form:

Change the select element to use name="plang[]" for multiple selection:
html
Copy
<select class="form-control" name="plang[]" id="field-name-4" multiple required>
Run HTML
Add Python, Java, and Go to your programming languages options:
html
Copy
<option value="Python">Python</option>
<option value="Java">Java</option>
<option value="Go">Go</option>
Run HTML
4. Important notes:

Replace the database credentials (u70422, 4545635, u70422) with your actual credentials.
The form uses Russian language, but the database fields use English names for consistency.
The validation now properly checks:
FIO: only letters and spaces, max 150 chars
Phone: digits, spaces, and common phone symbols
Email: proper email format
Date: proper date format
Gender: only allowed values
Bio: not empty and max 500 chars
Checkbox: must be checked
The database structure follows 3NF with:
applications table for main data
programming_languages table for language names
application_languages junction table for the many-to-many relationship
To implement this:

Run the SQL commands to create the tables
Save the PHP code as form.php
Make sure your HTML form submits to this PHP file
Test the form submission
This solution provides proper validation, secure database insertion using prepared statem
