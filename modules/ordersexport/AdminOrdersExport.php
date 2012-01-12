<?php
class AdminOrdersExport extends AdminTab
{
  public function __construct()
  {
   global $cookie;
   
   $this->table     = 'order';
	 $this->className = 'Order';
	 
	 $this->sql       =

   parent::__construct();
  }
  
  public function display()
  {
    global $cookie;

    $this->postProcess();
    echo $this->displayForm();
  }

  public function displayForm()
  {
    $output  = '';
    $output .= '<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">';
    $output .= '  <fieldset>';
    $output .= '    <legend>' . $this->l('Orders export') . '</legend>';
    $output .= '    <label class="clear">' . $this->l('Choose the export format:') . '</label>';
    $output .= '    <p class="margin-form">';
    $output .= '      <input type="radio" name="export_format" value="csv" id="format_csv" />';
    $output .= '      <label for="format_csv" class="t">' . $this->l('CSV') . '</label>';
    $output .= '      <br />';
    $output .= '      <input type="radio" name="export_format" value="xls" id="format_xls" />';
    $output .= '      <label for="format_xls" class="t">' . $this->l('XLS') . '</label>';
    $output .= '      <p class="margin-form">';
    $output .= '        <input type="submit" value="' . $this->l('Submit') . '" name="submitExportFormat" class="button">';
    $output .= '      </p>';
    $output .= '    </p>';
    $output .= '</form>';
    
    return $output;
  }
  
  public function postProcess()
  {
    if (Tools::isSubmit('submitExportFormat'))
		{
		  
	  }
  }
}
?>