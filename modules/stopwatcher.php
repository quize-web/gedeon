<?php
/**
 * Created by PhpStorm.
 * User: ilya
 * Date: 23.08.17
 * Time: 22:53
 */

namespace modules;


/* * *
 * Секундомер
 * * */
class stopwatcher
{


  /**
   *  Отобразить время выполнения скрипта / скриптов
   *
   * @param $anonymousFunction callable анонимная функция, внутри которой
   * находится скрипт / скрипты, время которых необходимо засечь.
   * @return void
   **/
  public static function showScriptTimeOutlay(callable $anonymousFunction)
  {

    echo 'Script time outlay: ' . self::pinpointScriptTimeOutlay($anonymousFunction) . ' ms.';

  }


  /**
   * Получить время выполнения скрипта / скриптов (в мс)
   *
   * @param $anonymousFunction callable анонимная функция, внутри которой
   * находится скрипт / скрипты, время которых необходимо засечь.
   * @return float
   **/
  private static function pinpointScriptTimeOutlay(callable $anonymousFunction): float
  {

    $startTime = microtime(true);

    call_user_func($anonymousFunction);

    return microtime(true) - $startTime;

  }


  /**
   *  Отобразить время загрузки приложения
   *
   * @return void
   **/
  public static function showApplicationTimeOutlay()
  {
    
    echo 'Application time outlay: ' . self::pinpointApplicationTimeOutlay() . ' ms.';

  }


  /**
   * Получить время загрузки приложения (в мс)
   *
   * @return float
   **/
  private static function pinpointApplicationTimeOutlay(): float
  {

    return microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];

  }


}