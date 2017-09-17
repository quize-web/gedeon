<?php
/**
 * Created by PhpStorm.
 * User: ilya
 * Date: 09.09.17
 * Time: 22:55
 */

namespace modules;


/* * *
 * Работа с файлами
 * * */
class keeper
{


  /**
   * Получить массив со списков файлов и каталогов в каталоге
   *
   * @param $folderPath string путь к каталогу с файлами
   * @return array
   **/
  public static function getFilesList(string $folderPath): array
  {

    return array_slice(scandir($folderPath), 2);

  }


  /**
   * Получить название файлов
   *
   * @param $files array файлы в виде массива
   * @return array
   **/
  public static function getFilesNames(array $files): array
  {

    foreach ($files as $key => $file) {

      $files[$key] = self::getFileName($file);

    }

    return  $files;

  }


  /**
   * Получить название файла
   *
   * @param $file string файл в виде строки
   * Пример аргумента: 'index.php'
   * @return string
   * Пример результата: 'index'
   **/
  public static function getFileName(string $file): string
  {

    return stristr($file, '.', true);

  }


}