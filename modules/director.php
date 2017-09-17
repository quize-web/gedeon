<?php
/**
 * Created by PhpStorm.
 * User: ilya
 * Date: 06.08.17
 * Time: 18:55
 */

namespace modules;

use core\redirector;
use core\router;
use core\database;
use controllers\panel\panel;


/* * *
 * Иерархическое представление архитектуры сайта
 * * */

class director
{


  /**
   * Логика добавления новой страницы / каталога / фильтра
   *
   * @uses database
   * @uses redirector
   * @uses router
   * @return void
   **/
  public static function addition()
  {

    $_POST['template'] = panel::$printer->createTemplateJSON($_POST['carcass']);
    unset($_POST['carcass']);

    database::addRow('pages', $_POST);

    redirector::redirectTo(router::$parentFolderURI);

  }

  /**
   * Логика удаления страницы / каталога / фильтра
   *
   * @param $elementID integer ID элемента, который удаляем
   * @uses database
   * @return void
   **/
  public static function delete(int $elementID, $redirectTo)
  {

    database::deleteRow('pages', $elementID);

    redirector::redirectTo($redirectTo);

  }


}