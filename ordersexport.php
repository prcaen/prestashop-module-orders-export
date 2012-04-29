<?php
class OrdersExport extends Module
{
	public function __construct()
	{
		$this->name = 'ordersexport';
		$this->tab = 'administration';
		$this->version = 0.1;
		$this->author = 'Pierrick CAEN';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Orders export');
		$this->description = $this->l('Export your orders in CSV or XLS');

		$this->_tabs = array(
			1 => array(
				'className' => 'AdminOrdersExport',
				'i18n'			=> array(
					 1 => 'Orders export',
					 2 => 'Export des commandes',
					 3 => 'Orders export',
					 4 => 'Orders export',
					 5 => 'Orders export',
				),
				'idParent'	=> Tab::getIdFromClassName('AdminTools')
			)
		);
	}

	public function install()
	{
		if(!parent::install())
			return false;

		// Set AdminTab
		foreach($this->_tabs AS $tab)
			$this->_installModuleTab($tab['className'], $tab['i18n'], $tab['idParent']);

		return true;
	}

	public function uninstall()
	{
		if(!parent::uninstall())
			return false;

		// Unset AdminTab
		foreach($this->_tabs AS $tab)
			$this->_uninstallModuleTab($tab['className']);

		return true;
	}

	private function _installModuleTab($className, $name, $idParent)
	{
		$tab = new Tab();

		$tab->class_name = $className;
		$tab->name			 = $name;
		$tab->module		 = $this->name;
		$tab->id_parent	 = $idParent;

		if(!$tab->save())
		{
			$this->_errors[] = Tools::displayError('An error occurred while saving new tab: ') . ' <b>' . $tab->name . ' (' . mysql_error() . ')</b>';
			return false;
		}
	}
	
	private function _uninstallModuleTab($className)
	{
		$idTab = Tab::getIdFromClassName($className);

		if($idTab != 0)
		{
			$tab = new Tab($idTab);
			$tab->delete();

			return true;
		}
	}
}