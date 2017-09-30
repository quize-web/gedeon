<?php
/**
 * Created by PhpStorm.
 * User: ilya
 * Date: 27.07.17
 * Time: 23:05
 */

namespace core;

use controllers\panel\panel;
use modules\editor;


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
   * URN массив
   *
   * @var array
   **/
  public static $arrayPath;

  /**
   * Свойство определяет, находимся ли мы в административной панели
   *
   * @var boolean
   **/
  public static $isPanel;


  /**
   * Устанавливаем / строим константы путей
   *
   * @uses editor
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

    self::$URN = editor::deleteSlashes($_SERVER['REQUEST_URI'] . '/');

    self::$URI = editor::deleteSlashes(self::$URL . self::$URN);

    self::$panelURL = editor::deleteSlashes(self::$URL . self::$panelKey);

    self::$panelURN = editor::deleteSlashes(str_replace(self::$panelURL, '/', self::$URI));

    self::$parentFolderURI = dirname(self::$URI) . '/';

    self::$arrayPath = self::buildArrayPath();

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
   * Строим массив из URN
   *
   * @uses editor
   * @return array
   **/
  public static function buildArrayPath(): array
  {

    $stringPath = editor::deleteSlashes(self::$URN, 'both');

    $URNarray = explode('/', $stringPath);

    if (self::isPanel($URNarray))
      array_shift($URNarray);

    return $URNarray;

  }


  /**
   * Проверяем, находимся ли мы в панели управления и формируем значение свойства $isPanel
   *
   * @param $URNarray array
   * @uses editor
   * @return boolean
   **/
  private static function isPanel(array $URNarray): bool
  {

    self::$isPanel =
      ($URNarray[0] == editor::deleteSlashes(self::$panelKey, 'both'));

    return self::$isPanel;

  }


  /**
   * Проверяем, находимся ли мы на главной странице
   *
   * @return boolean
   **/
  public static function isIndex(): bool
  {

    return
      empty(self::$arrayPath[0]);

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
    $arrayPath = self::$arrayPath;

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

    if (self::$isPanel)
      panel::connect();
    else
      redirector::to404();

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
   * @param $methodTypeCondition string условие на тип метода запроса
   * @param $keyConditions array условие нахождение в запросе определенного параметра (элемента массива)
   * @return mixed
   **/
  public static function haveRequest(string $methodTypeCondition = '', array $keyConditions = [])
  {

    ### проверка наличия запроса
    if (empty($_REQUEST)) return false;

    ### проверка условия на тип метода запроса
    if (!empty($methodTypeCondition) AND $methodTypeCondition !== $_SERVER['REQUEST_METHOD'])
      return false;

    ### проверка наличия ключей в запросе, если есть условие для ключей ($keyConditions)
    if (!empty($keyConditions)) {
      foreach ($keyConditions as $keyCondition) {
        if (!array_key_exists($keyCondition, $_REQUEST))
          return false;
      }
    }

    return true;

  }


  /**
   * Проверяем путь на визуальные ошибки
   *
   * Например на множественные слеши или заглавные буквы
   *
   * @uses redirector
   * @uses editor
   * @return void
   **/
  public static function checkRouteAdequacy()
  {

    if (preg_match('@[A-Z]+|\/{2,}@', $_SERVER['REQUEST_URI'])) {

      redirector::redirectTo(
        editor::deleteSlashes(
          strtolower(self::$URI)
        )
      );

    }

  }


}