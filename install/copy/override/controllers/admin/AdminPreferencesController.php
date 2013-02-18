<?php
class AdminPreferencesController extends AdminPreferencesControllerCore
{
	public function __construct()
	{
        global $cookie;
        
		$this->context = Context::getContext();
		$this->className = 'Configuration';
		$this->table = 'configuration';

		$cms_tab = array(0 =>
			array(
				'id'   => 0,
				'name' => $this->l('None')
			)
		);
        
		foreach (CMS::listCms($cookie->id_lang) as $cms_file)
        {
			$cms_tab[] = array(
                'id'   => $cms_file['id_cms'],
                'name' => $cms_file['meta_title']
            );
        }

		// Prevent classes which extend AdminPreferences to load useless data
		if (get_class($this) == 'AdminPreferencesController')
		{
			$round_mode = array(
				array(
					'value' => PS_ROUND_UP,
					'name' => $this->l('superior')
				),
				array(
					'value' => PS_ROUND_DOWN,
					'name' => $this->l('inferior')
				),
				array(
					'value' => PS_ROUND_HALF,
					'name' => $this->l('classical')
				)
			);

			$fields = array(
				'PS_SSL_ENABLED' => array(
					'title' => $this->l('Enable SSL'),
					'desc' => $this->l('If your hosting provider allows SSL, you can activate SSL encryption (https://) for customer account identification and order processing'),
					'validation' => 'isBool',
					'cast' => 'intval',
					'type' => 'bool',
					'default' => '0'
				),
				'PS_TOKEN_ENABLE' => array(
					'title' => $this->l('Increase Front Office security'),
					'desc' => $this->l('Enable or disable token on the Front Office in order to improve PrestaShop security'),
					'validation' => 'isBool',
					'cast' => 'intval',
					'type' => 'bool',
					'default' => '0',
					'visibility' => Shop::CONTEXT_ALL
				),
				'PS_PRICE_ROUND_MODE' => array(
					'title' => $this->l('Round mode'),
					'desc' => $this->l('You can choose how to round prices: always round superior; always round inferior, or classic rounding'),
					'validation' => 'isInt',
					'cast' => 'intval',
					'type' => 'select',
					'list' => $round_mode,
					'identifier' => 'value'
				),
				'PS_DISPLAY_SUPPLIERS' => array(
					'title' => $this->l('Display suppliers and manufacturers'),
					'desc' => $this->l('Display suppliers and manufacturers list even if corresponding blocks are disabled'),
					'validation' => 'isBool',
					'cast' => 'intval',
					'type' => 'bool'
				),
				'PS_MULTISHOP_FEATURE_ACTIVE' => array(
					'title' => $this->l('Enable Multistore'),
					'desc' => $this->l('Multistore feature allows you to manage several shops with one back-office. If this feature is enabled, a "Multistore" page will be available in the "Advanced Parameters" menu.'),
					'validation' => 'isBool',
					'cast' => 'intval',
					'type' => 'bool',
					'visibility' => Shop::CONTEXT_ALL
				),
                'PS_PSTATISTIC' => array(
                    'title' => $this->l('User data storage'),
                    'desc' => $this->l('Storage of user data like visited pages, carts, or IP for statistics'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                ),
                'PS_CMS_ID_REVOCATION' => array(
                    'title' => $this->l('Conditions of use revocation CMS page'),
                    'desc' => $this->l('Choose the Conditions of use revocation CMS page'),
                    'validation' => 'isInt',
                    'type' => 'select',
                    'list' => $cms_tab,
                    'identifier' => 'id',
                    'cast' => 'intval'
                ),
                'PS_PRIVACY' => array(
                    'title' => $this->l('Checkbox for privacy policy'),
                    'desc' => $this->l('Require customers to accept or decline terms of privacy policy'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool'
                ),
                'PS_CMS_ID_PRIVACY' => array(
                    'title' => $this->l('Conditions of use privacy policy CMS page'),
                    'desc' => $this->l('Choose the Conditions of use privacy policy CMS page'),
                    'validation' => 'isInt',
                    'type' => 'select',
                    'list' => $cms_tab,
                    'identifier' => 'id',
                    'cast' => 'intval'
                ),
                'PS_CMS_ID_DELIVERY' => array(
                    'title' => $this->l('Conditions of use delivery CMS page'),
                    'desc' => $this->l('Choose the Conditions of use delivery CMS page'),
                    'validation' => 'isInt',
                    'type' => 'select',
                    'list' => $cms_tab,
                    'identifier' => 'id',
                    'cast' => 'intval'
                ),
                'PS_CMS_ID_IMPRINT' => array(
                    'title' => $this->l('Conditions of use imprint CMS page'),
                    'desc' => $this->l('Choose the Conditions of use imprint CMS page'),
                    'validation' => 'isInt',
                    'type' => 'select',
                    'list' => $cms_tab,
                    'identifier' => 'id',
                    'cast' => 'intval'
                )
			);
            
			// No HTTPS activation if you haven't already.
			if (!Tools::usingSecureMode())
			{
				$fields['PS_SSL_ENABLED']['type'] = 'disabled';
				$fields['PS_SSL_ENABLED']['disabled'] = '<a href="https://'.Tools::getShopDomainSsl().Tools::safeOutput($_SERVER['REQUEST_URI']).'">'.
					$this->l('Please click here to use HTTPS protocol before enabling SSL.').'</a>';
			}

			$this->fields_options = array(
				'general' => array(
					'title' =>	$this->l('General'),
					'icon' =>	'tab-preferences',
					'fields' =>	$fields,
					'submit' => array('title' => $this->l('   Save   '), 'class' => 'button'),
				),
			);
		}

		AdminController::__construct();
	}
}

