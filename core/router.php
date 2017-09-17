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
   * Версия протокола передачи
   *
   * Пример: 'HTTP/1.1'
   *
   * @var $protocolVersion string
   **/
  public static $protocolVersion;

  /**
   * Тип протокола передачи
   *
   * http или https
   *
   * @var $protocolType string
   **/
  public static $protocolType;

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
   * @var $URL string
   **/
  public static $URL;

  /**
   * Адрес запроса
   *
   * Пример: /pages/contacts/
   *
   * @var $URN string
   **/
  public static $URN;

  /**
   * Полный адрес ресурса
   *
   * Пример: https://gedeon.io/pages/contacts/
   *
   * @var $URI string
   **/
  public static $URI;

  /**
   * Путь к панели (можно задать tuner'ом)
   *
   * Пример: /panel/
   * Путь к панели также можно задать tuner'ом
   *
   * @var $panelKey string
   **/
  public static $panelKey = '/panel/';

  /**
   * Корневой адрес панели
   *
   * Пример: https://gedeon.io/panel/
   *
   * @var $panelURL string
   **/
  public static $panelURL;

  /**
   * Адрес запроса панели
   *
   * Пример: /director/add/
   *
   * @var $panelURN string
   **/
  public static $panelURN;

  /**
   * Адрес родительского каталога
   *
   * Пример:
   * текущий адрес - https://gedeon.io/panel/director/add/
   * адрес родительского каталога - https://gedeon.io/panel/director/
   *
   * @var $parentFolderURI string
   **/
  public static $parentFolderURI;


  /**
   * Устанавливаем / строим константы путей
   *
   * @return void
   **/
  public static function setPaths()
  {

    self::$root = $_SERVER['DOCUMENT_ROOT'] . '/';

    self::$protocolVersion = $_SERVER['SERVER_PROTOCOL'];

    self::$protocolType = $_SERVER['REQUEST_SCHEME'];

    self::$domain = $_SERVER['HTTP_HOST'];

    self::$TLD = strstr(self::$domain, '.');

    self::$URL = self::$protocolType . '://' . self::$domain . '/';

    self::$URN = $_SERVER['REQUEST_URI'];

    self::$URI = self::deleteSlashes(self::$URL . self::$URN);

    self::$panelURL = self::deleteSlashes(self::$URL . self::$panelKey);

    self::$panelURN = self::deleteSlashes(str_replace(self::$panelURL, '/', self::$URI));

    self::$parentFolderURI = dirname(self::$URI) . '/';

  }


  /**
   * Проверяет, находится ли приложение на локальном сервере
   *
   * @return boolean
   **/
  public static function isLocalServer(): bool
  {

    ### если отсутствует домен верхнего уровня (TDL) - сервер локальный
    return (empty(self::$TLD));

  }


  /**
   * Строим массив из urn
   *
   * @param $mode string
   * @return array
   **/
  public static function buildArrayPath(string $mode = 'surface'): array
  {

    switch ($mode) {

      case 'panel':
        $stringPath = self::deleteSlashes(self::$panelURN, 'both');
        break;

      default:
        $stringPath = self::deleteSlashes(self::$URN, 'both');

    }

    return explode('/', $stringPath);

  }


  /**
   * Проверяем, находимся ли мы на главной странице
   *
   * @return boolean
   **/
  public static function isIndex(): bool
  {

    return
      empty(self::buildArrayPath()[0]);

  }


  /**
   * Проверяем, находимся ли мы в панели управления
   *
   * @return boolean
   **/
  public static function isPanel(): bool
  {

    return
      (self::buildArrayPath()[0] == self::deleteSlashes(self::$panelKey, 'both'));

  }


  /**
   * Проверяем, находимся ли мы на странице ошибки 404
   *
   * @return boolean
   **/
  public static function is404(): bool
  {

    # отдельное объявление переменной необходимо, так как функция 'end()'
    # преобразовывает представление массива, меняя его содержимое
    $arrayPath = self::buildArrayPath();

    return
      (end($arrayPath) == '404');

  }


  /**
   * Подключаем, если нужно, необходимый контроллер
   *
   * @uses panel
   * @uses redirector
   * @return void
   **/
  public static function connectToController()
  {

    if (self::isPanel())
      panel::connect();
    else
      redirector::to404();

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
  public static function deleteSlashes(string $string, string $position = 'plenty'): string
  {

    switch ($position) {

      case ('first'):
        return ltrim($string, '/');

      case ('last'):
        return rtrim($string, '/');

      case ('both'):
        return trim($string, '/');

      case ('plenty'):
        return preg_replace('@(?<!\:)\/\/+@', '/', $string);

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
   * стало - /pages/contacts/moscow.php
   *
   * @param $folderPath string строка пути
   * @param $extension string расширение файла
   * @return string
   **/
  public static function folderToFile(string $folderPath, string $extension = 'php'): string
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


  /**
   * Проверка наличия POST или GET запроса
   *
   * @param $returnMethod boolean возвращать или не возвращать тип запроса в виде строки
   * @param $keyConditions array условие нахождение в запросе определенного параметра (элемента массива)
   * @return mixed
   **/
  public static function haveRequest(bool $returnMethod = false, array $keyConditions = [])
  {

    ### проверка наличия запроса
    if (empty($_REQUEST)) return false;

    ### проверка наличия ключей в запросе, если есть условия ($keyConditions)
    if (!empty($keyConditions)) {
      foreach ($keyConditions as $keyCondition) {
        if (!array_key_exists($keyCondition, $_REQUEST))
          return false;
      }
    }

    ### если нужно, возвращаем тип запроса
    if ($returnMethod) return $_SERVER["REQUEST_METHOD"];
    else return true;

  }


}