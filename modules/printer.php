<?php
/**
 * Created by PhpStorm.
 * User: ilya
 * Date: 31.07.17
 * Time: 20:24
 */

namespace modules;

use Exception;
use core\router;


/**
 * Конструктор для построения frontend части приложения
 * */
class printer
{

  /**
   * Путь к папкам с секторами
   *
   * @var $templatesPath string
   **/
  private $componentsPath;

  /**
   * Путь к каркасам
   *
   * @var $carcassesPath string
   **/
  private $carcassPath;

  /**
   * Текущий выбранный каркас
   *
   * @var $chosenCarcass string
   **/
  private $chosenCarcass;

  /**
   * Секторы выбранного каркаса
   *
   * @var $chosenSectors array
   **/
  private $carcassSectors = [];

  /**
   * Базовый шаблон контроллера
   *
   * @var $basicTemplates array
   **/
  private $basicTemplates = [];

  /**
   * Список переменных, используемых в построенном шаблоне
   *
   * @var $variables array
   **/
  private $variables = [];


  /**
   * Задаем пути к папкам с компонентами и каркасами
   *
   * @param $type string тип конструктора ('basic' или 'panel')
   **/
  public function __construct($type = 'surface')
  {

    ### панель или нет?
    switch ($type) {

      case 'surface':
        ### путь шаблонам
        $this->componentsPath = router::deleteSlashes(router::$root . '/components/surface/');
        break;

      case 'panel':
        ### путь к шаблонам панели
        $this->componentsPath = router::deleteSlashes(router::$root . '/components/panel/');
        break;

    }

    ### путь к каркасам
    $this->carcassPath = router::deleteSlashes($this->componentsPath . '/carcass/');

  }


  /**
   * Получить контент из файла каркаса
   *
   * @param $carcassName string название необходимого каркаса
   * @return string
   **/
  private function getCarcassContent($carcassName)
  {

    $carcassFile = router::folderToFile(
      router::deleteSlashes(
        $this->carcassPath . '/' . $carcassName . '/'
      )
    );

    return
      $this->includeFileContent($carcassFile);

  }


  /**
   * Получить контент из файла сектора
   *
   * @param $sectorName string название сектора
   * @param $chosenSector string название выбранного сектора
   * @return string
   **/
  private function getSectorContent($sectorName, $chosenSector)
  {

    $sectorFile = router::folderToFile(
      router::deleteSlashes(
        $this->componentsPath . '/' . $sectorName . '/' . $chosenSector . '/'
      )
    );

    return
      $this->includeFileContent($sectorFile);

  }


  /**
   * Выводим данные файла через буфер
   *
   * Буфер необходим для отображения переменных,
   * внедренных в построенный шаблон
   *
   * Функцией extract внедряем переменные,
   * буфер и инклюд заберет только нужные
   *
   * @param $filePath string путь к файлу, контент которого мы выводим
   * @return string
   **/
  private function includeFileContent($filePath)
  {

    ### внедряем переменные
    extract($this->variables);

    ### включаем буфер
    ob_start();

    ### заносим запрошенный файл в буфер
    include($filePath);

    ### получаем содержимое файла из буфера и очищаем буфер
    return ob_get_clean();

  }


  /**
   * Внедрить переменную в шаблон
   *
   * @param $variables array внедряемая переменная
   * @return void
   **/
  public function injectVariables($variables)
  {

    $this->variables = array_merge($this->variables, $variables);

  }


  /**
   * Регистрируем каркас страницы
   *
   * @param $carcassName string название каркаса
   * @throws Exception выдает ошибку, если выбранный каркас уже существует
   * @return self
   **/
  public function registerCarcass($carcassName)
  {

    ### проверяем наличие существующего каркаса
    if (!empty($this->chosenCarcass))
      ### если существует - выдаем ошибку
      throw new Exception('Каркасом уже выбран ' . $this->chosenCarcass);

    ### проверяем наличие файла и записываем выбранный каркас в свойство
    if ($this->isComponentExist($carcassName, 'carcass'))
      $this->chosenCarcass = $carcassName;

    ### парсим каркас на наличие секторов
    $this->parseCarcass($this->chosenCarcass);

    return $this;

  }


  /**
   * Парсим сектора каркаса и регистрируем распарсеные сектора
   *
   * @param $carcassName string название каркаса, который будем парсить
   * @return void
   **/
  private function parseCarcass($carcassName)
  {

    ### получаем контент каркаса, необходимый для поиска переменных
    $carcassContent = $this->getCarcassContent($carcassName);

    ### инициализируем массив, в который войдут переменные и названия секторов
    $array = [];

    ### ищем сектора в каркасе
    preg_match_all('%@@@ (.*) @@@%', $carcassContent, $array);

    ### внедряем в свойство класса названия найденных секторов в каркасе
    $this->registerSectors($array);

  }


  /**
   * Регистрируем сектора, которые распарсили из каркаса
   *
   * @param $parseredSectorsArray array массив распарсенных секторов
   * @return void
   **/
  private function registerSectors($parseredSectorsArray)
  {

    ### берем второй элемент массива ([1]), потому что в первом у нас находятся
    ### найденные переменные (@@@ header @@@), а во втором - их названия (header)
    $sectorNames = $parseredSectorsArray[1];

    ### вносим названия секторов в массив с секторами каркаса
    foreach ($sectorNames as $column => $value) {
      $this->carcassSectors[$value] = '';
    }

  }


