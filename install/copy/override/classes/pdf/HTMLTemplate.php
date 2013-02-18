<?php
abstract class HTMLTemplate extends HTMLTemplateCore
{
    protected function getTemplate($template_name)
	{
		$template = false;
		$template_file = GN_PDF_PATH.$template_name.'.tpl';

		if (file_exists($template_file))
			$template = $template_file;
            
        if ( ! $template)
        {
            return parent::getTemplate($template_name);
        }

		return $template;
	}
	
	public function getFooter()
	{
		$shop_address = $this->getShopAddress();
		
		$gn_address_rows = array();
		$gn_address_row_names = array(
			'PS_SHOP_COMPANY' => self::l('Company'),
			'PS_SHOP_ADDR1|PS_SHOP_ADDR2' => self::l('Address'),
			'PS_SHOP_CODE' => self::l('Post/Zip code'),
			'PS_SHOP_CITY' => self::l('City'),
			'PS_SHOP_STATE' => self::l('State'),
			'PS_SHOP_COUNTRY' => self::l('Country'),
			'PS_SHOP_PHONE' => self::l('Phone'),
			'PS_SHOP_FAX' => self::l('Fax'),
			'PS_SHOP_EMAIL' => self::l('Email'),
			'PS_SHOP_REPRESENTER' => self::l('Authorised representative'),
			'PS_SHOP_REGISTER_COURT' => self::l('Register court'),
			'PS_SHOP_REGISTER_NUM' => self::l('Register number'),
			'PS_SHOP_SALES_TAX_ID' => self::l('Sales tax ID number'),
			'PS_SHOP_BANK_NAME' => self::l('Bank name'),
			'PS_SHOP_BANK_ACCOUNT' => self::l('Account number'),
			'PS_SHOP_BANK_CODE' => self::l('Bank identifier code'),
			'PS_SHOP_BANK_IBAN' => self::l('IBAN'),
			'PS_SHOP_BANK_SWIFT' => self::l('SWIFT'),
		);
		
		foreach ($gn_address_row_names as $config_var => $name)
		{
			$value = '';
			
			if (strpos($config_var, '|') !== false)
			{
				$config_var = explode('|', $config_var);

				foreach ($config_var as $config)
				{
					$tmp = Configuration::get($config);
					
					if ($tmp && ! Tools::isEmpty($tmp))
					{
						$value.= $tmp . '<br />';
					}
				}
				
				$value = rtrim($value, '<br />');
			}
			else
			{
				$value = Configuration::get($config_var);
			}

			if ( ! Tools::isEmpty($value))
			{
				array_push($gn_address_rows, array(
					'name' => $name,
					'value' => $value
				));
			}
		}
		
		$this->smarty->assign(array(
			'available_in_your_account' => $this->available_in_your_account,
			'shop_address' => $shop_address,
			'shop_fax' => Configuration::get('PS_SHOP_FAX'),
			'shop_phone' => Configuration::get('PS_SHOP_PHONE'),
			'shop_details' => Configuration::get('PS_SHOP_DETAILS'),
			'free_text' => Configuration::get('PS_INVOICE_FREE_TEXT', (int)Context::getContext()->language->id),
			'footer_address_rows' => $gn_address_rows
		));

		return $this->smarty->fetch($this->getTemplate('footer'));
	}
}

