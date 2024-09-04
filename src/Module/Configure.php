<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    UAB "Reprezentuok" <info@reprezentuok.lt>
 * @copyright Reprezentuok, UAB, 2023
 * @license   https://opensource.org/licenses/AFL-3.0
 */

namespace Re\ContactForm\Module;

use Context;
use Module;
use Tools;

class Configure
{
    /** @var Module */
    private $module;

    public function __construct(Module $module)
    {
        $this->module = $module;
    }

    public function __invoke(): string
    {
        $output = $this->getMenu();

        foreach ($this->module->forms as $form) {
            $output .= (new $form($this->module))();
        }

        return $output;
    }

    private function getMenu()
    {
        $currentController = Tools::getValue('controller');
        $tabs = $this->module->getTabs();
        $context = Context::getContext();

        $menu = [
            [
                'link' => $context->link->getAdminLink('AdminModules', true) . '&configure=' . $this->module->name,
                'active' => 'AdminModules' == $currentController,
                'title' => $this->module->getTranslator()->trans('Configuration', [], 'Modules.Recontactform.Module'),
                'icon' => 'icon icon-cogs',
            ],
            [
                'link' => $context->link->getAdminLink($this->module::MODULE_ADMIN_CONTROLLER),
                'active' => $currentController == $this->module::MODULE_ADMIN_CONTROLLER,
                'title' => $tabs[$this->module::MODULE_ADMIN_CONTROLLER]['title'],
                'icon' => 'icon icon-dot-circle-o',
            ],
        ];

        $context->smarty->assign('menuItems', $menu);

        return $context->smarty->fetch(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/menu.tpl');
    }
}
