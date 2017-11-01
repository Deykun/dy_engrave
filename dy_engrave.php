<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class Dy_Engrave extends Module implements WidgetInterface
{
    private $templateFile;

    public function __construct()
    {
        $this->name = 'dy_engrave';
        $this->author = 'Deykun';
        $this->version = '0.2.0';
        $this->need_instance = 0;

        $this->ps_versions_compliancy = [
            'min' => '1.7.1.0',
            'max' => _PS_VERSION_,
        ];

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->trans('Engrave', array(), 'Modules.Engrave.Admin');
        $this->description = $this->trans('Additional engraving for products.', array(), 'Modules.Engrave.Admin');

        $this->templateFile = 'module:dy_engrave/views/templates/hook/engraver.tpl';
    }

    public function install()
    {
        $this->_clearCache('*');

        Configuration::updateValue('ENGRAVER_FEATURE_ID', 8);
        Configuration::updateValue('HOME_FEATURED_ENGRAVERCAT', (int) Context::getContext()->shop->getCategory());
        Configuration::updateValue('HOME_FEATURED_ENGRAVERRANDOMIZE', false);

        return parent::install()
			&& $this->registerHook('displayHeader')
            && $this->registerHook('displayOrderConfirmation2')	
            && $this->registerHook('displayProductAdditionalInfo')	
        ;
    }

    public function uninstall()
    {
        $this->_clearCache('*');

        return parent::uninstall();
    }
	
	public function hookdisplayHeader($params)
    {
        $this->context->controller->registerJavascript('modules-engraver', 'modules/'.$this->name.'/js/engrave.js', ['position' => 'bottom', 'priority' => 150]);
    }

    public function hookAddProduct($params)
    {
        $this->_clearCache('*');
    }

    public function hookUpdateProduct($params)
    {
        $this->_clearCache('*');
    }

    public function hookDeleteProduct($params)
    {
        $this->_clearCache('*');
    }

    public function hookCategoryUpdate($params)
    {
        $this->_clearCache('*');
    }

    public function hookActionAdminGroupsControllerSaveAfter($params)
    {
        $this->_clearCache('*');
    }

    public function _clearCache($template, $cache_id = null, $compile_id = null)
    {
        parent::_clearCache($this->templateFile);
    }

    public function getContent()
    {
        $output = '';
        $errors = array();

        if (Tools::isSubmit('submitHomeFeatured')) {
            $fid = Tools::getValue('ENGRAVER_FEATURE_ID');
            if (!Validate::isInt($fid) || $fid <= 0) {
                $errors[] = $this->trans('ID of feature is invalid. Please enter a positive number.', array(), 'Modules.Engrave.Admin');
            }

            $cat = Tools::getValue('HOME_FEATURED_ENGRAVERCAT');
            if (!Validate::isInt($cat) || $cat <= 0) {
                $errors[] = $this->trans('The category ID is invalid. Please choose an existing category ID.', array(), 'Modules.Engrave.Admin');
            }

            $rand = Tools::getValue('HOME_FEATURED_ENGRAVERRANDOMIZE');
            if (!Validate::isBool($rand)) {
                $errors[] = $this->trans('Invalid value for the "randomize" flag.', array(), 'Modules.Engrave.Admin');
            }
            if (isset($errors) && count($errors)) {
                $output = $this->displayError(implode('<br />', $errors));
            } else {
                Configuration::updateValue('ENGRAVER_FEATURE_ID', (int) $fid);
                Configuration::updateValue('HOME_FEATURED_ENGRAVERCAT', (int) $cat);
                Configuration::updateValue('HOME_FEATURED_ENGRAVERRANDOMIZE', (bool) $rand);

                $this->_clearCache('*');

                $output = $this->displayConfirmation($this->trans('The settings have been updated.', array(), 'Admin.Notifications.Success'));
            }
        }

        return $output.$this->renderForm();
    }

    public function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->trans('Settings', array(), 'Admin.Global'),
                    'icon' => 'icon-cogs',
                ),

                'description' => $this->trans('To add products to your homepage, simply add them to the corresponding product category (default: "Home").', array(), 'Modules.Engrave.Admin'),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->trans('Engraver feature ID.', array(), 'Modules.Engrave.Admin'),
                        'name' => 'ENGRAVER_FEATURE_ID',
                        'class' => 'fixed-width-xs',
                        'desc' => $this->trans('Add or find engraver feature and put here ID.', array(), 'Modules.Engrave.Admin'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->trans('Category from which to pick products to be displayed', array(), 'Modules.Engrave.Admin'),
                        'name' => 'HOME_FEATURED_ENGRAVERCAT',
                        'class' => 'fixed-width-xs',
                        'desc' => $this->trans('Choose the category ID of the products that you would like to display on homepage (default: 2 for "Home").', array(), 'Modules.Engrave.Admin'),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->trans('Randomly display featured products', array(), 'Modules.Engrave.Admin'),
                        'name' => 'HOME_FEATURED_ENGRAVERRANDOMIZE',
                        'class' => 'fixed-width-xs',
                        'desc' => $this->trans('Enable if you wish the products to be displayed randomly (default: no).', array(), 'Modules.Engrave.Admin'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->trans('Yes', array(), 'Admin.Global'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->trans('No', array(), 'Admin.Global'),
                            ),
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->trans('Save', array(), 'Admin.Actions'),
                ),
            ),
        );

        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->id = (int) Tools::getValue('id_carrier');
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitHomeFeatured';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($fields_form));
    }

    public function getConfigFieldsValues()
    {
        return array(
            'ENGRAVER_FEATURE_ID' => Tools::getValue('ENGRAVER_FEATURE_ID', (int) Configuration::get('ENGRAVER_FEATURE_ID')),
            'HOME_FEATURED_ENGRAVERCAT' => Tools::getValue('HOME_FEATURED_ENGRAVERCAT', (int) Configuration::get('HOME_FEATURED_ENGRAVERCAT')),
            'HOME_FEATURED_ENGRAVERRANDOMIZE' => Tools::getValue('HOME_FEATURED_ENGRAVERRANDOMIZE', (bool) Configuration::get('HOME_FEATURED_ENGRAVERRANDOMIZE')),
        );
    }

    public function renderWidget($hookName = null, array $configuration = [])
    {
        if (!$this->isCached($this->templateFile, $this->getCacheId('dy_engrave'))) {
            $variables = $this->getWidgetVariables($hookName, $configuration);

            if (empty($variables)) {
                return false;
				
            }

            $this->smarty->assign($variables);
        }

        return $this->fetch($this->templateFile, $this->getCacheId('dy_engrave'));
    }

	private function getProductID($configuration)
    {
        if (empty($configuration['product'])) {
            return false;
        }

        $product = $configuration['product'];
        if (is_object($product)) {
            $product = (array) $product;
            $product['id_product'] = $product['id'];
        }

        $id_product = $product['id_product'];

        if (!empty($id_product)) {
            return array(
                'id_product' => $id_product
            );
        }

        return false;
    }
	
	private function engraverEnableInProduct($productFeatures)
	{
		$engraverFeatureID = $this->getConfigFieldsValues()['ENGRAVER_FEATURE_ID'];	
		
		foreach($productFeatures as $feature) {
			if ($feature['id_feature'] == $engraverFeatureID) {
				
				if ($feature['value'] != '' && strtolower($feature['value']) != 'no') {
					return true;
				}
				
				break;
			}
		}
        return false;
    }
	
    public function getWidgetVariables($hookName = null, array $configuration = [])
    {
		$params = $this->getProductID($configuration);
		
		if ($params['id_product']) {
					
			$engraver = array();

			$engraver['id_product'] = $params['id_product'];

			$featuresObj = new Product($engraver['id_product']);
			$productFeatures = $featuresObj->getFrontFeatures($this->context->language->id);

			$engraver['enable'] = $this->engraverEnableInProduct($productFeatures);


			return array(
				'engraver' => $engraver,
			);
		} else {
			return false;
		}

    }
}
