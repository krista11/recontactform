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
use Tab;

class Uninstaller
{
    /** @var \Module */
    private $module;

    public function __construct(\Module $module)
    {
        $this->module = $module;
    }

    public function __invoke(): bool
    {
        return $this->hooks() && $this->tables() && $this->removeConfigs();
    }

    private function hooks(): bool
    {
        $result = true;

        foreach ($this->module->hooks as $hook) {
            $result &= $this->module->unregisterHook($hook);
        }

        return $result;
    }

    private function tables(): bool
    {
        $tables = ['re_contact_form'];
        $result = true;

        foreach ($tables as $table) {
            $result = $result && Db::getInstance()->execute('DROP TABLE IF EXISTS ' . _DB_PREFIX_ . $table);
        }

        return $result;

    }

    private function removeConfigs(): bool
    {
        $result = true;
        $settings = [
            'RE_CONTACT_FORM_EXAMPLES_LINK',
        ];

        foreach ($settings as $setting) {
            $result &= \Configuration::deleteByName($setting);
        }

        return $result;
    }
}
