<?php
/**
 * Created by PhpStorm.
 * User: ilya
 * Date: 10.09.17
 * Time: 0:25
 */

namespace modules;

use Exception;


/* * *
 * Работа со строками и массивами
 * * */

class editor
{


  /**
   * Превратить массив в строку
   *
   * Комбинация implode и array_map
   *
   * @param $array array массив, который превращаем
   * @param $spacer string разделитель элементов массива
   * @param $afterArrayElementValue string строка после элемента массива
   * @param $beforeArrayElementValue string строка до элемента массива
   * @return string
   **/
  public static function arrayToString(
    array $array,
    string $spacer,
    string $afterArrayElementValue = '',
    string $beforeArrayElementValue = ''
  ): string
  {

    if (!empty($afterArrayElementValue) OR !empty($beforeArrayElementValue)) {

      $updatedArray = array_map(

      ### callback функция "обрамления" каждого элемента массива
        function ($arrayElement) use ($afterArrayElementValue, $beforeArrayElementValue) {

          return $beforeArrayElementValue . $arrayElement . $afterArrayElementValue;

        }, $array);

    } else $updatedArray = $array;

    # разделяем каждый элемент через $spacer
    # разделитель и превращаем в строку
    return implode($spacer, $updatedArray);

  }


  /**
   * Удаление слешей из различных положений строки ($position)
   *
   * Пример:
   * было - /pages/contacts/
   * стало - pages/contacts/ ### $position = 'first'
   *
   * было - /pages/contacts/
   * стало - /pages/contacts ### $position = 'last'
   *
   * было - /pages/contacts/
   * стало - pages/contacts ### $position = 'both'
   *
   * было - https://gedeon.com//pages//contacts/
   * стало - https://gedeon.com/pages/contacts/ ### $position = 'plenty'
   * такой случай ^ является издержкой красивых констант путей в приложении
   *
   * @param $string string строка, откуда удаляем
   * @param $position string откуда удаляем слеши
   * @return string
   **/
  public static function deleteSlashes(string $string, string $position = 'plenty'): string
  {

    switch ($position) {

      case ('first'):
        return ltrim($string, '/');

      case ('last'):
        return rtrim($string, '/');

      case ('both'):
        return trim($string, '/');

      case ('plenty'):
        return preg_replace('@(?<!\:)\/\/+@', '/', $string);

      default:
        return false;

    }

  }


  /**
   * Сортировка элементов двумерного массива
   *
   * @param $array array исходный массив
   * @param $key string ключ, по которому сортируем
   * @param $order array порядок, по которому сортируем
   * @return array
   **/
  public static function sortTwoDimensionalArray(array $array, string $key, array $order): array
  {

    usort($array, function ($a, $b) use ($array, $key, $order) {

      $aPosition = array_search($a[$key], $order);

      $bPosition = array_search($b[$key], $order);

      if($aPosition == $bPosition) return 0;
      else return ($aPosition < $bPosition) ? -1 : 1;

    });

    return $array;

  }


}