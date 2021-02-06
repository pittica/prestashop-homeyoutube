<?php

/**
 * 2021 Pittica S.r.l.s.
 *
 * @author    Lucio Benini <info@pittica.com>
 * @copyright 2021 Pittica S.r.l.s.
 * @license   http://opensource.org/licenses/LGPL-3.0  The GNU Lesser General Public License, version 3.0 ( LGPL-3.0 )
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class PitticaHomeYoutube extends Module
{
    public function __construct()
    {
        $this->name          = 'pitticahomeyoutube';
        $this->tab           = 'front_office_features';
        $this->version       = '1.0.0';
        $this->author        = 'Pittica';
        $this->need_instance = 1;
        $this->bootstrap     = 1;

        parent::__construct();

        $this->displayName = $this->l('YouTube for Home');
        $this->description = $this->l('YouTube for homepage.');

        $this->ps_versions_compliancy = array(
            'min' => '1.7',
            'max' => _PS_VERSION_
        );
    }

    public function install()
    {
        return parent::install() && $this->registerHook('displayHome');
    }

    public function uninstall()
    {
        Configuration::deleteByName('PITTICA_YOUTUBE_HOME_CODE');
        Configuration::deleteByName('PITTICA_YOUTUBE_HOME_ALIGNMENT');

        return parent::uninstall();
    }

    public function hookDisplayHome($params)
    {
        $code = Configuration::get('PITTICA_YOUTUBE_HOME_CODE');

        if ($code) {
            $this->context->smarty->assign(array(
                'youtubecode' => $code,
                'youtubealign' => Configuration::get('PITTICA_YOUTUBE_HOME_ALIGNMENT', null, null, null, 'center')
            ));

            return $this->fetch('module:' . $this->name . '/views/templates/hook/displayHome.tpl');
        } else {
            return null;
        }
    }

    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('savepitticahomeyoutube')) {
            Configuration::updateValue('PITTICA_YOUTUBE_HOME_CODE', Tools::getValue('code'));
            Configuration::updateValue('PITTICA_YOUTUBE_HOME_ALIGNMENT', Tools::getValue('alignment'));

            $output .= $this->displayConfirmation($this->l('Settings updated.'));
        }

        return $output . $this->renderForm();
    }

    protected function renderForm()
    {
        $lang     = (int) Configuration::get('PS_LANG_DEFAULT');

        $helper                           = new HelperForm();
        $helper->module                   = $this;
        $helper->name_controller          = 'pitticahomeyoutube';
        $helper->identifier               = $this->identifier;
        $helper->token                    = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex             = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->default_form_language    = $lang;
        $helper->allow_employee_form_lang = $lang;
        $helper->title                    = $this->displayName;
        $helper->submit_action            = 'savepitticahomeyoutube';

        $helper->fields_value = array(
            'code' => Configuration::get('PITTICA_YOUTUBE_HOME_CODE'),
            'alignment' => Configuration::get('PITTICA_YOUTUBE_HOME_ALIGNMENT', null, null, null, 'center')
        );

        return $helper->generateForm(array(
            array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Settings')
                    ),
                    'input' => array(
                        array(
                            'type' => 'text',
                            'label' => $this->l('YouTube Code'),
                            'name' => 'code'
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Alignment'),
                            'name' => 'alignment',
                            'options' => array(
                                'query' => array(
                                    array(
                                        'value' => 'center',
                                        'name' => $this->l('Center')
                                    ),
                                    array(
                                        'value' => 'left',
                                        'name' => $this->l('Left')
                                    ),
                                    array(
                                        'value' => 'right',
                                        'name' => $this->l('Right')
                                    )
                                ),
                                'id' => 'value',
                                'name' => 'name'
                            )
                        )
                    ),
                    'submit' => array(
                        'title' => $this->l('Save')
                    )
                )
            )
        ));
    }
}
