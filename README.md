Here are the SQL commands to create the database tables:

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
This solution provides proper validation, secure database insertion using prepared statements, and a normalized database structure.
