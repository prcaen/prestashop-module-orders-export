<?php
include_once('ExportToCSV.php');
class AdminOrdersExport extends AdminTab
{
  private $currencySign;
  public function __construct()
  {
   global $cookie;
   
   $this->table     = 'orders';
	 $this->className = 'Order';
	 
	 parent::__construct();
	 
	 $this->file      = 'export_orders_' . date('d-m-Y');
	 $this->dir       = dirname(__FILE__) . '/exports/';
	 $this->fileCSV   = $this->file . '.csv';
	 $this->fileXLS   = $this->file . '.xls';
   $this->dirCSV    = $this->dir . 'csv/';
   $this->dirXLS    = $this->dir . 'xls/';
   
   $this->tpl       = dirname(__FILE__) . '/AdminOrdersExport.tpl';
   
   $currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
   $this->_currencySign = $currency->getSign();

	 $this->sql = array(
	   'select' => array(
	     'o' => array(
	        'n' => 'orders',
	        'f' => array(
	        array(
	          'n' => 'id_order',
	          'l' => 'Order reference',
	          'supplier' => false,
	          'carrier'  => true
	        ),
	        array(
	          'n' => 'date_add',
	          'l' => 'Date',
	          'supplier' => false,
	          'carrier'  => true
	        )
	      )),
	      'm'  => array(
	        'n' => 'manufacturer',
	        'f' => array(
	          array(
	          'n' => 'name',
	          'l' => 'Manufacturer',
	          'supplier' => true,
	          'carrier'  => true
	        ))
	      ),
	      'od' => array(
	        'n' => 'order_detail',
	        'f' => array(
	          array(
	          'n' => 'product_name',
	          'l' => 'Product name',
	          'supplier' => true,
	          'carrier'  => true
	        ),
	        array(
	          'n' => 'product_quantity',
	          'l' => 'Quantity',
	          'supplier' => true,
	          'carrier'  => true
	        ),
	        array(
	          'n' => 'product_price',
	          'l' => 'ET unit cost' . ' (' . $this->_currencySign . ')',
	          'supplier' => false,
	          'carrier'  => true
	        ))
	      ),
	      'ads' => array(
	        'n' => 'address',
	        'f' => array(
	        array(
	          'n' => 'lastname',
	          'l' => 'Delivery lastname',
	          'supplier' => false,
	          'carrier'  => true
	        ),
	        array(
	          'n' => 'firstname',
	          'l' => 'Delivery firstname',
	          'supplier' => false,
	          'carrier'  => true
	        ),
	        array(
	          'n' => 'address1',
	          'l' => 'Delivery address',
	          'supplier' => false,
	          'carrier'  => true
	        ),
 	        array(
 	          'n' => 'postcode',
 	          'l' => 'Delivery zip code',
	          'supplier' => false,
	          'carrier'  => true
 	        ),
 	        array(
 	          'n' => 'city',
 	          'l' => 'Delivery city',
	          'supplier' => false,
	          'carrier'  => true
 	        ))
	      ),
	      'cld' => array(
	        'n' => 'country_lang',
	        'f' => array(
	          array(
	          'n' => 'name',
	          'l' => 'Delivery country',
	          'supplier' => false,
	          'carrier'  => true
	        ))
	      ),
	      'adi' => array(
	        'n' => 'address',
	        'f' => array(
	          array(
	          'n' => 'lastname',
	          'l' => 'Invoice lastname',
	          'supplier' => false,
	          'carrier'  => true
	        ),
	        array(
	          'n' => 'firstname',
	          'l' => 'Invoice firstname',
	          'supplier' => false,
	          'carrier'  => true
	        ),
	        array(
	          'n' => 'address1',
	          'l' => 'Invoice address',
	          'supplier' => false,
	          'carrier'  => true
	        ),
 	        array(
 	          'n' => 'postcode',
 	          'l' => 'Invoice zip code',
	          'supplier' => false,
	          'carrier'  => true
 	        ),
 	        array(
 	          'n' => 'city',
 	          'l' => 'Invoice city',
	          'supplier' => false,
	          'carrier'  => true
 	        ))
	      ),
	      'cli' => array(
	        'n' => 'country_lang',
	        'f' => array(
	          array(
	          'n' => 'name',
	          'l' => 'Invoice country',
	          'supplier' => false,
	          'carrier'  => true
	        ))
	      ),
	      'u' => array(
	       'n' => 'customer',
	       'f' => array(
	         array(
	         'n' => 'email',
	         'l' => 'Email',
	         'supplier' => false,
	         'carrier'  => true
	       )
	       )
	      )
	    ),
	    'join' => array(
	      'od' => array(
	       'n' => 'order_detail',
	       'j' => 'o',
	        1  => 'id_order',
	        2  => 'id_order'
	      ),
	      'p' => array(
	       'n' => 'product',
	       'j' => 'od',
	        1  => 'id_product',
	        2  => 'product_id',
	      ),
	      'm' => array(
	       'n' => 'manufacturer',
	       'j' => 'p',
	        1  => 'id_manufacturer',
	        2  => 'id_manufacturer',
	      ),
	      'u' => array(
	       'n' => 'customer',
	       'j' => 'o',
	        1  => 'id_customer',
	        2  => 'id_customer',
	      ),
	      'ads' => array(
	       'n' => 'address',
	       'j' => 'o',
	        1  => 'id_address',
	        2  => 'id_address_delivery',
	      ),
	      'adi' => array(
	       'n' => 'address',
	       'j' => 'o',
	        1  => 'id_address',
	        2  => 'id_address_invoice',
	      ),
	      'cli' => array(
	       'n' => 'country_lang',
	       'j' => 'adi',
	        1  => 'id_country',
	        2  => 'id_country',
	      ),
	      'cld' => array(
	       'n' => 'country_lang',
	       'j' => 'ads',
	        1  => 'id_country',
	        2  => 'id_country',
	      ),
	      'a' => array(
	       'n' => 'attribute',
	       'j' => 'od',
	        1  => 'id_attribute',
	        2  => 'product_attribute_id',
	      ),
	      'al' => array(
	       'n' => 'attribute_lang',
	       'j' => 'a',
	        1  => 'id_attribute',
	        2  => 'id_attribute',
	      )
	    ),
	    'from'  => '`' . _DB_PREFIX_ . $this->table . '` o',
	    'where' => array(
	     '1' => '1'
	    )
	  );
  }
  