  /**
   * Заполняем сектор
   *
   * @param $sectorName string выбранный сектор
   * @param $componentName mixed каким компонентом заполняем
   * @return self
   **/
  public function fillSector($sectorName, $componentName)
  {

    if (
      ### проверяем, не заполнен ли сектор
      $this->isFillable($sectorName) &&
      ### проверяем, существует ли файл сектора
      $this->isComponentExist($componentName, $sectorName)
    )
      $this->carcassSectors[$sectorName] = $componentName;

    return $this;

  }


  /**
   * Заполняем сектора
   *
   * @param $sectorsArray array внедряемые сектора
   * @return self
   **/
  public function fillSectors($sectorsArray)
  {

    foreach ($sectorsArray as $sectorName => $componentName) {

      $this->fillSector($sectorName, $componentName);

    }

    return $this;

  }


  /**
   * Проверяем, можно ли заполнить сектор
   *
   * @param $sectorName string проверяемый сектор
   * @throws Exception выдает ошибку, если сектор не существует или уже заполнен
   * @return boolean
   **/
  private function isFillable($sectorName)
  {

    if (
      ### проверяем существование сектора
      isset($this->carcassSectors[$sectorName]) &&
      ### проверяем заполненность сектора
      empty($this->carcassSectors[$sectorName])
    ) return true;

    else throw new Exception('Сектор ' . $sectorName . ' не существует или уже заполнен.');

  }


  /**
   * Проверяет наличие компонента
   *
   * @param $component string путь к компонента (начинается от папки components)
   * @param $componentDirectory string директория компонента
   * @throws Exception выдает ошибку, если включаемого компонента не существует
   * @return boolean
   **/
  private function isComponentExist($component, $componentDirectory)
  {

    $fullPath =
      router::folderToFile(
        router::deleteSlashes(
          $this->componentsPath . '/' . $componentDirectory . '/' . $component . '/'
        )
      );

    if (file_exists($fullPath)) return true;

    else throw new Exception(
      'Включаемого компонента ' . $component . ' в директории ' . $componentDirectory . ' не существует.'
    );

  }


  /**
   * Регистрируем базовый шаблон
   *
   * @param $name string название базового каркаса
   * @param $chosenCarcass string выбранный каркас
   * @param $sectorsArray array массив с секторами
   * @return self
   **/
  public function registerBasicTemplate($name, $chosenCarcass, $sectorsArray)
  {

    ### заносим базовый шаблон (название каркаса и значение секторов)
    ### в массив с базовыми шаблонами
    $this->basicTemplates[$name][$chosenCarcass] = $sectorsArray;

    return $this;

  }


  /**
   * Внедряем базовый шаблон как выбранный для будущего вывода
   *
   * @param $basicTemplateName string название базового шаблона
   * @return self
   **/
  public function includeBasicTemplate($basicTemplateName)
  {

    ### извлекаем название каркаса выбранного базового шаблона и регистрируем его
    $this->registerCarcass(
      key($this->basicTemplates[$basicTemplateName])
    );

    ### заполняем сектора
    $this->fillSectors(
      array_shift($this->basicTemplates[$basicTemplateName])
    );

    return $this;

  }


  /**
   * Выводим шаблон
   *
   * @throws Exception выдает ошибку, если есть незаполненные сектора
   * @return void
   **/
  public function print()
  {

    ### проверяем, не ли незаполненных секторов
    ### если есть, выдаем ошибку
    if ($this->someSectorsAreEmpty())
      throw new Exception('Есть незаполненные сектора. Заполните их, либо воспользуйтесь методом ignoreEmptySectors().');

    ### получаем содержание файла каркаса
    $templateContent = $this->getCarcassContent($this->chosenCarcass);

    ### заполняем сектора каркаса
    foreach ($this->carcassSectors as $sectorName => $chosenSector) {

      ### если сектор отмечен как незаполненный, то оставляем вместо него пустую строку
      if ($chosenSector === '@@@ empty @@@')
        $chosenSectorContent = '';
      else
        ### в противном случае заполняем сектор выбранным файлом сектора
        $chosenSectorContent = $this->getSectorContent($sectorName, $chosenSector);


      ### записываем сектор
      $templateContent =
        preg_replace('%@@@ ' . $sectorName . ' @@@%', $chosenSectorContent, $templateContent);

    }

    ### выводим шаблон
    echo $templateContent;

  }


  /**
   * Проверяем, не ли незаполненных секторов
   *
   * @return boolean
   **/
  public function someSectorsAreEmpty()
  {

    foreach ($this->carcassSectors as $sector) {

      if (empty($sector)) return true;

    }

    return false;

  }


  /**
   * Заполняем пустые сектора переменной @@@ empty @@@
   *
   * В дальнейшем, она будет заменена на пустую строку
   *
   * @return self
   **/
  public function ignoreEmptySectors()
  {

    foreach ($this->carcassSectors as $sectorName => $sectorValue) {

      if (empty($sectorValue)) $this->carcassSectors[$sectorName] = '@@@ empty @@@';

    }

    return $this;

  }


  /**
   * Метод быстрого вывода базового шаблона и одновременного заполнения секторов
   *
   * @param $basicTemplateName string название базового шаблона для вывода
   * @param $chosenSectorsArray array массив с выбранными секторами
   * @param $ignoreEmptySectors boolean игнорируем или нет пустые сектора
   * @return void
   **/
  public function printBasicTemplate($basicTemplateName, $chosenSectorsArray, $ignoreEmptySectors = false)
  {

    ### внедряем базовый шаблон, регистрируем и заполняем сектора
    $this->includeBasicTemplate($basicTemplateName)->fillSectors($chosenSectorsArray);

    ### игнорируем, если нужно, пустые сектора
    if ($ignoreEmptySectors) $this->ignoreEmptySectors();

    ### выводим базовый шаблон
    $this->print();

  }


}