<?php
/**
 * Created by PhpStorm.
 * User: ilya
 * Date: 27.07.17
 * Time: 23:02
 */

namespace core;

use PDO;
use modules\editor;
use Exception;


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
  private static $PDO;


  /**
   * Подключение к базе данных
   *
   * @uses PDO
   * @return void
   **/
  public static function connect()
  {

    ### обрабатываем название кодировки
    $PDOcharset = self::formatCharsetForPDO(self::$charset);

    ### имя источника данных
    $dsn = 'mysql:
    host=' . self::$server . ';
    dbname=' . self::$database . ';
    charset=' . $PDOcharset;

    ### специальные настройки
    $options = [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];

    ### подключаемся
    self::$PDO = new PDO($dsn, self::$user, self::$password, $options);

  }


  /**
   * Удаляем дефисы из названия кодировки (требует сам PDO)
   *
   * @param $originalCharset string название кодировки, которую необходимо обработать
   * @return string
   **/
  private static function formatCharsetForPDO(string $originalCharset): string
  {

    return str_replace('-', '', $originalCharset);

  }


  /**
   * Выполнение ручного запроса
   *
   * @param $queryString string строка (текст) запроса
   * @param $queryVariables mixed массив со переменными-значениями столбца/ов
   * @return array
   **/
  public static function query(string $queryString, $queryVariables): array
  {

    ### готовим запрос
    $query = self::$PDO->prepare($queryString . ';');

    ### если переданный аргумент переменной не массив - создаем массив
    if (!is_array($queryVariables)) $queryVariables = [$queryVariables];

    ### внедряем переменные и отправлям запрос
    $query->execute($queryVariables);

    ### распаковываем ответ
    return $query->fetchAll();

  }


  /**
   * Получить записи таблицы
   *
   * Возможна фильтрация
   *
   * @param $tableName string название таблицы
   * @param $filtersArray array условия фильтрации записей
   * @param $columnsNames string|array названия столбзов
   * @return array
   **/
  public static function getRows(string $tableName, array $filtersArray = [], $columnsNames = '*'): array
  {

    ### генерируем строку с фильтрами
    $filtersString = (!empty($filtersArray)) ?
      self::generateFiltersString(array_keys($filtersArray)) : '';

    ### генерируем строку со столбцами
    $columnsString = self::generateColumnsString($columnsNames);

    ### возвращаем уже полученный ответ от БД
    return
      self::query('SELECT ' . $columnsString . ' FROM `' . $tableName . '` ' . $filtersString,
        array_values($filtersArray)
      );

  }


  /**
   * Добавить запись в таблицу
   *
   * @param $tableName string название таблицы
   * @param $values array добавляемые столбцы и их значения
   * @return array
   **/
  public static function addRow(string $tableName, array $values): array
  {

    $columnsString = self::generateColumnsString(array_keys($values));

    $valuesString = self::generateValuesString($values);

    return
      self::query('INSERT INTO `' . $tableName . '` (' . $columnsString . ') VALUES (' . $valuesString . ')',
        array_values($values)
      );

  }


  /**
   * Удаление записи из таблицы
   *
   * @param $tableName string название таблицы
   * @param $rowID integer ID записи, которую удаляем из таблицы
   * @param $keyColumn string ключ-столбец, по которому ищем запись в таблице
   * @return array
   **/
  public static function deleteRow(string $tableName, int $rowID, string $keyColumn = 'id'): array
  {

    return self::query('DELETE FROM `' . $tableName . '` WHERE `' . $keyColumn . '` = ?', $rowID);

  }


  /**
   * Превращаем массив с фильтрами в строку с фильтрами
   *
   * Пример возвращаемой строки:
   * 'WHERE filterExample01 = ? AND filterExample02 = ?'
   *
   * @param $filtersNames array массив с названиями фильтров
   * @return string
   **/
  private static function generateFiltersString(array $filtersNames): string
  {

    return 'WHERE ' . editor::arrayToString($filtersNames, ' AND ', '` = ?', '`');

  }


  /**
   * Превращаем массив со столбцами в строку со столбацими
   *
   * Пример возвращаемой строки:
   * '`name`, `phone`'
   *
   * @param $columnsNames string|array строка с названием столбца (или массив с названиями столбцов)
   * @uses editor
   * @uses Exception
   * @throws Exception выдает ошибку, когда передан некорректный аргумент
   * @return string
   **/
  private static function generateColumnsString($columnsNames): string
  {

    if ($columnsNames === '*') return $columnsNames;

    elseif (is_string($columnsNames)) return '`' . $columnsNames . '`';

    elseif (is_array($columnsNames))
      return editor::arrayToString($columnsNames, ', ', '`', '`');

    else throw new Exception('Передан некорректный аргумент (не строка и не массив).');

  }


  /**
   * Генерируем строку из значений
   *
   * Пример возвращаемой строки:
   * '?, ?, ?'
   *
   * @param $values array массив со значениями
   * @return string
   **/
  private static function generateValuesString(array $values)
  {

    $values = array_map(function () {
      return '?';
    }, $values);

    return implode(', ', $values);

  }


}