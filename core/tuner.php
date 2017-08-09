<?php
/**
 * Created by PhpStorm.
 * User: ilya
 * Date: 27.07.17
 * Time: 23:12
 */

namespace core;

/**
 * Главный настройщик приложения
 * */
class tuner
{

  public static $charset;

  /**
   * Регистрация загрузчика классов
   *
   * @return void
   **/
  public static function bootClasses()
  {

    spl_autoload_register(function ($class_name) {

      ### экранируем обратные слеши в пути класса
      ### пример: было - core\database, стало - core/database
      $class_path = str_replace('\\', '/', $class_name);

      ### полный путь к файлу класса на сервере
      $file_path = router::$root . $class_path . '.php';

      ###  инклюдим найденный класс
      include_once $file_path;

    });

  }


  /**
   * Правило отображения ошибок
   *
   * 1 - включить отображение; 2 - выключить отображение.
   *
   * @param $status integer
   * @return void
   **/
  public static function error_mode($status = 1)
  {

    ### ошибки выполнения
    ini_set('display_errors', $status);

    ### ошибки при запуске php
    ini_set('display_startup_errors', $status);

    ### уровень отображения ошибок
    error_reporting(($status) ? E_ALL : 0);

  }


  /**
   *  Авторизационные данные для подключение к базе данных
   *
   * @param $server string сервер, на котором находится база данных
   * @param $user string пользователь, у которого есть права на управления базой данных
   * @param $password string пароль для входа пользователя
   * @param $database string название необходимой нам базы данных
   * @return void
   **/
  public static function databaseLoginData(
    $server = 'localhost',
    $user = 'root',
    $password = 'root',
    $database = 'gedeon')
  {

    database::$server = $server;
    database::$user = $user;
    database::$password = $password;
    database::$database = $database;

    database::$charset = self::$charset;

  }


  /**
   * Задаем особую кодировку для базы данных
   *
   * @param $charset string
   * @return void
   **/
  public static function databaseCharset($charset)
  {

    database::$charset = $charset;

  }


}