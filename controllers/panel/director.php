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

/**
 * Контроллер для каталога director
 **/
class director
{


  /**
   * Подключаемся к контроллеру director'а
   *
   * @return void
   **/
  public static function connect()
  {

    ### если в поддиректории
    if ($subfolder = router::buildArrayPath()[2] ?? false) {

      ### вывод director'а
    } else {

      panel::$printer->printBasicTemplate('basicPanelTemplate', ['purport' => 'director']);

    }

  }

}