<?php
/**
 * This file is part of My Kitty, a module for Prestashop.
 *
 * @author    Philippe <philippe@dissitou.org>
 * @copyright 2019 Philippe Hénaff
 * @license   Licensed under the GPL version 2.0 license
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class MyKitty extends Module
{
    public function __construct()
    {
        $this->name = 'mykitty';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Philippe';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array(
            'min' => '1.7',
            'max' => _PS_VERSION_,
        );
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('My Kitty');
        $this->description = $this->l('Manage a kitty based on shop orders');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        $languages = Language::getLanguages(false);
        foreach ($languages as $language) {
            if (!Configuration::get('mykitty_title_'.$language['id_lang'])) {
                $this->warning = $this->l('No name provided');
            }
        }
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        $languages = Language::getLanguages(false);
        foreach ($languages as $language) {
            if (!parent::install() || !$this->registerHook('displayLeftColumn') || !$this->registerHook('header') || !Configuration::updateValue('mykitty_title_'.$language['id_lang'], 'My kitty') || !Configuration::updateValue('mykitty_value_'.$language['id_lang'], '0.50')) {
                return false;
            }

            return true;
        }
    }

    public function uninstall()
    {
        $languages = Language::getLanguages(false);
        foreach ($languages as $language) {
            if (!parent::uninstall() || !Configuration::deleteByName('mykitty_value_'.$language['id_lang']) || !Configuration::deleteByName('mykitty_title_'.$language['id_lang'])) {
                return false;
            }

            return true;
        }
    }

    public function getContent()
    {
        $output = null;

        if (Tools::isSubmit('submit' . $this->name)) {
            $languages = Language::getLanguages(false);
            foreach ($languages as $language) {
                $mykitty_value = (string) Tools::getValue('mykitty_value_'.$language['id_lang']);
                $mykitty_title = (string) Tools::getValue('mykitty_title_'.$language['id_lang']);
                $mykitty_href = (string) Tools::getValue('mykitty_href_'.$language['id_lang']);


                if (!$mykitty_value || empty($mykitty_value) || !Validate::isGenericName($mykitty_value) ||
                !$mykitty_title || empty($mykitty_title) || !Validate::isGenericName($mykitty_title)) {
                    $output .= $this->displayError($this->l('Invalid message'));
                } else {
                    Configuration::updateValue('mykitty_value_'.$language['id_lang'], $mykitty_value);
                    Configuration::updateValue('mykitty_title_'.$language['id_lang'], $mykitty_title);
                    Configuration::updateValue('mykitty_href_'.$language['id_lang'], $mykitty_href);

                    $output = $this->displayConfirmation($this->l('Settings updated'));
                }
            }
        }

        return $output . $this->displayForm();
    }

    public function displayForm()
    {
        // Get default language
        $default_lang = (int) Configuration::get('PS_LANG_DEFAULT');

        // Init Fields form array
        $fields_form = array();
        $fields_form[0]['form'] = array(
            'legend' => array(
            'title' => $this->l('Settings'),
        ),
            'input' => array(
            array(
                'type' => 'text',
                'label' => $this->l('Title:'),
                'name' => 'mykitty_title',
                'size' => 5,
                'required' => true,
                'lang' => true,
                'desc' => $this->l('Mandatory. Widget title.'),
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Amount per order:'),
                'name' => 'mykitty_value',
                'size' => 5,
                'required' => true,
                'lang' => true,
                'desc' => $this->l('Mandatory. Number, two digits after the decimal point. Separator : dot.'),
                ),
            array(
                'type' => 'text',
                'label' => $this->l('Link destination:'),
                'name' => 'mykitty_href',
                'size' => 5,
                'required' => false,
                'lang' => true,
                'desc' => $this->l('Optional. If no destination is provided, there will be no link on the image'),
                ),
        ),
            'submit' => array(
            'title' => $this->l('Save'),
            'class' => 'btn btn-default pull-right',
        ),
        );

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        foreach (Language::getLanguages(false) as $lang) {
            $helper->languages[] = array(
                'id_lang' => $lang['id_lang'],
                'iso_code' => $lang['iso_code'],
                'name' => $lang['name'],
                'is_default' => ($default_lang == $lang['id_lang'] ? 1 : 0)
            );
        }

        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true; // false -> remove toolbar
        $helper->toolbar_scroll = true; // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = array(
            'save' => array(
            'desc' => $this->l('Save'),
            'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name .
            '&token=' . Tools::getAdminTokenLite('AdminModules'),
        ),
            'back' => array(
            'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Back to list'),
        ),
        );
        
        // Load current value
        foreach ($helper->languages as $language) {
            $helper->fields_value['mykitty_value'][$language['id_lang']] = Configuration::get('mykitty_value_'.$language['id_lang']);
            $helper->fields_value['mykitty_title'][$language['id_lang']] = Configuration::get('mykitty_title_'.$language['id_lang']);
            $helper->fields_value['mykitty_href'][$language['id_lang']] = Configuration::get('mykitty_href_'.$language['id_lang']);
        }

        return $helper->generateForm($fields_form);
    }

    public function hookDisplayLeftColumn($params)
    {
        $languages = Language::getLanguages(false);
        foreach ($languages as $language) {
            $this->context->smarty->assign(array(
            'mykitty_title_'.$language['id_lang'] => Configuration::get('mykitty_title_'.$language['id_lang']),
            'mykitty_value_'.$language['id_lang'] => Configuration::get('mykitty_value_'.$language['id_lang']),
            'mykitty_href_'.$language['id_lang'] => Configuration::get('mykitty_href_'.$language['id_lang']),
            ));
        }
        return $this->display(__FILE__, 'mykitty.tpl');
    }

    public function hookDisplayHeader()
    {
        $this->context->controller->addCSS($this->_path . 'views/css/mykitty.css', 'all');
    }

    public static function getMyKitty()
    {
        $context = Context::getContext();

        $query = 'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'orders o 
        LEFT JOIN ' . _DB_PREFIX_ . 'order_state os ON (o.current_state = os.id_order_state) 
        WHERE os.paid = 1 AND MONTH(o.date_add) = MONTH(NOW()) AND YEAR(o.date_add) = YEAR(NOW())';

        $nb_orders = Db::getInstance()->getValue($query);
        

        $languages = Language::getLanguages(false);
        foreach ($languages as $language) {
            $mykitty_total = number_format((float) $nb_orders * Configuration::get('mykitty_value_'.$language['id_lang']), 2, ',', '') . ' €';
            $context->smarty->assign(array(
            'mykitty_title_'.$language['id_lang'] => Configuration::get('mykitty_title_'.$language['id_lang']),
            'mykitty_value_'.$language['id_lang'] => Configuration::get('mykitty_value_'.$language['id_lang']),
            'mykitty_href_'.$language['id_lang'] => Configuration::get('mykitty_href_'.$language['id_lang']),
            'mykitty_total_'.$language['id_lang'] => $mykitty_total,
            ));
        }
    }
}

$kitty = new MyKitty;
$kitty->getMyKitty();
