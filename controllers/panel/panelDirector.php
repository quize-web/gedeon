<?php
/**
 * Created by PhpStorm.
 * User: ilya
 * Date: 09.08.17
 * Time: 22:11
 */

namespace controllers\panel;

use core\database;
use core\router;
use modules\director;
use core\redirector;

/* * *
 * Контроллер для каталог director
 * * */

class panelDirector
{


  /**
   * Подключаемся к контроллеру director'а
   *
   * @uses panel
   * @uses redirector
   * @uses router
   * @return void
   **/
  public static function connect()
  {

    ### если есть какое-либо действие (экшн)
    if ($action = router::buildArrayPath('panel')[1] ?? false) {

      switch ($action) {

        ### добавление страницы / директории
        case ('add'):
          self::add();
          break;

        case ('delete'):
          self::delete();
          break;

        default:
          redirector::to404();

      }

      ### вывод director'а
    } else {

      panel::$printer->injectVariables([
        'elements' => database::getRows(
          'pages',
          ['parent' => '0'],
          ['id', 'key', 'variableTable', 'type']
        ),
        'panelURL' => router::deleteSlashes(router::$panelURL, 'last'),
        'URI' => router::$URI
      ]);

      panel::$printer->printBasicTemplate('basicPanelTemplate', ['purport' => 'director/index']);

    }

  }


  /**
   * Контроллер добавления страницы / каталога / фильтра
   *
   * @uses router
   * @uses director
   * @uses panel
   * @return void
   **/
  public static function add()
  {

    if (router::haveRequest()) director::addition();
    else {

      panel::$printer->injectVariable('carcasses', panel::$printer->getCarcassesList());

      panel::$printer->printBasicTemplate('basicPanelTemplate', ['purport' => 'director/add']);

    }

  }


  /**
   * Контроллер удаления страницы / каталога / фильтра
   *
   * @uses router
   * @uses director
   * @uses redirector
   * @return void
   **/
  public static function delete()
  {

    if (router::haveRequest(false, ['ID', 'originURI']))
      director::delete($_POST['ID'], $_POST['originURI']);

    else
      redirector::to404();

  }


}