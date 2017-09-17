<?php
/**
 * Created by PhpStorm.
 * User: ilya
 * Date: 27.07.17
 * Time: 22:40
 */


/* * *
 *
 * Gedeon CMS
 *
 * @version 0.1
 *
 * * */


/* *
 * Подключение ядра
 * */

### подключаем и инициализируем ядро
require_once('core/core.php');

use core\core;

### подключаем необходимые для запуска ядра скрипты (в папке core)
core::buildCore();


/* * *
 * Настройки (используется специальный глобальный настройщик tuner)
 * * */

require_once('modules/tuner.php');

use modules\tuner;

### кодировка приложения
tuner::setCharset('utf-8');
# по умолчанию базе данных задается та же кодировка,
# но ее можно изменить методом tuner::setDatabaseCharset

### режим обработки ошибок
tuner::setErrorHandlingMode(1);

### авторизационные данные для подключения к базе данных
tuner::databaseLoginData();
### настройки по умолчанию - для локального сервера

### отображать ли скорость загрузки приложения
tuner::showApplicationTimeOutlay(true);

### задаем название пути к панели управления (по умолчанию - 'panel')
tuner::setPanelKey('panel');


### запуск ядра ###
core::run();
###################