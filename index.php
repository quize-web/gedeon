<?php
/**
 * Created by PhpStorm.
 * User: ilya
 * Date: 27.07.17
 * Time: 22:40
 */


/**
 *
 * Gedeon CMS
 *
 * @version 0.1
 *
 **/


/* *
 * Подключение ядра
 * */

### управление путями
require_once('core/router.php');

use core\router;

### настройщик
require_once('core/tuner.php');

use core\tuner;

### методы для работы с базой данных
require_once('core/database.php');

use core\database;

### ядро для запуска системы
require_once('core/core.php');

use core\core;


/* * *
 * Настройки (используется специальный настройщик tuner)
 * * */

### режим обработки ошибок
tuner::setErrorHandlingMode(1);

### кодировка приложения
tuner::setCharset('utf-8');
# по умолчанию базе данных задается та же кодировка,
# но ее можно изменить методом tuner::setDatabaseCharset

### авторизационные данные для подключения к базе данных
tuner::databaseLoginData();
### настройки по умолчанию - для локального сервера

### задаем путь / адрес к панели (по умолчанию - '/panel/')
tuner::setPanelName('/panel/');


### запуск ядра ###
core::run();
###################