<?php
/**
 * Created by PhpStorm.
 * User: ilya
 * Date: 09.08.17
 * Time: 22:11
 */

namespace controllers\panel;

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
   * @uses router
   * @return void
   **/
  public static function connect()
  {

    ### если есть какое-либо действие (экшн)
    if (isset(router::$arrayPath[1])) {

      switch ($action = router::$arrayPath[1]) {

        ### добавление страницы / директории
        case ('add'):
          self::addAction();
          break;

        ### удаление страницы / директории
        case ('delete'):
          self::deleteAction();
          break;

        ### редактирование страницы / директории
        case ('edit'):
          self::editAction();
          break;

        ### просмотр страницы / директории
        default:
          self::getElement(intval($action));
          break;

      }

      ### вывод дерева элементов
    } else {

      self::showTree();

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
  private static function addAction()
  {

    ### если есть запрос - добавляем
    if (router::haveRequest('POST', ['key', 'type', 'parent'])) {

      director::addition($_POST);

      ### если нет - загружаем шаблон добавления элемента
    } else {

      ### получаем список каркасов для select'а
      panel::$printer->injectVariable('carcasses', panel::$printer->getCarcassesList());

      ### выводим шаблон
      panel::$printer
        ->buildBasicTemplate('basicPanelTemplate', ['purport' => 'director/add'])
        ->print();

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
  private static function deleteAction()
  {

    if (router::haveRequest('POST', ['ID', 'originURI']))
      director::delete($_POST['ID'], $_POST['originURI']);

    else
      redirector::to404();

  }


  /**
   * Получение элемента
   *
   * @uses router
   * @uses redirector
   * @uses panel
   * @uses director
   * @return void
   **/
  private static function editAction()
  {

    ### проверяем, дан ли нам ID
    if (empty($elementID = intval(router::$arrayPath[2]))) redirector::to404();

    ### внедряем элемент по ID
    panel::$printer->injectVariable('element', director::getElement($elementID));

    ### получаем список каркасов для select'а
    panel::$printer->injectVariable('carcasses', panel::$printer->getCarcassesList());

    ### выводим шаблон
    panel::$printer
      ->buildBasicTemplate('basicPanelTemplate', ['purport' => 'director/edit'])
      ->print();

  }


  /**
   * Получение элемента
   *
   * @param $elementID integer идентификатор элемента
   * @uses director
   * @return void
   **/
  private static function getElement(int $elementID)
  {

    // TODO: Делаем

  }


  /**
   * Вывод дерева элементов
   *
   * @uses panel
   * @uses director
   * @uses router
   * @return void
   **/
  private static function showTree()
  {

    ### внедряем переменные
    panel::$printer->injectVariables([

      ### все элементы дерева
      'elements' => director::getElements(),

      ### полный путь
      'URI' => router::$URI

    ]);

    ### вывод дерева
    panel::$printer->buildBasicTemplate('basicPanelTemplate', ['purport' => 'director/index'])->print();

  }


}