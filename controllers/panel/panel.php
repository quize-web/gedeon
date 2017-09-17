<?php
/**
 * Created by PhpStorm.
 * User: ilya
 * Date: 30.07.17
 * Time: 12:06
 */

namespace controllers\panel;

use core\router;
use modules\printer;
use core\redirector;

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
   * @uses printer
   * @return void
   **/
  public static function connect()
  {

    ### создаем объект конструктора шаблонов
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
   * @uses router
   * @uses redirector
   * @return void
   **/
  public static function directMe()
  {

    ### если в директории
    if ($folder = router::buildArrayPath()[1] ?? false) {

      switch ($folder) {

        case '404':
          self::$printer->printBasicTemplate('basicPanelTemplate', ['purport' => 'error404']);
          break;

        case 'director':
          panelDirector::connect();
          break;

        default: redirector::to404();

      }

      ### вывод корня панели
    } else {

      self::$printer->printBasicTemplate('basicPanelTemplate', ['purport' => 'index']);

    }

  }


}