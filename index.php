<?php

/**
 Для подключения автозагрузчика композера нужно:
 1 Создать файл composer.json в корне проекта.
 2 Добавить в composer.json код для построения логики автозагрузок файлов. См код в composer.json и запустить команду "composer dump-autoload"
 3 На всех страницах подключать автозагрузчик файлов через такую конструкцию require_once 'vendor/autoload.php';
 */


use app\User; // Вынос неймспейса User в отдельное место в секцию use

require_once 'vendor/autoload.php'; // Это подключили автозагрузчик composer

$u = new User(); // Короткий неймспейс, т.к. вынесли в use
$u->say();

$t = new \app\components\Test(); // Длинный неймспейс, т.к. не вынесли в use
$t->test();
