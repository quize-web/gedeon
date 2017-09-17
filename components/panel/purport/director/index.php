<?
/* @var $elements array */
/* @var $panelURL string */
/* @var $URI string */
?>

<table class="table_director">


  <tr>
    <th></th>
    <th>ключ</th>
    <th>таблица переменных</th>
    <th></th>
    <th class="table_director__icon table_director__button">
      <a href="/panel/director/add/"><img src="/images/add.svg"></a>
    </th>
  </tr>


  <? foreach ($elements as $element): ?>
    <tr>

      <td class="table_director__icon"><img src="/images/<?= $element['type'] ?>.svg" alt="type"></td>

      <td><a href="<?= $panelURL ?>/director/<?= $element['id'] ?>/"><?= $element['key'] ?></a></td>

      <td><?= $element['variableTable'] ?></td>

      <td class="table_director__icon table_director__button">
        <a href="<?= $panelURL ?>/director/edit/<?= $element['id'] ?>/"><img src="/images/settings.svg"></a>
      </td>

      <td class="table_director__icon table_director__button">
        <form action="<?= $panelURL ?>/director/delete/" method="POST">
          <input type="hidden" name="ID" value="<?= $element['id'] ?>">
          <input type="hidden" name="originURI" value="<?= $URI ?>">
          <input type="image" src="/images/delete.svg">
        </form>
      </td>

    </tr>
  <? endforeach; ?>


</table>