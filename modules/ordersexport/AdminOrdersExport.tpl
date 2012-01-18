{if isset($errors)}
<div class="error"><img src="../img/admin/error2.png" alt="" /> {$errors}</div>
{/if}
{if isset($confirm)}
<div class="confirm"><img src="../img/admin/confirm2.png" alt="" /> {$confirm}</div>
{/if}
<form action="{$request_uri}" method="post">
  <fieldset>
    <legend>{l s='Orders export' mod='ordersexport'}</legend>    
    <label class="clear">{l s='Choose the export format:' mod='ordersexport'}</label>
    <p class="margin-form">
      <input type="radio" name="export_format" value="csv" id="format_csv" checked="checked" />
      <label for="format_csv" class="t">{l s='CSV' mod='ordersexport'}</label>
      <br />
      <input type="radio" name="export_format" value="xls" id="format_xls" />
      <label for="format_xls" class="t">{l s='XLS' mod='ordersexport'}</label>
    </p>
    <label for="date" class="clear">{l s='Choose your week:' mod='ordersexport'}</label>
    <p class="margin-form">
      <select name="date" id="date">
        {foreach from=$dates item=date}
        <option value="{$date['timestamp']}">{$date['display']}</option>
        {/foreach}
      </select>
    </p>
    <p class="margin-form">
      <input type="radio" name="export_type" value="supplier" id="type_supplier" checked="checked" />
      <label for="type_supplier" class="t">{l s='Supplier' mod='ordersexport'}</label>
      <br />
      <input type="radio" name="export_type" value="carrier" id="type_carrier" />
      <label for="type_carrier" class="t">{l s='Carrier' mod='ordersexport'}</label>
      <br />
      <input type="radio" name="export_type" value="admin" id="type_admin" />
      <label for="type_admin" class="t">{l s='Admin' mod='ordersexport'}</label>
    </p>
    <p class="margin-form">
      <input type="submit" value="{l s='Download' mod='ordersexport'}" name="submitExportFormat" class="button" />
    </p>
  </fieldset>
</form>