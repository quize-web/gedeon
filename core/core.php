<?php
/**
 * Created by PhpStorm.
 * User: ilya
 * Date: 28.07.17
 * Time: 22:02
 */

namespace core;


class core
{

  /**
   * Запуск процессов, необходимых для функционирования приложения
   *
   * @return void
   **/
  public static function run()
  {

    router::setPaths();

    tuner::bootClasses();

    database::connect();


    router::connectToController();

  }

}