<?php
/**
 * Created by PhpStorm.
 * User: ilya
 * Date: 30.07.17
 * Time: 12:06
 */

namespace controllers\panel;

use core\database;
use core\router;
use modules\printer;

/**
 *  Главный контроллер панели
 **/
class panel
{

  /**
   * Экземпляр класса Printer
   *
   * @var $printer object
   **/
  public static $printer;


  /**
   * Подключаемся к контроллеру панели
   *
   * @return void
   **/
  public static function connect()
  {

    ### создаем объект конструктора
    self::$printer = new printer('panel');

    ### регистрируем базовый шаблон панели
    self::$printer->registerBasicTemplate('basicPanelTemplate', 'main', [
      'head' => 'main',
      'navigation' => 'main',
      'footer' => 'main'
    ]);

    ### ищем необходимый контроллер
    self::directMe();

  }


  /**
   * Поиск контроллера для текущей страницы
   *
   * @return void
   **/
  public static function directMe()
  {

    ### если в директории
    if ($folder = router::buildArrayPath()[1] ?? false) {

      switch ($folder) {

        case 'director':
          director::connect();
          break;

      }

      ### вывод корня панели
    } else {

      self::$printer->printBasicTemplate('basicPanelTemplate', ['purport' => 'index']);

    }

  }


}