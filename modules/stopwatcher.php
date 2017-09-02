<?php
/**
 * Created by PhpStorm.
 * User: ilya
 * Date: 23.08.17
 * Time: 22:53
 */

namespace modules;


/**
 * Секундомер
 **/
class stopwatcher
{


  /**
   * Засекаем время выполнения скрипта / скриптов (в мс)
   *
   * @param $anonymousFunction callable анонимная функция, внутри которой
   * находится скрипт / скрипты, время которых необходимо засечь.
   * @return float
   **/
  public static function pinpointScriptOutlay(callable $anonymousFunction): float
  {

    $startTime = microtime(true);

    call_user_func($anonymousFunction);

    return microtime(true) - $startTime;

  }


  /**
   * Получить время загрузки приложения (в мс)
   *
   * @return float
   **/
  public static function getPageLoadTime(): float
  {

    return microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];

  }


}