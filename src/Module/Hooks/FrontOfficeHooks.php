<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    UAB "Reprezentuok" <info@reprezentuok.lt>
 * @copyright Reprezentuok, UAB, 2023
 * @license   https://opensource.org/licenses/AFL-3.0
 */

namespace Re\ContactForm\Module\Hooks;

use \Module;
use \Configuration;
use \Context;

trait FrontOfficeHooks
{
    public function renderWidget($hookName, array $configuration)
    {
        $data = $this->getWidgetVariables($hookName, $configuration);
        $this->context->smarty->assign([
            'json' => json_encode($data),
            'pathApp' => $this->getPathUri() . 'views/js/app.js',
            'chunkVendor' => $this->getPathUri() . 'views/js/chunk-vendors.js'
        ]);

        return $this->context->smarty->fetch('module:recontactform/views/templates/front/form.tpl');
    }

    public function hookDisplayHome()
    {
        return $this->renderWidget('displayHome', []);
    }

    public function getWidgetVariables($hookName, array $configuration)
    {
        $variables = [
            'action' => $this->context->link->getModuleLink($this->name, 'submit', ['ajax' => 1]),
            'errorMessage' => $this->trans('Form has errors. Please fix and try again', [], 'Modules.Recontactform.Shop'),
            'types' => [
                'name' => $this->trans('Form type', [], 'Modules.Recontactform.Shop'),
                'options' => [
                    'normal' => [
                        'name' => $this->trans('Normal form', [], 'Modules.Recontactform.Shop'),
                    ],
                    'master' => [
                        'name' => $this->trans('Get the master\'s recommendation', [], 'Modules.Recontactform.Shop'),
                    ],
                ]
            ],
            'fields' => [
                'name' => [
                    'type' => 'text',
                    'name' => $this->trans('First name', [], 'Shop.Forms.Labels'),
                    'required' => ['normal', 'master'],
                ],
                'city' => [
                    'type' => 'text',
                    'name' => $this->trans('City', [], 'Shop.Forms.Labels'),
                    'required' => ['master'],
                ],
                'phone' => [
                    'type' => 'text',
                    'name' => $this->trans('Phone', [], 'Shop.Forms.Labels'),
                    'required' => ['normal', 'master'],
                ],
                'email' => [
                    'type' => 'email',
                    'name' => $this->trans('Email', [], 'Shop.Forms.Labels'),
                    'required' => ['master'],
                ],
                'quadrature' => [
                    'type' => 'text',
                    'name' => $this->trans('Preliminary square footage of the room', [], 'Modules.Recontactform.Shop'),
                    'required' => ['master'],
                ],
                'date' => [
                    'type' => 'date',
                    'name' => $this->trans('Possible start date', [], 'Modules.Recontactform.Shop'),
                    'required' => ['master'],
                ],
                'file-upload' => [
                    'type' => 'file',
                    'name' => $this->trans('If you have chosen the desired wall decoration from our samples, please add a photo', [], 'Modules.Recontactform.Shop'),
                    'required' => [],
                ],
                'information' => [
                    'type' => 'textarea',
                    'name' => $this->trans('Please provide additional information', [], 'Modules.Recontactform.Shop'),
                    'required' => [],
                ]
            ],
            'link' => [
                'name' => $this->trans('See examples here', [], 'Modules.Recontactform.Shop'),
                'url' => Configuration::get('RE_CONTACT_FORM_EXAMPLES_LINK', Context::getContext()->language->id),
            ]
        ];

        $variables['reCaptchaEnabled'] = false;

        $eicaptcha = Module::getInstanceByName('eicaptcha');
        if ($eicaptcha && $eicaptcha->active) {
            $variables['reCaptchaEnabled'] = true;
            $variables['siteKey'] = Configuration::get('CAPTCHA_PUBLIC_KEY');
        }

        return $variables;
    }
}
