<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Форма регистрации</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f7f7f7;
            overflow: auto; /* Позволяет прокручивать страницу, если форма больше экрана */
        }

        .popup {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            overflow: auto; /* Позволяет прокручивать попап, если форма больше его */
        }

        .popup.active {
            display: flex;
        }

        .form-container {
            background: #fff;
            padding: 20px; /* Уменьшили отступы, чтобы форма не выходила за пределы экрана */
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            overflow: auto; /* Позволяет прокручивать, если содержимое больше контейнера */
            max-height: 90vh; /* Максимальная высота формы, чтобы не выходила за экран */
        }

        /* Остальные стили без изменений */
        .form-container h2 {
            margin: 0 0 15px;
        }

        .form-row {
            margin-bottom: 20px;
        }

        .form-container input,
        .form-container textarea,
        .form-container select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .form-container button {
            width: 100%;
            padding: 10px;
            background: #007BFF;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 20px;
        }

        .form-container button:hover {
            background: #0056b3;
        }

        .gender-container {
            display: flex;
            margin-top: 5px;
        }

        .form-check {
            margin-right: 20px;
            display: flex;
            align-items: center;
        }

        .form-group {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }

        .form-group label {
            margin-left: 5px;
            margin-bottom: 0; /* Убрать отступ снизу */
        }

        /* Обновленный стиль для чекбокса */
        .form-check {
            display: flex;
            align-items: center;
        }

        .form-check-input {
            margin-right: 10px; /* Отступы перед чекбоксом */
        }
    </style>
</head>
<body>
    <button id="openFormButton">Открыть форму регистрации</button>
    <div class="popup" id="popup">
        <div class="form-container">
            <h2>Регистрация</h2>
            <form id="registrationForm">
                <div class="form-row">
                    <label for="field-name-1">ФИО:</label>
                    <input type="text" class="form-control" id="field-name-1" placeholder="Иванов Иван Иванович" required>
                </div>
                <div class="form-row">
                    <label for="field-tel">Номер телефона:</label>
                    <input type="tel" class="form-control" id="field-tel" placeholder="Введите ваш номер" required>
                </div>
                <div class="form-row">
                    <label for="field-email">Email:</label>
                    <input type="email" class="form-control" id="field-email" placeholder="Введите вашу почту" required>
                </div>
                <div class="form-row">
                    <label for="field-date">Дата рождения:</label>
                    <input type="date" class="form-control" id="field-date" required>
                </div>
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
                <div class="form-row">
                    <label for="field-name-4">Любимый язык программирования:</label>
                    <select class="form-control" id="field-name-4" multiple required>
                        <option value="Pascal">Pascal</option>
                        <option value="C">C</option>
                        <option value="C++">C++</option>
                        <option value="JavaScript">JavaScript</option>
                        <option value="PHP">PHP</option>
                        <option value="Haskell">Haskell</option>
                        <option value="Clojure">Clojure</option>
                        <option value="Prolog">Prolog</option>
                        <option value="Scala">Scala</option>
                    </select>
                </div>
                <div class="form-row">
                    <label for="field-name-2">Биография:</label>
                    <textarea class="form-control" id="field-name-2" rows="3" placeholder="Расскажите о себе" required></textarea>
                </div>
                <div class="form-row form-group">
                    <label class="form-check-label" for="check-1">С контрактом ознакомлен(а)</label>
                    <input type="checkbox" class="form-check-input" id="check-1" required>
                </div>
                <button type="submit">Сохранить</button>
            </form>
            <div class="message" id="message"></div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const openFormButton = document.getElementById('openFormButton');
            const popup = document.getElementById('popup');
            const registrationForm = document.getElementById('registrationForm');
            const messageDiv = document.getElementById('message');

            // Открыть попап
            openFormButton.addEventListener('click', () => {
                showPopup();
            });

            // Закрыть попап при нажатии Back
            window.addEventListener('popstate', () => {
                if (popup.classList.contains('active')) closePopup();
            });

            // Показать попап и обновить URL
            function showPopup() {
                popup.classList.add('active');
                history.pushState({ popup: true }, '', '#registration');
            }

            // Закрыть попап и обновить URL
            function closePopup() {
                popup.classList.remove('active');
                history.pushState(null, '', window.location.pathname);
            }

            // Отправка данных
            registrationForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                const formData = new FormData(registrationForm);
                try {
                    const response = await fetch('https://formcarry.com/s/pKrct8nT-YP', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                        },
                        body: formData,
                    });

                    if (!response.ok) {
                        const errorText = await response.text();
                        console.error('Ошибка:', response.status, errorText);
                        alert('Ошибка!');
                    } else {
                        const result = await response.json();
                        registrationForm.reset();
                        closePopup();
                        alert('Регистрация успешна!');
                    }
                } catch (error) {
                    alert('Ошибка!');
                }
            });
        });
    </script>
</body>
</html>
