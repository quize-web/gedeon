<?php
/**
 * Created by PhpStorm.
 * User: ilya
 * Date: 06.08.17
 * Time: 18:55
 */

namespace modules;

use core\router;


class director
{


  /**
   * Логика добавления новой страницы / каталога
   *
   * @return void
   **/
  public static function addition()
  {

    //

    router::redirectTo(router::$parentFolderURI);

  }


}