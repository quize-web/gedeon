<?php
/**
 * Created by PhpStorm.
 * User: ilya
 * Date: 02.09.17
 * Time: 14:52
 */

namespace core;

use Exception;
use core\router;
use controllers\panel\panel;

/* * *
 * Управление редиректами и заголовками
 * * */

class redirector
{

  /**
   * Редирект на страницу с ошибкой 404
   *
   * Заголовок header также передается с ошибкой 404
   *
   * @uses router
   * @uses panel
   * @return void
   **/
  public static function to404()
  {

    self::sendHeader(404);

    if (router::$isPanel)
      panel::$printer->buildBasicTemplate('basicPanelTemplate', ['purport' => 'error404'])->print();

    exit;

  }


  /**
   * Редирект на страницу
   *
   * @param $address string страница, на которую нужно сделать редирект
   * @param $returnHeader boolean отправлять или не отправлять заголовок редиректа
   * @return void
   **/
  public static function redirectTo(string $address)
  {

    self::sendHeader(301);

    header('Location: ' . $address);

    exit;

  }


  /**
   * Отослать заголовок
   *
   * @param $headerCode integer код заголовка (например: '404')
   * @uses Exception
   * @uses router
   * @throws Exception выдает ошибку, если код заголовка не задан
   * @return void
   **/
  public static function sendHeader(int $headerCode)
  {

    switch ($headerCode) {

      case 301:
        header(router::$protocolVersion . ' 301 Moved Permanently', true, 301);
        break;

      case 404:
        header(router::$protocolVersion . ' 404 Not Found', true, 404);
        break;

      default:
        throw new Exception('Код заголовка не задан.');
        break;

    }

  }


}