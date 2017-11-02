<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class Dy_Engrave extends Module implements WidgetInterface
{
    private $templateFile;
	private $templates = array (
        'product' => 'product.tpl',
        'cart' => 'cart.tpl',
    );

    public function __construct()
    {
        $this->name = 'dy_engrave';
        $this->author = 'Deykun';
        $this->version = '0.3.5';
        $this->need_instance = 0;

        $this->ps_versions_compliancy = [
            'min' => '1.7.1.0',
            'max' => _PS_VERSION_,
        ];

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->trans('Engrave', array(), 'Modules.Engrave.Admin');
        $this->description = $this->trans('Additional engraving for products.', array(), 'Modules.Engrave.Admin');
    }

    public function install()
    {
        $this->_clearCache('*');

        Configuration::updateValue('ENGRAVER_FEATURE_ID', 8);
        Configuration::updateValue('HOME_FEATURED_ENGRAVERCAT', (int) Context::getContext()->shop->getCategory());
        Configuration::updateValue('HOME_FEATURED_ENGRAVERRANDOMIZE', false);

        return parent::install()
			&& $this->registerHook('displayHeader')
            && $this->registerHook('displayShoppingCart')	
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
		$this->context->controller->registerStylesheet('modules-engraver', 'modules/'.$this->name.'/css/engrave.css', ['media' => 'all', 'priority' => 150]);
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
		$variables = $this->getWidgetVariables($hookName, $configuration);
		
        $templateFile = $this->templates[$variables['template']];
		
		$this->smarty->assign('engraver', $variables);
		
		return $this->fetch('module:'.$this->name.'/views/templates/hook/'.$templateFile);
    }

	private function getProductID($configuration)
    {
        if (empty($configuration['product'])) {
            return 0;
        }

        $product = $configuration['product'];
        if (is_object($product)) {
            $product = (array) $product;
            $product['id_product'] = $product['id'];
        }

        $id_product = $product['id_product'];

        if (!empty($id_product)) {
            return $id_product;
        }

        return 0;
    }
	
	private function engraverEnableInProduct($productID)
	{
		$featuresObj = new Product($productID);
		$productFeatures = $featuresObj->getFrontFeatures($this->context->language->id);
		
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
		$engraver = array();
		$productID = $this->getProductID($configuration);
		$cartID = $configuration['cart']->id;
		
		if ($productID > 0) {
			$engraver['template'] = 'product';
			
			$engraver['product_id'] = $productID;
			$engraver['enable'] = $this->engraverEnableInProduct($productID);	
			
        } else if ($cartID > 0) {
			$engraver['template'] = 'cart';
			$engraver['enable'] = false;
			
			$cartObj = new Cart($cartID);
			$products = $cartObj->getProducts(true);
			$engraver['enablein'] = 0;
			$engraver['total'] = count($products);
			
			$engraver['products'] = array();
			
			foreach($products as $product) {
				if ($this->engraverEnableInProduct($product['id_product'])) {
					$engraver['enable'] = true;
					$engraver['enablein'] += 1;
					
					$tempproduct = array('');
					$tempproduct['id'] = $product['id_product'];
					$tempproduct['name'] = $product['name'];
					$tempproduct['attributes'] = $product['attributes'];
					
					$productCover = Product::getCover($product['id_product']);
					$tempproduct['cover_url'] = $this->context->link->getImageLink($product['link_rewrite'], $productCover['id_image'], ImageType::getFormatedName('home'));
					
					array_push($engraver['products'], $tempproduct);
				}
			}
		}
		
		return $engraver;

    }
}
