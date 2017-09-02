<?php
/**
 * Created by PhpStorm.
 * User: ilya
 * Date: 27.07.17
 * Time: 23:12
 */

namespace core;

/* * *
 * Главный настройщик приложения
 * * */
class tuner
{


  /**
   * Режим обработки ошибок
   *
   * 0 - выключить отображение всех ошибок
   * 1 - включить отображение всех ошибок
   *
   * @param $errorHandlingMode integer
   * @uses core
   * @return void
   **/
  public static function setErrorHandlingMode(int $errorHandlingMode = 1)
  {

    core::$errorHandlingMode = $errorHandlingMode;

  }


  /**
   * Установить кодировку
   *
   * @param $charset string название кодировки
   * @uses core
   * @return void
   **/
  public static function setCharset(string $charset = 'utf-8')
  {

    core::$charset = $charset;

  }


  /**
   * Замерять ли скорость загрузки страницы
   *
   * @param $status boolean включен замер или выключен
   * @uses core
   * @return  void
   **/
  public static function revealPageLoadTime(bool $status = true)
  {

    core::$revealPageLoadTime = true;

  }


  /**
   * Установить путь / адрес панели
   *
   * @param $panelName string название панели
   * @uses router
   * @return void
   **/
  public static function setPanelName(string $panelName)
  {

    router::$panelName = $panelName;

  }


  /**
   *  Авторизационные данные для подключение к базе данных
   *
   * @param $server string сервер, на котором находится база данных
   * @param $user string пользователь, у которого есть права на управления базой данных
   * @param $password string пароль для входа пользователя
   * @param $database string название необходимой нам базы данных
   * @uses database
   * @return void
   **/
  public static function databaseLoginData(
    $database = 'gedeon',
    $server = 'localhost',
    $user = 'root',
    $password = 'root'
  )
  {

    database::$server = $server;
    database::$user = $user;
    database::$password = $password;
    database::$database = $database;

    database::$charset = core::$charset;

  }


  /**
   * Задаем особую кодировку для базы данных
   *
   * @param $charset string
   * @uses database
   * @return void
   **/
  public static function setDatabaseCharset(string $charset)
  {

    database::$charset = $charset;

  }


}