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
$errorsExist = FALSE;
if (!empty($messages)) {
  $errorsExist = TRUE;
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
                    ФИО:<br /> <input name="fio" <?php if (isset($errors['fio']) && $errors['fio'] != '') {print 'class="error"';} ?> value="<?php if(isset($values['fio'])) {print $values['fio'];} ?>"
                    type="text" placeholder="Иванов Иван Иванович" />
                </label><br />
            </li>

            <li>
                <label>
                    Телефон:<br /><input name="phone" <?php if (isset($errors['phone']) && $errors['phone'] != '') {print 'class="error"';} ?> value="<?php if(isset($values['phone'])) {print $values['phone'];} ?>"
                    type="tel" placeholder="Введите номер телефона" />
                </label><br />
            </li>

            <li>
                <label>
                    E-mail:<br /><input name="email" <?php if (isset($errors['email']) && $errors['email'] != '') {print 'class="error"';} ?> value="<?php if(isset($values['email'])) {print $values['email'];} ?>"
                    type="email" placeholder="Введите вашу почту" />
                </label><br />
            </li>

            <li>
                <label>
                    Дата рождения:<br /><input name="birthdate" <?php if (isset($errors['birthdate']) && $errors['birthdate'] != '') {print 'class="error"';} ?> value="<?php print $values['birthdate']; ?>"
                    type="date"/ required>
                </label><br />
            </li>

            <li>
                <label>
                <input name="gender" type="radio" value="female" <?php if (isset($errors['gender']) && $errors['gender'] != '') {print 'class="error"';} ?>
                <?php if (isset($values['gender']) && $values['gender'] == 'female') {echo 'checked';}?>> Женский </label>

                <label> <input name="gender" type="radio" value="male" <?php if (isset($errors['gender']) && $errors['gender'] != '') {print 'class="error"';} ?>
                <?php if (isset($values['gender']) && $values['gender'] == 'male') {echo 'checked';} ?>> Мужской</label><br />

            </li>

            <li>
                <label>
                    Любимый язык программирования: <br />
                    <select name="language[]" multiple="multiple" <?php if ($errors['language_n'] == '1' || $errors['language_d'] == '1') {print 'class="error"';} ?>>
                    <?php
                    foreach ($valid_languages as $language) {
                      $selected = in_array($language, $values['languages']) ? 'selected' : '';
                      printf('<option value="%s" %s>%s</option>', $language, $selected, $language);
                    } ?>
                    </select>
                </label><br />
            </li>

            <li>
                <label>
                    Биография: <br /><textarea name="bio" <?php if (isset($errors['bio']) && $errors['bio'] != '') {print 'class="error"';} ?>
                    placeholder="Напишите о себе"> <?php if(isset($values['bio'])) {print $values['bio'];} ?> </textarea>
                </label> <br />
            </li>

            <li>
                Соглашение: <br />
                <label <?php if (isset($errors['check']) && $errors['check'] != '') {print 'class="error"';} ?> ><input type="checkbox" name="check" <?php if (isset($values['check']) && $values['check'] == 1) {echo 'checked';} ?>
                /> C контрактом ознакомлен(а)</label><br /> <!-- $errorsExist && $errors['check'] == '' -->
            </li>

            <li><input type="submit" value="Сохранить" /></li>
        </ol>
    </form>
</div>
</body>
</html>
