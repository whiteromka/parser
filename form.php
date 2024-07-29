<?php

/**
* Для подключения автозагрузчика композера нужно:
 * 1 Создать файл composer.json в корне проекта.
 * 2 Добавить в composer.json код для построения логики автозагрузок файлов. См код в composer.json и запустить команду "composer dump-autoload"
 * 3 На всех страницах подключать автозагрузчик файлов через такую конструкцию require_once 'vendor/autoload.php';
*/

require_once 'vendor/autoload.php'; // Это подключили автозагрузчик composer

function debug($data): void
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

// debug($_POST);
// debug($_FILES);
// debug($_SERVER);
// $_SERVER - еще один суперглобальный массив с информацией от сервера

$loadErrors = []; // Ошибки загрузки
if (!empty($_POST["submit"])) {
    // Проверяем, был ли файл успешно загружен
    if (isset($_FILES["csvFile"]) && $_FILES["csvFile"]["error"] == 0) {
        $fileName = $_FILES["csvFile"]["name"];
        //$fileType = $_FILES["csvFile"]["type"];
        //$fileSize = $_FILES["csvFile"]["size"];

        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION)); // Расширение файла
        if ($fileExtension !== "csv") {
            $loadErrors[] = "Пожалуйста, загрузите файл с расширением CSV.";
        } else {
            // Перемещаем загруженный файл в нужную директорию
            $tempFileLocation = $_FILES["csvFile"]["tmp_name"];
            $newFileLocation = "uploads/" . basename($fileName);
            if (move_uploaded_file($tempFileLocation, $newFileLocation)) {
                // файл сохранен в uploads

                $csvData = array_map('str_getcsv', file($newFileLocation)); // $csvData содержит данные из CSV csv файла
                // debug($csvData);
            } else {
                $loadErrors[] = "Ошибка при перемещении файла в uploads/";
            }
        }
    } else {
        $loadErrors[] = "Файл не был загружен";
    }
}

/*
 Переписать на ООП манер
- Класс должен уметь сохранять ошибки если такие будут в ходе работы, выдавать ошибки по запросу,
  выдавать распарсенные данные по запросу.
*/

$parser = new Parser();
if (
    $parser->load() &&            // load() отвечает за загрузку файлов (Loader)
    empty($parser->getErrors())   // getErrors() отвечает за получение ошибок возникших в процессе загрузки файла
) {
    $csvData = $parser->getCsvData(); // распарсенные данные
} else {
    $loadErrors = $parser->getErrors();
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <!-- HTML Формы  -->
    <!-- !!! Внимание атрибут enctype="multipart/form-data" ОБЯЗАТЕЛЕН для загрузки файлов  -->
    <form action="" method="post" enctype="multipart/form-data">
        <p>Выберите CSV файл для загрузки:</p>
        <input type="file" name="csvFile" id="csvFile">
        <input type="submit" value="Загрузить CSV" name="submit">
    </form>

    <div>
        <?php foreach ($loadErrors as $error) {
            echo "<p>$error</p>";
        } ?>
    </div>

</head>
<body>

</body>
</html>
