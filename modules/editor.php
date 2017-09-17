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


}