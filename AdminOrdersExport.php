<?php
include_once('classes/ExportToCSV.class.php');
include_once('classes/LoaderTool.class.php');
require_once('Spreadsheet/Excel/Writer.php');

class AdminOrdersExport extends AdminTab
{
	private $_tpl;

	private $_dirName;
	private $_fileName;
	private $_file;

	private $_currency;

	public function __construct()
	{
	 global $cookie;

	 $this->table			= 'orders';
	 $this->className = 'Order';
	 $this->_tpl			= dirname(__FILE__) . '/AdminOrdersExport.tpl';

	 parent::__construct();

	 $this->_dirName	= dirname(__FILE__) . '/exports/';
	 $this->_fileName = $this->l('export_orders_');

	 $this->_currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
	}

	public function display()
	{
		global $smarty, $cookie, $currentIndex;

		$this->_postProcess();
		$smarty->assign('request_uri', Tools::safeOutput($_SERVER['REQUEST_URI']));
		$smarty->assign('dates', $this->_getDateForExport());
		$smarty->assign('order_states', $this->_getOrderStates());

		echo $smarty->display($this->_tpl);
	}

	private function _postProcess()
	{
		global $smarty, $cookie;

		if (Tools::isSubmit('submitExportFormat'))
		{
			$timestampS = (int)Tools::getValue('date');
			$timestampE = $timestampS + $this->_weekToSeconds();
			$dateStart	= date('Y-m-d H:i:s', $timestampS);
			$dateEnd		= date('Y-m-d H:i:s', $timestampE);

			if(Tools::getValue('export_type') == 'admin')
			{
				$results = $this->_getResultAdmin($dateStart, $dateEnd, (int)Tools::getValue('order_state'));
				$titles	 = $this->_getTitlesAdmin($results);
				$type		 = $this->l('admin');
				$dirType = 'admin/';
			}
			elseif(Tools::getValue('export_type') == 'supplier')
			{
				$results = $this->_getResultSupplier($dateStart, $dateEnd);
				$titles	 = $this->_getTitlesSupplier($results);
				$type		 = $this->l('supplier');
				$dirType = 'supplier/';
			}
			elseif(Tools::getValue('export_type') == 'carrier')
			{
				$results = $this->_getResultCarrier($dateStart, $dateEnd, (int)Tools::getValue('order_state'));
				$titles	 = $this->_getTitlesCarrier($results);
				$type		 = $this->l('carrier');
				$dirType = 'carrier/';
			}
			else
			{
				$smarty->assign('errors', $this->l('Error: You have not select an export type').' '. $this->_fileName);
				return false;
			}

			if(Tools::getValue('export_format') == 'csv')
			{
				// --- CSV ---
				$this->_dirName	 .= 'csv/' . $dirType;
				$this->_fileName .= $type . '_' . date('Y-m-d', $timestampS) . '_' . date('Y-m-d', $timestampE) . '.csv';
				$this->_file			= $this->_dirName . $this->_fileName;

				$datas = (is_array($results) ? array_merge(array($titles), $results) : $titles);
				$exportCSV = new ExportToCSV($this->_fileName, $this->_dirName, ',', '"');

				if(!$exportCSV->open())
				{
					$smarty->assign('errors', $this->l('Error: cannot write').' '. $this->_fileName);
					return false;
				}

				$exportCSV->setContent($datas);

				if($exportCSV->close())
				{
					$smarty->assign('confirm', $this->l('The CSV file has been successfully exported'));
					LoaderTool::downloadContent($this->_file, $this->_fileName, false, 'text/csv');

					return true; 
				}
				else
				{
					$smarty->assign('errors', $this->l('Error: An error as occured').' '. $this->_fileName);
					return false;
				}
			}
			elseif(Tools::getValue('export_format') == 'xls')
			{
				// --- XLS --- 
				$this->_dirName	 .= 'xls/' . $dirType;
				$this->_fileName .= $type . '_' . date('Y-m-d', $timestampS) . '_' . date('Y-m-d', $timestampE) . '.xls';
				$this->_file			= $this->_dirName . $this->_fileName;

				$datas = (is_array($results) ? array_merge(array($titles), $results) : $titles);

				// Creating workbook
				$workbook = new Spreadsheet_Excel_Writer($this->_file);
				$workbook->setVersion(8);
				
				if (PEAR::isError($worksheet)) {
					$smarty->assign('errors', $this->l('Error: cannot write').' '. $this->_fileName);
					return false;
				}

				// Adding worksheet
				$worksheet =& $workbook->addWorksheet($type . '_' . date('Y-m-d', $timestampS));
				$worksheet->setColumn(0,count($titles) - 1, 33);
				$worksheet->setRow(0,16);
				$worksheet->setInputEncoding('utf-8');

				// Set title format
				$format_title =& $workbook->addFormat();
				$format_title->setBold();
				$format_title->setPattern(1);
				$format_title->setFgColor(22);
				$format_title->setAlign('center');
				$format_title->setAlign('vcenter');

				// Data input - Header
				foreach($titles as $key => $title)
					$worksheet->write(0, $key, $title, $format_title);

				// Data input - Content
				foreach($results as $key => $product)
				{
					$keyRow = 0;
					foreach($product as $val)
					{
						$worksheet->write($key + 1, $keyRow, $val);
						$keyRow++;
					}
				}
				// Saving file
				if($workbook->close())
				{
					$smarty->assign('confirm', $this->l('The XLS file has been successfully exported'));
					LoaderTool::downloadContent($this->_file, $this->_fileName, false, 'application/vnd.ms-excel');

					return true; 
				}
				else
				{
					$smarty->assign('errors', $this->l('Error: An error as occured').' '. $this->_fileName);
					return false;
				}
			}
		}
	}

