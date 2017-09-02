<?php
/**
 * Created by PhpStorm.
 * User: ilya
 * Date: 27.07.17
 * Time: 23:02
 */

namespace core;

use PDO;


/*
 * Методы для работы с базой данных
 * */

class database
{


  /**
   * Сервер, на котором находится база данных
   *
   * @var $server string
   **/
  public static $server = 'localhost';

  /**
   * Пользователь, у которого есть права на управления базой данных
   *
   * @var $user string
   **/
  public static $user = 'root';

  /**
   * Пароль для входа пользователя
   *
   * @var $password string
   **/
  public static $password = 'root';

  /**
   * Название необходимой нам базы данных
   *
   * @var $database string
   **/
  public static $database = 'gedeon';

  /**
   * Кодировка, с которой работает база данных
   *
   * @var $charset string
   **/
  public static $charset = 'utf8';

  /**
   * Объект, позволяющий управлять методами, работающих с базой данных
   *
   * @var $PDO object
   **/
  public static $PDO;


  /**
   * Подключение к базе данных
   *
   * @uses PDO
   * @return void
   **/
  public static function connect()
  {

    ### удаляем дефисы из названия кодировку (требует сам PDO)
    $PDOcharset = str_replace('-', '', self::$charset);

    ### имя источника данных
    $dsn = 'mysql:
    host=' . self::$server . ';
    dbname=' . self::$database . ';
    charset=' . $PDOcharset;

    ### особые настройки
    $options = [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];

    ### подключаемся
    self::$PDO = new PDO($dsn, self::$user, self::$password, $options);

  }


  /**
   * Получить все записи таблицы
   *
   * @param $tableName string название таблицы
   * @return array
   **/
  public static function getAll(string $tableName): array
  {

    return self::$PDO->query('SELECT * FROM ' . $tableName)->fetchAll();

  }


  /**
   * Получить отфильтрованные записи таблицы
   *
   * @param $tableName string название таблицы
   * @param $filtersArray array условия фильтрации записей
   * @return array
   **/
  public static function getFiltered(string $tableName, array $filtersArray): array
  {

    $filtersString = self::filtersArrayToFiltersString($filtersArray);

    $query = self::$PDO->prepare('SELECT * FROM `' . $tableName . '` WHERE ' . $filtersString);

    $query->execute(array_values($filtersArray));

    return $query->fetchAll();

  }


  /**
   * Превращаем массив с фильтрами в строку с фильтрами
   *
   * Пример возвращаемой строки:
   * filter01 = ?, filter02 = ?
   *
   * @param $filtersArray array массив с фильтрами
   * @return string
   **/
  private static function filtersArrayToFiltersString(array $filtersArray): string
  {

    $filtersNames = array_keys($filtersArray);

    for ($i = 0; $i < count($filtersNames); $i++) {

      $filtersNames[$i] .= ' = ?';

    }

    return implode(' AND ', $filtersNames);

  }


}