  public function display()
  {
    global $smarty, $cookie, $currentIndex;
    
    // Date
    $dates = array();
    $d = $this->_getDateInstall();
    while($d < time())
    {
      $dates[] = array(
        'timestamp' => $d,
        'display'   => Tools::displayDate(date('Y-m-d H:i:s', $d), (int)$cookie->id_lang, false)
      );

      $d += 7 * 24 * 60 * 60;
    }
    
    $this->_postProcess();
    $smarty->assign('request_uri', Tools::safeOutput($_SERVER['REQUEST_URI']));
    $smarty->assign('dates', $dates);
    
    echo $smarty->display($this->tpl);
  }

  private function _postProcess()
  {
    global $smarty, $cookie;

    if (Tools::isSubmit('submitExportFormat'))
		{
		  $fields = array();
		  $joins  = array();
		  $where  = array();
		  $titles = array();

      $currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
    
		  foreach($this->sql AS $type => $data)
		  {
		    if($type == 'select')
		    {
		      foreach($data AS $prefix => $table)
		      {
		        foreach($table AS $col => $value)
		        {
		          if($col == 'f')
		          {
		            foreach($value as $val)
		            {
		              $titles[] = $val['l'];
		              if($val['n'] == 'product_price')
		              {
		                $titles[] = $this->l('ATI unit cost') . ' (' . $this->_currencySign . ')';
		                $titles[] = $this->l('ET cost') . ' (' . $this->_currencySign . ')';
		                $titles[] = $this->l('ATI cost') . ' (' . $this->_currencySign . ')';
		              }
		              $fields[] = $prefix . '.' . '`' . $val['n'] . '` AS `' . $val['l'] . '`';
		              if($val['n'] == 'product_price')
		              {
                    $fields[] = '(od.`product_price` * ((100 + (od.`tax_rate`))/100)) AS `'. $this->l('ATI unit cost') . ' (' . $this->_currencySign . ')' . '`';
                    $fields[] = '(od.`product_price` * od.`product_quantity`) AS `' . $this->l('ET cost') . ' (' . $this->_currencySign . ')' . '`';
                    $fields[] = '(od.`product_price` * ((100 + (od.`tax_rate`))/100) * od.`product_quantity`) AS `' . $this->l('ATI cost') . ' (' . $this->_currencySign . ')' . '`';
                  }
	              }
		          }
		        }
		      }
		    }
		    elseif($type == 'join')
		    {
		      foreach($data AS $prefix => $join)
		        $joins[] = 'LEFT JOIN `' . _DB_PREFIX_ . $join['n'] . '` ' . $prefix . ' ON (' . $prefix . '.`' . $join[1] . '` = '. $join['j'] . '.`' . $join[2] . '`' . (substr($join['n'], -5) == '_lang' ? ' AND ' . $prefix . '.`id_lang` = ' . (int)$cookie->id_lang : '') . ')';
		      
		      $joins[] = 'LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)Country::getDefaultCountryId().' AND tr.`id_state` = 0)';
		      $joins[] = 'LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)';
		    }
		    elseif($type == 'where')
		    {
		      foreach($data AS $key => $val)
		        $where[] = $key . ' = ' . $val;
		    }
		  }

		  $fields = implode(', ', $fields);
		  $joins  = implode(' ', $joins);
		  $where  = implode(' AND ', $where);
		  $from   = $this->sql['from'];

		  $sql = "SELECT " . $fields . " FROM " . $from . " " . $joins . " WHERE " . $where;
		  
		  //echo $sql;
		  $results = Db::getInstance()->ExecuteS($sql);
      //print_r($titles);
      // --- CSV ---
      if(Tools::getValue('export_format') == 'csv')
		  {
		    $results = (is_array($results) ? array_merge(array($titles), $results) : $titles);

		    $exportCSV = new ExportToCSV($this->fileCSV, $this->dirCSV, ',', '"');
		    
		    if(!$exportCSV->open())
		    {
          $smarty->assign('errors', $this->l('Error: cannot write').' '. $this->fileCSV);
          return false;
        }
		    foreach($results AS $result)
		      $exportCSV->setContent($result);

		    if($exportCSV->output())
		    {
		      $smarty->assign('confirm', $this->l('The CSV file has been successfully exported'));
		      //Tools::redirect('modules/ordersexport/exports/csv/' . $this->fileCSV);
		      
		      return true; 
		    }
		    else
		    {
          $smarty->assign('errors', $this->l('Error: An error as occured').' '. $this->fileCSV);
          return false;
        }
		  }
		  elseif(Tools::getValue('export_format') == 'xls')
		  {
		    $content  = '';
		    $content .= '<table>';
		    $content .= ' <tr>';
		    foreach($titles as $title)
		      $content .= '   <th>' . $title . '</th>';
		    $content .= ' </tr>';
		    $content .= ' <tr>';
		    foreach($titles as $title)
		      $content .= '   <td>' . $title . '</td>';
		    $content .= ' </tr>';
		    $content .= '</table>';

		    fwrite($file, $content);
		  }
		  
		  // --- XLS --- 
	  }
  }
  
  private function _getDateInstall()
  {
    $sql = "SELECT date_add FROM `" . _DB_PREFIX_ . "configuration` WHERE name = 'PS_SHOP_NAME'";
    
    return strtotime(Db::getInstance()->getValue($sql));
  }
}
?>