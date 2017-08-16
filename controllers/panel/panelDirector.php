<?php
/**
 * Created by PhpStorm.
 * User: ilya
 * Date: 09.08.17
 * Time: 22:11
 */

namespace controllers\panel;

use core\router;
use controllers\panel\panel;
use modules\director;

/**
 * Контроллер для каталог director
 **/
class panelDirector
{


  /**
   * Подключаемся к контроллеру director'а
   *
   * @return void
   **/
  public static function connect()
  {

    ### если есть какое-либо действие
    if ($action = router::buildArrayPath('panel')[1] ?? false) {

      switch ($action) {

        ### добавление страницы / директории
        case ('add'):
          self::add();
          break;

        default:
          router::to404();

      }

      ### вывод director'а
    } else {

      panel::$printer->printBasicTemplate('basicPanelTemplate', ['purport' => 'director']);

    }

  }


  /**
   * Контроллер добавления страницы / директории
   *
   * @return void
   **/
  public static function add()
  {

    if (router::haveRequest()) director::addition();
    else
      panel::$printer->printBasicTemplate('basicPanelTemplate', ['purport' => 'directorAdd']);

  }


}