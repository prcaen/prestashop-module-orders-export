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
	 
	 $this->file      = 'export_orders_';
	 $this->dir       = dirname(__FILE__) . '/exports/';
	 
	 $this->fileXLS   = $this->file . '.xls';
   $this->dirCSV    = $this->dir . 'csv/';
   $this->dirXLS    = $this->dir . 'xls/';
   
   $this->tpl       = dirname(__FILE__) . '/AdminOrdersExport.tpl';
   
   $this->currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
   $this->_currencySign = $this->currency->getSign();

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
    //echo $this->_getResultAdmin();
    $this->_postProcess();
    $smarty->assign('request_uri', Tools::safeOutput($_SERVER['REQUEST_URI']));
    $smarty->assign('dates', $dates);
    $smarty->assign('order_states', $this->_getOrderStates());
    
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

      $dateS     = (int)Tools::getValue('date');
      $dateE     = $dateS + (7 * 24 * 60 * 60);
      $dateStart = date('Y-m-d H:i:s', $dateS);
      $dateEnd   = date('Y-m-d H:i:s', $dateE);
      
      if(Tools::getValue('export_type') == 'admin')
		  {
		    $results = $this->_getResultAdmin($dateStart, $dateEnd, (int)Tools::getValue('order_state'));
		    $titles  = $this->_getTitlesAdmin($results);
		    $type    = 'admin';
      }
      elseif(Tools::getValue('export_type') == 'supplier')
      {
		    $results = $this->_getResultSupplier($dateStart, $dateEnd, (int)Tools::getValue('order_state'));
		    $titles  = $this->_getTitlesAdmin();
		    $type    = 'supplier';
      }
      elseif(Tools::getValue('export_type') == 'carrier')
      {
		    $results = $this->_getResultCarrier($dateStart, $dateEnd, (int)Tools::getValue('order_state'));
		    $titles  = $this->_getTitlesAdmin();
		    $type    = 'carrier';
      }
      else
      {
        // error
      }

      $dir = $type . '/';
      // --- CSV ---
      if(Tools::getValue('export_format') == 'csv')
		  {
		    $this->fileCSV = $this->file . $type . '_' . date('Y-m-d', $dateS) . '_' . date('Y-m-d', $dateE) . '.csv';

		    $results = (is_array($results) ? array_merge(array($titles), $results) : $titles);
		    $exportCSV = new ExportToCSV($this->fileCSV, $this->dirCSV . $dir, ',', '"');
		    
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
		      Tools::redirect('modules/ordersexport/exports/csv/' . $dir . $this->fileCSV);
		      
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
    
    $dateInstall = Db::getInstance()->getValue($sql);
    return strtotime('midnight', strtotime($dateInstall));
  }
  
  private function _getOrderStates()
  {
    global $cookie;
    $sql = "SELECT osl.`id_order_state`, osl.`name` FROM `order_state_lang` osl WHERE osl.`id_lang` = " . (int)$cookie->id_lang;
    
    $results = Db::getInstance()->ExecuteS($sql);
    array_unshift($results, array('id_order_state' => 0, 'name' => $this->l('All')));

    return $results;
  }
  
  private function _getGroupAttributes()
  {
    global $cookie;

    $sql = "SELECT agl.`id_attribute_group`, agl.`name` FROM `" . _DB_PREFIX_ . "attribute_group_lang` agl WHERE agl.`id_lang` = " . (int)$cookie->id_lang;
    
    return Db::getInstance()->ExecuteS($sql);
  }
  
  private function _getResultAdmin($dateStart, $dateEnd, $orderState)
  {
    global $cookie;

    if($orderState != 0)
      $state = " AND oh.`id_order_state` = " . $orderState;
    else
      $state = "";
    $sql = "SELECT SQL_CALC_FOUND_ROWS o.`id_order` AS `o.id_order`, 
                   (SELECT COUNT(oddd.`id_order_detail`) FROM `" . _DB_PREFIX_ . "order_detail` oddd WHERE oddd.`id_order_detail` >= od.`id_order_detail` AND oddd.`id_order` = od.`id_order`) AS `nb_product`,
                   (SELECT COUNT(odd.`id_order`) FROM `" . _DB_PREFIX_ . "order_detail` odd WHERE odd.`id_order` = od.`id_order`) AS `nb_total`,
                   o.`date_add` AS `o.date_add`, 
                   m.`name` AS `m.name`, 
                   od.`product_name` AS `od.product_name`, 
                   (SELECT GROUP_CONCAT(agl.`id_attribute_group` ORDER BY agl.`id_attribute_group` ASC SEPARATOR '#')
                    FROM `" . _DB_PREFIX_ . "product_attribute` pa
                    LEFT JOIN `" . _DB_PREFIX_ . "product_attribute_combination` pac ON (pac.`id_product_attribute` = pa.`id_product_attribute`) 
                    LEFT JOIN `" . _DB_PREFIX_ . "attribute` a ON (a.`id_attribute` = pac.`id_attribute`) 
                    LEFT JOIN `" . _DB_PREFIX_ . "attribute_group_lang` agl ON (agl.`id_attribute_group` = a.`id_attribute_group` AND agl.`id_lang` = " . (int)$cookie->id_lang . ")
                    WHERE pa.`id_product_attribute` = od.`product_attribute_id`) AS `agl.id_attribute_group`,
                   (SELECT GROUP_CONCAT(al.`name` ORDER BY al.`name` ASC SEPARATOR '#')
                    FROM `" . _DB_PREFIX_ . "product_attribute` pa
                    LEFT JOIN `" . _DB_PREFIX_ . "product_attribute_combination` pac ON (pac.`id_product_attribute` = pa.`id_product_attribute`) 
                    LEFT JOIN `" . _DB_PREFIX_ . "attribute` a ON (a.`id_attribute` = pac.`id_attribute`) 
                    LEFT JOIN `" . _DB_PREFIX_ . "attribute_lang` al ON (al.`id_attribute` = a.`id_attribute` AND al.`id_lang` = " . (int)$cookie->id_lang . ") 
                    WHERE pa.`id_product_attribute` = od.`product_attribute_id`) AS `al.name`,
                   od.`product_quantity` AS `od.product_quantity`, 
                   od.`product_price` AS `et_unit_cost`, 
                   (od.`product_price` * ((100 + (od.`tax_rate`))/100)) AS `ati_unit_cost`, 
                   (od.`product_price` * od.`product_quantity`) AS `et_cost`, 
                   (od.`product_price` * ((100 + (od.`tax_rate`))/100) * od.`product_quantity`) AS `ati_cost`, 
                   ads.`lastname` AS `delivery_lastname`, 
                   ads.`firstname` AS `delivery_firstname`, 
                   CONCAT(ads.`address1`, ' ', ads.`address2`) AS `delivery_address`, 
                   ads.`postcode` AS `delivery_postcode`, 
                   ads.`city` AS `delivery_city`, 
                   cld.`name` AS `delivery_country`, 
                   adi.`lastname` AS `invoice_lastname`, 
                   adi.`firstname` AS `invoice_firstname`, 
                   CONCAT(adi.`address1`, ' ', adi.`address2`) AS `invoice_address`, 
                   adi.`postcode` AS `invoice_postcode`, 
                   adi.`city` AS `invoice_city`, 
                   cli.`name` AS `invoice_country`, 
                   u.`email` AS `u.email`,
                   osl.`name` AS `osl.name`
              FROM `" . _DB_PREFIX_ . "orders` o
              LEFT JOIN `" . _DB_PREFIX_ . "order_detail` od ON (od.`id_order` = o.`id_order`)
              LEFT JOIN `" . _DB_PREFIX_ . "product` p ON (p.`id_product` = od.`product_id`) 
              LEFT JOIN `" . _DB_PREFIX_ . "manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`) 
              LEFT JOIN `" . _DB_PREFIX_ . "customer` u ON (u.`id_customer` = o.`id_customer`) 
              LEFT JOIN `" . _DB_PREFIX_ . "address` ads ON (ads.`id_address` = o.`id_address_delivery`) 
              LEFT JOIN `" . _DB_PREFIX_ . "address` adi ON (adi.`id_address` = o.`id_address_invoice`) 
              LEFT JOIN `" . _DB_PREFIX_ . "country_lang` cli ON (cli.`id_country` = adi.`id_country` AND cli.`id_lang` = " . (int)$cookie->id_lang . ") 
              LEFT JOIN `" . _DB_PREFIX_ . "country_lang` cld ON (cld.`id_country` = ads.`id_country` AND cld.`id_lang` = " . (int)$cookie->id_lang . ") 
              
              LEFT JOIN `" . _DB_PREFIX_ . "tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = " . (int)Country::getDefaultCountryId() . " AND tr.`id_state` = 0) 
              LEFT JOIN `" . _DB_PREFIX_ . "tax` t ON (t.`id_tax` = tr.`id_tax`)
              LEFT JOIN `" . _DB_PREFIX_ . "order_history` oh ON (oh.`id_order` = o.`id_order`)
              LEFT JOIN `" . _DB_PREFIX_ . "order_state` os ON (os.`id_order_state` = oh.`id_order_state`)
              LEFT JOIN `" . _DB_PREFIX_ . "order_state_lang` osl ON (osl.`id_order_state` = oh.`id_order_state` AND osl.`id_lang` = " . (int)$cookie->id_lang . ") 
              WHERE o.date_add BETWEEN '" . $dateStart . "' AND '" . $dateEnd . "'" . $state . "
              ORDER BY o.`id_order` ASC, nb_product ASC";
    $results = Db::getInstance()->ExecuteS($sql);
    $datas   = array();

    foreach($results as $n => $result)
    {
      foreach($result as $key => $val)
      {
        if($key == 'et_unit_cost' || $key == 'ati_unit_cost' || $key == 'et_cost' || $key == 'ati_cost')
          $datas[$n][$key] = Tools::displayPrice($val, $this->currency, false);
        elseif($key == 'o.date_add')
          $datas[$n][$key] = Tools::displayDate($val, (int)$cookie->id_lang, false);
        elseif($key == 'nb_product')
          $datas[$n]['position'] = $val . '/' . $results[$n]['nb_total'];
        elseif($key == 'osl.name')
        {
          if($result['nb_product'] == 1)
            $datas[$n][$key] = $val;
        }
        elseif($key == 'al.name')
        {
          $datas[$n] = $this->_getFeatureArray($datas[$n]);

          if($result[$key] != '')
          {
            $features = explode('#', $val);
            $featuresIds = explode('#', $result['agl.id_attribute_group']);
            foreach ($features as $k => $feature)
              $datas[$n]['feature_' . $featuresIds[$k]] = $feature;
          }
        }
        elseif($key == 'nb_total' || $key == 'agl.id_attribute_group')
          continue;
        else
          $datas[$n][$key] = $val;
      }
    }

    return $datas;
  }
  
  private function _getResultCarrier($dateStart, $dateEnd, $orderState)
  {
    global $cookie;

    if($orderState != 0)
      $state = " AND oh.`id_order_state` = " . $orderState;
    else
      $state = "";
    $sql = "SELECT SQL_CALC_FOUND_ROWS o.`id_order` AS `o.id_order`, 
                   (SELECT COUNT(oddd.`id_order_detail`) FROM `" . _DB_PREFIX_ . "order_detail` oddd WHERE oddd.`id_order_detail` >= od.`id_order_detail` AND oddd.`id_order` = od.`id_order`) AS `nb_product`,
                   (SELECT COUNT(odd.`id_order`) FROM `" . _DB_PREFIX_ . "order_detail` odd WHERE odd.`id_order` = od.`id_order`) AS `nb_total`,
                   o.`date_add` AS `o.date_add`, 
                   m.`name` AS `m.name`, 
                   od.`product_name` AS `od.product_name`, 
                   (SELECT GROUP_CONCAT(agl.`id_attribute_group` ORDER BY agl.`id_attribute_group` ASC SEPARATOR '#')
                    FROM `" . _DB_PREFIX_ . "product_attribute` pa
                    LEFT JOIN `" . _DB_PREFIX_ . "product_attribute_combination` pac ON (pac.`id_product_attribute` = pa.`id_product_attribute`) 
                    LEFT JOIN `" . _DB_PREFIX_ . "attribute` a ON (a.`id_attribute` = pac.`id_attribute`) 
                    LEFT JOIN `" . _DB_PREFIX_ . "attribute_group_lang` agl ON (agl.`id_attribute_group` = a.`id_attribute_group` AND agl.`id_lang` = " . (int)$cookie->id_lang . ")
                    WHERE pa.`id_product_attribute` = od.`product_attribute_id`) AS `agl.id_attribute_group`,
                   (SELECT GROUP_CONCAT(al.`name` ORDER BY al.`name` ASC SEPARATOR '#')
                    FROM `" . _DB_PREFIX_ . "product_attribute` pa
                    LEFT JOIN `" . _DB_PREFIX_ . "product_attribute_combination` pac ON (pac.`id_product_attribute` = pa.`id_product_attribute`) 
                    LEFT JOIN `" . _DB_PREFIX_ . "attribute` a ON (a.`id_attribute` = pac.`id_attribute`) 
                    LEFT JOIN `" . _DB_PREFIX_ . "attribute_lang` al ON (al.`id_attribute` = a.`id_attribute` AND al.`id_lang` = " . (int)$cookie->id_lang . ") 
                    WHERE pa.`id_product_attribute` = od.`product_attribute_id`) AS `al.name`,
                   od.`product_quantity` AS `od.product_quantity`, 
                   od.`product_price` AS `et_unit_cost`, 
                   (od.`product_price` * ((100 + (od.`tax_rate`))/100)) AS `ati_unit_cost`, 
                   (od.`product_price` * od.`product_quantity`) AS `et_cost`, 
                   (od.`product_price` * ((100 + (od.`tax_rate`))/100) * od.`product_quantity`) AS `ati_cost`, 
                   ads.`lastname` AS `delivery_lastname`, 
                   ads.`firstname` AS `delivery_firstname`, 
                   CONCAT(ads.`address1`, ' ', ads.`address2`) AS `delivery_address`, 
                   ads.`postcode` AS `delivery_postcode`, 
                   ads.`city` AS `delivery_city`, 
                   cld.`name` AS `delivery_country`, 
                   adi.`lastname` AS `invoice_lastname`, 
                   adi.`firstname` AS `invoice_firstname`, 
                   CONCAT(adi.`address1`, ' ', adi.`address2`) AS `invoice_address`, 
                   adi.`postcode` AS `invoice_postcode`, 
                   adi.`city` AS `invoice_city`, 
                   cli.`name` AS `invoice_country`, 
                   u.`email` AS `u.email`,
                   osl.`name` AS `osl.name`
              FROM `" . _DB_PREFIX_ . "orders` o
              LEFT JOIN `" . _DB_PREFIX_ . "order_detail` od ON (od.`id_order` = o.`id_order`)
              LEFT JOIN `" . _DB_PREFIX_ . "product` p ON (p.`id_product` = od.`product_id`) 
              LEFT JOIN `" . _DB_PREFIX_ . "manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`) 
              LEFT JOIN `" . _DB_PREFIX_ . "customer` u ON (u.`id_customer` = o.`id_customer`) 
              LEFT JOIN `" . _DB_PREFIX_ . "address` ads ON (ads.`id_address` = o.`id_address_delivery`) 
              LEFT JOIN `" . _DB_PREFIX_ . "address` adi ON (adi.`id_address` = o.`id_address_invoice`) 
              LEFT JOIN `" . _DB_PREFIX_ . "country_lang` cli ON (cli.`id_country` = adi.`id_country` AND cli.`id_lang` = " . (int)$cookie->id_lang . ") 
              LEFT JOIN `" . _DB_PREFIX_ . "country_lang` cld ON (cld.`id_country` = ads.`id_country` AND cld.`id_lang` = " . (int)$cookie->id_lang . ") 
              
              LEFT JOIN `" . _DB_PREFIX_ . "tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = " . (int)Country::getDefaultCountryId() . " AND tr.`id_state` = 0) 
              LEFT JOIN `" . _DB_PREFIX_ . "tax` t ON (t.`id_tax` = tr.`id_tax`)
              LEFT JOIN `" . _DB_PREFIX_ . "order_history` oh ON (oh.`id_order` = o.`id_order`)
              LEFT JOIN `" . _DB_PREFIX_ . "order_state` os ON (os.`id_order_state` = oh.`id_order_state`)
              LEFT JOIN `" . _DB_PREFIX_ . "order_state_lang` osl ON (osl.`id_order_state` = oh.`id_order_state` AND osl.`id_lang` = " . (int)$cookie->id_lang . ") 
              WHERE o.date_add BETWEEN '" . $dateStart . "' AND '" . $dateEnd . "'" . $state . "
              ORDER BY o.`id_order` ASC, nb_product ASC";
    $results = Db::getInstance()->ExecuteS($sql);
    $datas   = array();

    foreach($results as $n => $result)
    {
      foreach($result as $key => $val)
      {
        if($key == 'et_unit_cost' || $key == 'ati_unit_cost' || $key == 'et_cost' || $key == 'ati_cost')
          $datas[$n][$key] = Tools::displayPrice($val, $this->currency, false);
        elseif($key == 'o.date_add')
          $datas[$n][$key] = Tools::displayDate($val, (int)$cookie->id_lang, false);
        elseif($key == 'nb_product')
          $datas[$n]['position'] = $val . '/' . $results[$n]['nb_total'];
        elseif($key == 'osl.name')
        {
          if($result['nb_product'] == 1)
            $datas[$n][$key] = $val;
        }
        elseif($key == 'al.name')
        {
          $datas[$n] = $this->_getFeatureArray($datas[$n]);

          if($result[$key] != '')
          {
            $features = explode('#', $val);
            $featuresIds = explode('#', $result['agl.id_attribute_group']);
            foreach ($features as $k => $feature)
              $datas[$n]['feature_' . $featuresIds[$k]] = $feature;
          }
        }
        elseif($key == 'nb_total' || $key == 'agl.id_attribute_group')
          continue;
        else
          $datas[$n][$key] = $val;
      }
    }

    return $datas;
  }
  
  private function _getResultSupplier($dateStart, $dateEnd, $orderState)
  {
    global $cookie;

    if($orderState != 0)
      $state = " AND oh.`id_order_state` = " . $orderState;
    else
      $state = "";
    $sql = "SELECT SQL_CALC_FOUND_ROWS o.`id_order` AS `o.id_order`, 
                   (SELECT COUNT(oddd.`id_order_detail`) FROM `" . _DB_PREFIX_ . "order_detail` oddd WHERE oddd.`id_order_detail` >= od.`id_order_detail` AND oddd.`id_order` = od.`id_order`) AS `nb_product`,
                   (SELECT COUNT(odd.`id_order`) FROM `" . _DB_PREFIX_ . "order_detail` odd WHERE odd.`id_order` = od.`id_order`) AS `nb_total`,
                   o.`date_add` AS `o.date_add`, 
                   m.`name` AS `m.name`, 
                   od.`product_name` AS `od.product_name`, 
                   (SELECT GROUP_CONCAT(agl.`id_attribute_group` ORDER BY agl.`id_attribute_group` ASC SEPARATOR '#')
                    FROM `" . _DB_PREFIX_ . "product_attribute` pa
                    LEFT JOIN `" . _DB_PREFIX_ . "product_attribute_combination` pac ON (pac.`id_product_attribute` = pa.`id_product_attribute`) 
                    LEFT JOIN `" . _DB_PREFIX_ . "attribute` a ON (a.`id_attribute` = pac.`id_attribute`) 
                    LEFT JOIN `" . _DB_PREFIX_ . "attribute_group_lang` agl ON (agl.`id_attribute_group` = a.`id_attribute_group` AND agl.`id_lang` = " . (int)$cookie->id_lang . ")
                    WHERE pa.`id_product_attribute` = od.`product_attribute_id`) AS `agl.id_attribute_group`,
                   (SELECT GROUP_CONCAT(al.`name` ORDER BY al.`name` ASC SEPARATOR '#')
                    FROM `" . _DB_PREFIX_ . "product_attribute` pa
                    LEFT JOIN `" . _DB_PREFIX_ . "product_attribute_combination` pac ON (pac.`id_product_attribute` = pa.`id_product_attribute`) 
                    LEFT JOIN `" . _DB_PREFIX_ . "attribute` a ON (a.`id_attribute` = pac.`id_attribute`) 
                    LEFT JOIN `" . _DB_PREFIX_ . "attribute_lang` al ON (al.`id_attribute` = a.`id_attribute` AND al.`id_lang` = " . (int)$cookie->id_lang . ") 
                    WHERE pa.`id_product_attribute` = od.`product_attribute_id`) AS `al.name`,
                   od.`product_quantity` AS `od.product_quantity`, 
                   od.`product_price` AS `et_unit_cost`, 
                   (od.`product_price` * ((100 + (od.`tax_rate`))/100)) AS `ati_unit_cost`, 
                   (od.`product_price` * od.`product_quantity`) AS `et_cost`, 
                   (od.`product_price` * ((100 + (od.`tax_rate`))/100) * od.`product_quantity`) AS `ati_cost`, 
                   ads.`lastname` AS `delivery_lastname`, 
                   ads.`firstname` AS `delivery_firstname`, 
                   CONCAT(ads.`address1`, ' ', ads.`address2`) AS `delivery_address`, 
                   ads.`postcode` AS `delivery_postcode`, 
                   ads.`city` AS `delivery_city`, 
                   cld.`name` AS `delivery_country`, 
                   adi.`lastname` AS `invoice_lastname`, 
                   adi.`firstname` AS `invoice_firstname`, 
                   CONCAT(adi.`address1`, ' ', adi.`address2`) AS `invoice_address`, 
                   adi.`postcode` AS `invoice_postcode`, 
                   adi.`city` AS `invoice_city`, 
                   cli.`name` AS `invoice_country`, 
                   u.`email` AS `u.email`,
                   osl.`name` AS `osl.name`
              FROM `" . _DB_PREFIX_ . "orders` o
              LEFT JOIN `" . _DB_PREFIX_ . "order_detail` od ON (od.`id_order` = o.`id_order`)
              LEFT JOIN `" . _DB_PREFIX_ . "product` p ON (p.`id_product` = od.`product_id`) 
              LEFT JOIN `" . _DB_PREFIX_ . "manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`) 
              LEFT JOIN `" . _DB_PREFIX_ . "customer` u ON (u.`id_customer` = o.`id_customer`) 
              LEFT JOIN `" . _DB_PREFIX_ . "address` ads ON (ads.`id_address` = o.`id_address_delivery`) 
              LEFT JOIN `" . _DB_PREFIX_ . "address` adi ON (adi.`id_address` = o.`id_address_invoice`) 
              LEFT JOIN `" . _DB_PREFIX_ . "country_lang` cli ON (cli.`id_country` = adi.`id_country` AND cli.`id_lang` = " . (int)$cookie->id_lang . ") 
              LEFT JOIN `" . _DB_PREFIX_ . "country_lang` cld ON (cld.`id_country` = ads.`id_country` AND cld.`id_lang` = " . (int)$cookie->id_lang . ") 
              
              LEFT JOIN `" . _DB_PREFIX_ . "tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = " . (int)Country::getDefaultCountryId() . " AND tr.`id_state` = 0) 
              LEFT JOIN `" . _DB_PREFIX_ . "tax` t ON (t.`id_tax` = tr.`id_tax`)
              LEFT JOIN `" . _DB_PREFIX_ . "order_history` oh ON (oh.`id_order` = o.`id_order`)
              LEFT JOIN `" . _DB_PREFIX_ . "order_state` os ON (os.`id_order_state` = oh.`id_order_state`)
              LEFT JOIN `" . _DB_PREFIX_ . "order_state_lang` osl ON (osl.`id_order_state` = oh.`id_order_state` AND osl.`id_lang` = " . (int)$cookie->id_lang . ") 
              WHERE o.date_add BETWEEN '" . $dateStart . "' AND '" . $dateEnd . "'" . $state . "
              ORDER BY o.`id_order` ASC, nb_product ASC";
    $results = Db::getInstance()->ExecuteS($sql);
    $datas   = array();

    foreach($results as $n => $result)
    {
      foreach($result as $key => $val)
      {
        if($key == 'et_unit_cost' || $key == 'ati_unit_cost' || $key == 'et_cost' || $key == 'ati_cost')
          $datas[$n][$key] = Tools::displayPrice($val, $this->currency, false);
        elseif($key == 'o.date_add')
          $datas[$n][$key] = Tools::displayDate($val, (int)$cookie->id_lang, false);
        elseif($key == 'nb_product')
          $datas[$n]['position'] = $val . '/' . $results[$n]['nb_total'];
        elseif($key == 'osl.name')
        {
          if($result['nb_product'] == 1)
            $datas[$n][$key] = $val;
        }
        elseif($key == 'al.name')
        {
          $datas[$n] = $this->_getFeatureArray($datas[$n]);

          if($result[$key] != '')
          {
            $features = explode('#', $val);
            $featuresIds = explode('#', $result['agl.id_attribute_group']);
            foreach ($features as $k => $feature)
              $datas[$n]['feature_' . $featuresIds[$k]] = $feature;
          }
        }
        elseif($key == 'nb_total' || $key == 'agl.id_attribute_group')
          continue;
        else
          $datas[$n][$key] = $val;
      }
    }

    return $datas;
  }
  
  private function _getTitlesAdmin($results)
  {
    $titles = array(
      $this->l('Order reference'),
      $this->l('Product n°/Total products'),
      $this->l('Date'),
      $this->l('Manufacturer'),
      $this->l('Product name')
    );

    foreach($this->_getGroupAttributes() AS $att)
        $titles[] = $att['name'];

    array_push($titles,
      $this->l('Quantity'),
      $this->l('ET unit cost') . ' (' . $this->_currencySign . ')',
      $this->l('ATI unit cost') . ' (' . $this->_currencySign . ')',
      $this->l('ET cost') . ' (' . $this->_currencySign . ')',
      $this->l('ATI cost') . ' (' . $this->_currencySign . ')',
      $this->l('Delivery lastname'),
      $this->l('Delivery firstname'),
      $this->l('Delivery address'),
      $this->l('Delivery zip code'),
      $this->l('Delivery city'),
      $this->l('Delivery country'),
      $this->l('Invoice lastname'),
      $this->l('Invoice firstname'),
      $this->l('Invoice address'),
      $this->l('Invoice zip code'),
      $this->l('Invoice city'),
      $this->l('Invoice country'),
      $this->l('Email'),
      $this->l('Order state'));
    
    return $titles;
  }
  
  private function _getFeatureArray($array)
  {
    for ($i=1; $i <= count($this->_getGroupAttributes()); $i++) { 
      $array['feature_'. $i] = '';
    }
    
    return $array;
  }
}
?>