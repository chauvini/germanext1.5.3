<?php

class PaymentCostCore extends ObjectModel
{
	public $id_payment;
	public $module;
	public $cost_name;
   public $impact_dir;
	public $impact_type;
	public $impact_value;
	public $active;
	
	protected	$fieldsRequired = array('id_payment', 'module');
	protected	$fieldsSize = array('module' => 100);
	protected	$fieldsValidate = array(
		'id_payment' => 'isUnsignedId',
		'module' => 'isAnything', 
        'impact_dir' => 'isUnsignedId',
		'impact_type' => 'isUnsignedId',
		'impact_value' => 'isFloat',
		'active' => 'isUnsignedId');

	protected 	$table = 'payment_cost';
	protected 	$identifier = 'id_payment';

   //protected   $fieldsRequiredLang = array('cost_name');
   protected   $fieldsSizeLang = array('cost_name' => 128);
	protected   $fieldsValidateLang = array( 'cost_name' => 'isAnything');
   
	public function getFields()
	{
		parent::validateFields();
		$fields['id_payment'] = (int)($this->id_payment);
		$fields['module'] = pSQL($this->module);
	   $fields['impact_dir'] = (int)($this->impact_dir);
		$fields['impact_type'] = (int)($this->impact_type);
		$fields['impact_value'] = (float)($this->impact_value);
		$fields['active'] = (int)($this->active);
		return $fields;
	}
	
	public function add($autodate = true, $nullValues = false)
	{
		return false;
	}
   
   public function getPaymentList($bGetPaymentCost = FALSE)
   {
      if($bGetPaymentCost)
         return Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.$this->table.'`');
      
      global $cart, $cookie;
		$id_customer = (int)($cookie->id_customer);
		$billing = new Address((int)($cart->id_address_invoice));
      $id_country =(int)($billing->id_country);
      
      $query ='SELECT DISTINCT pm.* 
               FROM `'._DB_PREFIX_.$this->table.'` pm
               LEFT JOIN `'._DB_PREFIX_.'module` m ON pm.`module` = m.`name` AND m.`active` = 1
               LEFT JOIN `'._DB_PREFIX_.'module_country` mc ON m.`id_module` = mc.`id_module`
               INNER JOIN `'._DB_PREFIX_.'module_group` mg ON (m.`id_module` = mg.`id_module`)
               INNER JOIN `'._DB_PREFIX_.'customer_group` cg ON (cg.`id_group` = mg.`id_group` AND cg.`id_customer` = '.$id_customer.')
               LEFT JOIN `'._DB_PREFIX_.'hook_module` hm ON hm.`id_module` = m.`id_module`
               WHERE mc.id_country = '.$id_country.'
               ORDER BY hm.`position`, m.`name` DESC';
      //die ($query);
      return Db::getInstance()->ExecuteS($query);
   }
   
   public function getPriceImpact($price)
   {
      $impact = (float)$this->impact_value;

      if ($this->impact_type == 0) $impact = ((float)$price) * ($impact/100);
      if ($this->impact_dir == 1)  return $impact;         
      if ($this->impact_dir == 2)  return $impact*(-1);
      return 0;
   }
   static public function s_getPriceImpact($id_payment, $price)
   {
      $paymentCost = new PaymentCost($id_payment);
      return $paymentCost->getPriceImpact($price);
   }
   
   public function getTranslationsFieldsChild()
	{
		parent::validateFieldsLang(true, false);
      $fields = array();
		$languages = Language::getLanguages(false);
		foreach ($languages as $language)
		{
			$fields[$language['id_lang']]['id_lang'] = $language['id_lang'];
			$fields[$language['id_lang']][$this->identifier] = (int)($this->id);
			$fields[$language['id_lang']]['cost_name'] = (isset($this->cost_name[$language['id_lang']])) ? pSQL($this->cost_name[$language['id_lang']], true) : '';
		}
		return $fields;
	}
   
   static public function getFeeTitle($paymentModuleId, $languageId = null)
	{
		if ( ! Validate::isUnsignedId($paymentModuleId) || isset($languageId) && ! Validate::isUnsignedId($languageId))
			die(Tools::displayError());
			
		$languageId = isset($languageId) ? (int)$languageId : (int)Configuration::get('PS_LANG_DEFAULT');
			
		$paymentCost = new PaymentCost((int)$paymentModuleId, $languageId);
		
		if (Validate::isLoadedObject($paymentCost))
			return $paymentCost->cost_name;
		
		return ;
	}
	
	public static function getPaymentIdByModuleId($module_id)
	{
		$payment_id = 0;
		
		$module_name = Db::getInstance()->getValue('
			SELECT
				`name`
			FROM
				`' . _DB_PREFIX_ . 'module`
			WHERE
				`id_module` = ' . (int)$module_id
		);

		if ($module_name && ! Tools::isEmpty($module_name))
		{
			$payment_id = Db::getInstance()->getValue('
				SELECT
					`id_payment`
				FROM
					`' . _DB_PREFIX_ . 'payment_cost`
				WHERE
					`module` = "' . pSQL($module_name) . '"'
			);
		}
		
		return (int)$payment_id > 0 ? (int)$payment_id : false;
	}

	public static function getModuleIdByPaymentId($id_payment)
	{
		$module_id = 0;
		
		$module_name = Db::getInstance()->getValue('
			SELECT
				`module`
			FROM
				`' . _DB_PREFIX_ . 'payment_cost`
			WHERE
				`id_payment` = "' . (int)($id_payment) . '"'
		);
		if ($module_name && ! Tools::isEmpty($module_name))
		{
			$module_id = Db::getInstance()->getValue('
				SELECT
					`id_module`
				FROM
					`' . _DB_PREFIX_ . 'module`
				WHERE
					`name` = "' . pSQL($module_name) . '"'
			);
		}
		
		return (int)$module_id > 0 ? (int)$module_id : false;
	}
}

