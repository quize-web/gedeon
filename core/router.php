<?php
/**
 * Created by PhpStorm.
 * User: ilya
 * Date: 27.07.17
 * Time: 23:05
 */

namespace core;

use controllers\panel\panel;


/**
 * Управление путями
 * */
class router
{


  /**
   * Путь до корневой папки на сервере
   *
   * @var $root string
   **/
  public static $root;

  /**
   * Адрес домена
   *
   * Пример: gedeon.io
   *
   * @var $domain string
   **/
  public static $domain;

  /**
   * Протокол передачи
   *
   * http или https
   *
   * @var $protocol string
   **/
  public static $protocol;

  /**
   * Домен верхнего уровня
   *
   * Пример: .ru .com .org .io
   *
   * @var $TLD string
   **/
  public static $TLD;

  /**
   * Корневой адрес ресурса
   *
   * Пример: https://gedeon.io/
   *
   * @var $url string
   **/
  public static $url;

  /**
   * Адрес запроса
   *
   * Пример: /pages/contacts/
   *
   * @var $urn string
   **/
  public static $urn;

  /**
   * Полный адрес ресурса
   *
   * Пример: https://gedeon.io/pages/contacts/)
   *
   * @var $uri string
   **/
  public static $uri;


  /**
   * Устанавливаем константы путей
   *
   * @return void
   **/
  public static function setPaths()
  {

    self::$root = $_SERVER['DOCUMENT_ROOT'] . '/';

    self::$domain = $_SERVER['HTTP_HOST'];

    self::$protocol = $_SERVER['REQUEST_SCHEME'];

    self::$url = self::$protocol . '://' . self::$domain . '/';

    self::$urn = $_SERVER['REQUEST_URI'];

    self::$uri = self::deleteSlashes(self::$url . self::$urn, 'double');

    self::$TLD = strstr(self::$domain, '.');

  }


  /**
   * Проверяет, находится ли приложение на локальном сервере
   *
   * @return boolean
   **/
  public static function isLocalServer()
  {

    ### если отсутствует домен верхнего уровня (TDL) - сервер локальный
    return (empty(self::$TLD));

  }


  /**
   * Строим массив из urn
   *
   * @return array
   **/
  public static function buildArrayPath()
  {

    $stringPath = self::deleteSlashes(self::$urn, 'first');

    return explode('/', $stringPath);

  }


  /**
   * Проверяем, находимся ли мы на главной странице
   *
   * @return boolean
   **/
  public static function isIndex()
  {

    return
      empty(self::buildArrayPath()[0]);

  }


  /**
   * Проверяем, находимся ли мы в панели управления
   *
   * @return boolean
   **/
  public static function isPanel()
  {

    return
      (self::buildArrayPath()[0] == 'panel');

  }


  /**
   * Подключаем, если нужно, необходимый контроллер
   *
   * @return void
   **/
  public static function connectToController()
  {

    if (self::isPanel()) panel::connect();

  }


  /**
   * Удаление слешей из различных положений строки ($position)
   *
   * Пример:
   * было - /pages/contacts/
   * стало - pages/contacts/ ### $position = 'first'
   *
   * было - /pages/contacts/
   * стало - /pages/contacts ### $position = 'last'
   *
   * было - /pages/contacts/
   * стало - pages/contacts ### $position = 'both'
   *
   * было - https://gedeon.com//pages//contacts/
   * стало - https://gedeon.com/pages/contacts/ ### $position = 'double'
   * такой случай ^ является издержкой красивых констант путей в приложении
   *
   * @param $string string строка, откуда удаляем
   * @param $position string откуда удаляем слеши
   * @return string
   **/
  public static function deleteSlashes($string, $position = 'double')
  {

    switch ($position) {

      case ('first'):
        return ltrim($string, '/');

      case ('last'):
        return rtrim($string, '/');

      case ('both'):
        return trim($string, '/');

      case ('double'):
        return preg_replace('@(?<!\:)\/\/@', '/', $string);

      default:
        return false;

    }

  }


  /**
   * Превращаем последнюю директорию в пути в файл
   *
   * Пример:
   * было - /pages/contacts/moscow/
   * было - pages/contacts/moscow
   * стало - /pages/contaxts/moscow.php
   *
   * @param $folderPath string строка пути
   * @param $extension string расширение файла
   * @return string
   **/
  public static function folderToFile($folderPath, $extension = 'php')
  {

    ### если строка заканчивается слешом
    if (substr($folderPath, -1) == '/') {

      return rtrim($folderPath, '/') . '.' . $extension;

      ### если строка не заканчивается слешом
    } else {

      ### ищем расширение, если находим, возвращаем строку неизмененной
      if (preg_match('@^.*\.' . $extension . '$@', $folderPath)) return $folderPath;
      ### если не находим, добавляем расширение и возвращаем измененную строку
      else return $folderPath . '.' . $extension;

    }

  }


}