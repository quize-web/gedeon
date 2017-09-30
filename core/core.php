<?php
/**
 * Created by PhpStorm.
 * User: ilya
 * Date: 28.07.17
 * Time: 22:02
 */

namespace core;


use modules\stopwatcher;

class core
{


  /**
   * Кодировка приложения
   *
   * @var $charset string
   **/
  public static $charset = 'utf-8';

  /**
   * Режим обработки ошибок
   *
   * @var $errorHandlingMode integer
   **/
  public static $errorHandlingMode = 1;

  /**
   * Замерять ли скорость загрузки страницы
   *
   * @var $revealPageLoadTime boolean включен замер или выключен
   **/
  public static $showApplicationTimeOutlay = true;


  /**
   * Подключение необходимых модулей перед построением и запуском ядра
   *
   * @return void
   **/
  public static function includeNecessaryModules()
  {

    ### преобразование строк и массивов
    require_once(__ROOT__ . '/modules/editor.php');

  }


  /**
   * Подключение необходимых классов для дальнейшей настройки и запуска
   *
   * @return void
   **/
  public static function buildCore()
  {

    self::includeNecessaryModules();

    ### управление путями
    require_once('router.php');

    ### методы для работы с базой данных
    require_once('database.php');

  }


  /**
   * Запуск ядра
   *
   * @return void
   **/
  public static function run()
  {

    self::engageToggles();

    if (self::$showApplicationTimeOutlay)
      stopwatcher::showApplicationTimeOutlay();

  }


  /**
   * Запуск процессов, необходимых для функционирования приложения
   *
   * Внимание: порядок выполнения важен
   *
   * @uses database
   * @uses router
   * @return void
   **/
  private static function engageToggles()
  {

    ### режим обработки ошибок
    self::setErrorHandlingMode(self::$errorHandlingMode);

    ### устаналиваем свойства-пути
    router::setPaths();

    ### регистрируем модули и классы
    self::bootClasses();

    ### подключаемся к базе данных
    database::connect();

    ### проверяем путь на визуальные ошибки
    router::checkRouteAdequacy();

    ### подключаемся к необходимому контроллеру
    router::connectToController();

  }

  /**
   * Регистрация загрузчика классов
   *
   * @uses router
   * @return void
   **/
  private static function bootClasses()
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
   * Режим обработки ошибок
   *
   * 0 - выключить отображение всех ошибок
   * 1 - включить отображение всех ошибок
   *
   * @param $errorHandlingMode integer свитчер (1 или 0)
   * @return void
   **/
  private static function setErrorHandlingMode(int $errorHandlingMode = 1)
  {

    ### ошибки выполнения
    ini_set('display_errors', $errorHandlingMode);

    ### ошибки при запуске php
    ini_set('display_startup_errors', $errorHandlingMode);

    ### уровень отображения ошибок
    error_reporting(($errorHandlingMode) ? E_ALL : 0);

  }


}