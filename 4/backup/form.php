<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <title>Форма</title>
</head>
<body>
<!-- Шапка -->
<div class="row justify-content-md-between">
    <div class="main_header d-flex align-items-center">
        <img id="kitty_id" src="kitty.png" alt="Логотип сайта с котиком" class="mr-4">
        <h1 class="header_name">Форма</h1>
    </div>
</div>
<!-- Форма -->
<?php
if (!empty($messages)) {
  print('<div class="error-messages">');
  foreach ($messages as $message) {
    print($message);
  }
  print('</div>');
}
if(isset($_GET['actionsCompleted']) && $_GET['actionsCompleted'] == '1') {
    echo "<div class='error-messages'>Форма успешно отправлена!</div>";
}
?>

<div class="form">
    <h2>Форма</h2>
    <form action="index.php" method="POST">
        <ol>
            <li>
                <label>
                    ФИО:<br /> <input name="fio" <?php if (isset($errors['fio'])) {print 'class="error" value="' . $values['fio'] . '"';}?>
                    type="text" placeholder="Иванов Иван Иванович" />
                </label><br />
            </li>

            <li>
                <label>
                    Телефон:<br /><input name="phone" type="tel" placeholder="Введите номер телефона" />
                </label><br />
            </li>

            <li>
                <label>
                    E-mail:<br /><input name="email" type="email" placeholder="Введите вашу почту" />
                </label><br />
            </li>

            <li>
                <label>
                    Дата рождения:<br /><input name="birthdate" type="date" required/>
                </label><br />
            </li>

            <li>
                Пол: <br />
                <label class="gender"><input type="radio" name="gender" value="female" />Женский</label>
                <label class="gender"><input type="radio" name="gender" value="male" />Мужской</label><br />
            </li>

            <li>
                <label>
                    Любимый язык программирования: <br />
                    <select name="language[]" multiple="multiple">
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
                    </select>
                </label><br />
            </li>

            <li>
                <label>
                    Биография: <br /><textarea name="bio" placeholder="Напишите о себе"></textarea>
                </label> <br />
            </li>

            <li>
                Соглашение: <br />
                <label class="sogl"><input type="checkbox" name="check" required/> C контрактом ознакомлен(а)</label><br />
            </li>

            <li><input type="submit" value="Сохранить" /></li>
        </ol>
    </form>
</div>
</body>
</html>