	// --- FOR ADMIN ---
	private function _getTitlesAdmin($results)
	{
		$titles = array(
			$this->l('Order reference'),
			$this->l('Product n°/Total products'),
			$this->l('Date'),
			$this->l('Product reference'),
			$this->l('Manufacturer'),
			$this->l('Product name')
		);

		foreach($this->_getGroupAttributes() AS $att)
				$titles[] = $att['name'];

		array_push($titles,
			$this->l('Weight'),
			$this->l('Quantity'),
			$this->l('ET unit cost') . ' (' . $this->_currency->getSign() . ')',
			$this->l('ATI unit cost') . ' (' . $this->_currency->getSign() . ')',
			$this->l('ET cost') . ' (' . $this->_currency->getSign() . ')',
			$this->l('ATI cost') . ' (' . $this->_currency->getSign() . ')',
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
			$this->l('Order state'),
			$this->l('Gift ?'),
			$this->l('Gift message'));

		return $titles;
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
									 od.`product_reference` AS `od.product_reference`,
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
									 od.`product_weight` AS `od.product_weight`,
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
									 osl.`name` AS `osl.name`,
									 o.`gift` AS `o.gift`,
									 o.`gift_message` AS `o.gift_message`
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
		$datas	 = array();

		foreach($results as $n => $result)
		{
			foreach($result as $key => $val)
			{
				if($key == 'et_unit_cost' || $key == 'ati_unit_cost' || $key == 'et_cost' || $key == 'ati_cost')
					$datas[$n][$key] = Tools::displayPrice($val, $this->_currency, false);
				elseif($key == 'o.date_add')
					$datas[$n][$key] = Tools::displayDate($val, (int)$cookie->id_lang, false);
				elseif($key == 'nb_product')
					$datas[$n]['position'] = $val . '/' . $results[$n]['nb_total'];
				elseif($key == 'od.product_weight')
					$datas[$n][$key] =	$val . Configuration::get('PS_WEIGHT_UNIT');
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

	// --- FOR CARRIER ---
	private function _getTitlesCarrier($results)
	{
		$titles = array(
			$this->l('Order reference'),
			$this->l('Product n°/Total products'),
			$this->l('Date'),
			$this->l('Product reference'),
			$this->l('Manufacturer'),
			$this->l('Product name')
		);

		foreach($this->_getGroupAttributes() AS $att)
				$titles[] = $att['name'];

		array_push($titles,
			$this->l('Weight'),
			$this->l('Quantity'),
			$this->l('ET unit cost') . ' (' . $this->_currency->getSign() . ')',
			$this->l('ATI unit cost') . ' (' . $this->_currency->getSign() . ')',
			$this->l('ET cost') . ' (' . $this->_currency->getSign() . ')',
			$this->l('ATI cost') . ' (' . $this->_currency->getSign() . ')',
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
			$this->l('Gift ?'),
			$this->l('Gift message')
		);

		return $titles;
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
									 od.`product_reference` AS `od.product_reference`,
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
									 od.`product_weight` AS `od.product_weight`,
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
									 o.`gift` AS `o.gift`,
									 o.`gift_message` AS `o.gift_message`
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
		$datas	 = array();

		foreach($results as $n => $result)
		{
			foreach($result as $key => $val)
			{
				if($key == 'et_unit_cost' || $key == 'ati_unit_cost' || $key == 'et_cost' || $key == 'ati_cost')
					$datas[$n][$key] = Tools::displayPrice($val, $this->_currency, false);
				elseif($key == 'o.date_add')
					$datas[$n][$key] = Tools::displayDate($val, (int)$cookie->id_lang, false);
				elseif($key == 'nb_product')
					$datas[$n]['position'] = $val . '/' . $results[$n]['nb_total'];
				elseif($key == 'od.product_weight')
					$datas[$n][$key] =	$val . Configuration::get('PS_WEIGHT_UNIT');
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

	// --- FOR SUPPLIER ---
	private function _getTitlesSupplier($results)
	{
		$titles = array(
			$this->l('Date'),
			$this->l('Manufacturer'),
			$this->l('Product reference'),
			$this->l('Product name')
		);

		foreach($this->_getGroupAttributes() AS $att)
				$titles[] = $att['name'];

		array_push($titles,
			$this->l('Quantity')
		);

		return $titles;
	}

	private function _getResultSupplier($dateStart, $dateEnd)
	{
		global $cookie;

		$sql = "SELECT SQL_CALC_FOUND_ROWS
										o.`date_add` AS `o.date_add`, 
										m.`name` AS `m.name`,
										od.`product_reference` AS `od.product_reference`,
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
											SUM(od.`product_quantity`) AS `quantity`
							FROM `" . _DB_PREFIX_ . "orders` o
							LEFT JOIN `" . _DB_PREFIX_ . "order_detail` od ON (od.`id_order` = o.`id_order`)
							LEFT JOIN `" . _DB_PREFIX_ . "product` p ON (p.`id_product` = od.`product_id`) 
							LEFT JOIN `" . _DB_PREFIX_ . "manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
							WHERE o.date_add BETWEEN '" . $dateStart . "' AND '" . $dateEnd . "'
							GROUP BY od.`product_attribute_id`, od.`product_id`";

		$results = Db::getInstance()->ExecuteS($sql);
		$datas	 = array();

		foreach($results as $n => $result)
		{
			foreach($result as $key => $val)
			{
				if($key == 'o.date_add')
					$datas[$n][$key] = Tools::displayDate($val, (int)$cookie->id_lang, false);
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
				elseif($key == 'agl.id_attribute_group')
					continue;
				else
					$datas[$n][$key] = $val;
			}
		}

		return $datas;
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

	private function _getFeatureArray($array)
	{
		for ($i=1; $i <= count($this->_getGroupAttributes()); $i++) { 
			$array['feature_'. $i] = '';
		}

		return $array;
	}

	// --- Tools ---
	private function _weekToSeconds()
	{
		return 7 * 24 * 60 * 60;
	}

	private function _getDateForExport()
	{
		global $cookie;
		$dates = array();
		$d = $this->_getDateInstall();
		while($d < time())
		{
			$dates[] = array(
				'timestamp' => $d,
				'display'		=> Tools::displayDate(date('Y-m-d H:i:s', $d), (int)$cookie->id_lang, false)
			);

			$d += $this->_weekToSeconds();
		}

		return $dates;
	}
}
?>