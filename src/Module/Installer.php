<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    UAB "Reprezentuok" <info@reprezentuok.lt>
 * @copyright Reprezentuok, UAB, 2023
 * @license   https://opensource.org/licenses/AFL-3.0
 */

namespace Re\ContactForm\Module;

use Db;
use Language;
use Tab;

class Installer
{
    /** @var \Module */
    private $module;

    public function __construct(\Module $module)
    {
        $this->module = $module;
    }

    public function __invoke(): bool
    {
        return $this->hooks() && $this->tables() && $this->tabs();
    }

    private function hooks(): bool
    {
        $result = true;
        foreach ($this->module->hooks as $hook) {
            $result &= $this->module->registerHook($hook);
        }

        return $result;
    }

    private function tables(): bool
    {
        $sql = [];

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 're_contact_form` (
          `id_contact` int(10) unsigned NOT NULL auto_increment,
          `type` int(10) NULL,
          `name` varchar(255) NOT NULL,
          `city` varchar(255) NOT NULL,
          `phone` varchar(32) NOT NULL,
          `email` varchar(255) NULL,
          `square` varchar(255) NULL,
          `date` DATE NULL,
          `file_name` varchar(18) NULL,
          `info` varchar(512) NULL,
          `date_add` datetime NULL,
          PRIMARY KEY  (`id_contact`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        $result = true;

        foreach ($sql as $query) {
            $result = $result && Db::getInstance()->execute($query);
        }

        return $result;
    }

    private function tabs(): bool
    {
        foreach ($this->module->getTabs() as $controller => $tabData) {
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = $controller;
            $tab->name = [];
            $languages = Language::getLanguages(false);

            foreach ($languages as $language) {
                $tab->name[$language['id_lang']] = $tabData['title'];
            }

            $tab->id_parent = $tabData['parent_tab'];
            $tab->module = $this->module->name;

            if (!$tab->save()) {
                $this->module->displayError($this->module->getTranslator()->trans('Error while creating tab ', [], 'Modules.Recontactform.Module') . $tabData['title']);

                return false;
            }
        }

        return true;
    }
}
