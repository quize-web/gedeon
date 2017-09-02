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


/**
 * Иерархическое представление архитектуры сайта
 **/
class director
{


  /**
   * Логика добавления новой страницы / каталога
   *
   * @uses redirector
   * @uses router
   * @return void
   **/
  public static function addition()
  {

    //

    redirector::redirectTo(router::$parentFolderURI);

  }


}