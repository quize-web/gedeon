<?
/* @var $carcasses array */
?>

<form method="post" class="add_element">

  <label><span>Тип</span>
    <select name="type">
      <option value="directory">Директория</option>
      <option value="document">Документ</option>
      <option value="funnel">Фильтр</option>
    </select>
  </label>

  <label><span>Каркас</span>
    <select name="carcass">
      <? foreach ($carcasses as $carcass): ?>
        <option value="<?=$carcass?>"><?=$carcass?></option>
      <? endforeach; ?>
    </select>
  </label>

  <label><span>Ключ</span>
    <input type="text" name="key">
  </label>

  <input type="hidden" name="parent" value="0">

  <input type="submit" value="Добавить">

</form>