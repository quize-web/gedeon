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
   * @param $dataForAddition array массив данных для добавление в базу данных
   * @uses database
   * @uses redirector
   * @uses router
   * @return void
   **/
  public static function addition(array $dataForAddition)
  {

    $dataForAddition['template'] = panel::$printer->createJSONtemplate($dataForAddition['carcass']);
    unset($dataForAddition['carcass']);

    database::addRow('pages', $dataForAddition);

    redirector::redirectTo(router::$parentFolderURI);

  }

  /**
   * Логика удаления страницы / каталога / фильтра
   *
   * @param $elementID integer ID элемента, который удаляем
   * @param $redirectTo string страница, на которую переходим после удаления
   * @uses database
   * @return void
   **/
  public static function delete(int $elementID, string $redirectTo)
  {

    database::deleteRow('pages', $elementID);

    redirector::redirectTo($redirectTo);

  }

  /**
   * Логика редактирование страницы / каталога / фильтра
   *
   * @param $elementID integer ID элемента, который редактируем
   * @param
   * @return void
   **/
  public static function editing(int $elementID)
  {

    // TODO: Делаем

  }


  /**
   * Получение страницы / каталога / фильтра
   *
   * @param $elementID integer ID элемента, который получаем
   * @uses redirector
   * @return array
   * TODO: Делаем
   **/
  public static function getElement($elementID): array
  {

    $element = database::getRows('pages', ['id' => $elementID]);

    if (empty($element)) redirector::to404();

    return $element;

  }


  /**
   * Получения элементов дерева
   *
   * @param $parentID integer ID родителя
   * @uses database
   * @return array
   **/
  public static function getElements(int $parentID = 0): array
  {

    $elements = database::getRows('pages',
      ['parent' => '0'],
      ['id', 'key', 'variableTable', 'type']
    );

    $sortedElements = self::sortElements($elements);

    return $sortedElements;

  }


  /**
   * Сортировка элементов по типу
   *
   * @param $elementsArray array элементы, представленные в виде массива
   * @uses editor
   * @return array
   **/
  private static function sortElements($elementsArray)
  {

    return editor::sortTwoDimensionalArray($elementsArray, 'type', ['directory', 'funnel', 'document']);

  }


}