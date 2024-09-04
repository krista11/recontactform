<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    UAB "Reprezentuok" <info@reprezentuok.lt>
 * @copyright Reprezentuok, UAB, 2023
 * @license   https://opensource.org/licenses/AFL-3.0
 */

namespace Re\ContactForm\Module\Forms;

use AdminController;
use Configuration;
use Context;
use HelperForm;
use Language;
use Module;
use ReflectionClass;
use Validate;
use Tools;

abstract class AbstractForm
{
    /** @var Module */
    protected $module;

    /** @var string */
    protected $legend;

    /** @var string */
    protected $icon = 'cog';

    /** @var string */
    private $name;

    public function __construct(Module $module)
    {
        $this->module = $module;
        $this->name = str_replace(' ', '', $module->displayName) . (new ReflectionClass($this))->getShortName();
    }

    public function __invoke(): string
    {
        $helper = new HelperForm();
        $context = Context::getContext();

        // Process prev form
        $resultProcess = $this->process();

        // Module, token and current index
        $helper->module = $this->module;
        $helper->name_controller = $this->module->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->module->name;

        // Language
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ?: 0;

        // Field values
        $helper->fields_value = $this->getValues();

        // Title and toolbar
        $helper->title = $this->module->displayName;
        $helper->show_toolbar = false;
        $helper->toolbar_scroll = false;
        $helper->submit_action = $this->name . 'Submit';

        $helper->tpl_vars = [
            'languages' => $context->controller->getLanguages(),
            'id_language' => $context->language->id,
        ];

        return $resultProcess . $helper->generateForm($this->form());
    }

    public function getDefaults(): array
    {
        return [];
    }

    protected function getValues(): array
    {
        $values = [];

        foreach ($this->getFields() as $name => $field) {
            $default = $field['default'] ?? ($this->getDefaults()[$name] ?? null);

            if (isset($field['lang']) && $field['lang']) {
                $temp = Configuration::getConfigInMultipleLangs($name);
                foreach ($temp as $idLang => $value) {
                    if (empty($value) && isset($default[$idLang])) {
                        $values[$name][$idLang] = $default[$idLang];
                    } else {
                        $values[$name][$idLang] = $value;
                    }
                }
            } else {
                $values[$name] = Configuration::get(
                    $name,
                    null,
                    null,
                    null,
                    isset($field['default']) ? $field['default'] : ($this->getDefaults()[$name] ?? null)
                );
            }
        }

        return $values;
    }

    protected function validate(): bool
    {
        $result = true;

        foreach ($this->getFields() as $name => $field) {
            $value = Tools::getValue($name, Configuration::get($name));

            if (isset($field['required']) && !$field['required'] && empty($value) || $field['type'] == 'switch') {
                continue;
            }

            if (isset($field['validate']) && !call_user_func([Validate::class, $field['validate']], $value)) {
                return false;
            }

            $result &= $result
                && !is_null($value)
                && $value !== '';
        }

        return $result;
    }

    protected function update(): string
    {
        $result = true;
        $langs = Context::getContext()->controller->getLanguages();

        foreach ($this->getFields() as $name => $settings) {
            if (isset($settings['lang']) && $settings['lang']) {
                $values = [];

                foreach ($langs as $lang) {
                    $values[$lang['id_lang']] = Tools::getValue($name . '_' . $lang['id_lang'], Configuration::get($name, $lang['id_lang']));
                }

                $result = $result && Configuration::updateValue($name, $values, $settings['html'] ?? false);
            } else {
                $value = Tools::getValue($name, Configuration::get($name));
                $result = $result && Configuration::updateValue($name, trim($value));
            }
        }

        if (!$result) {
            return $this->module->displayError(
                $this->trans('Could not update configuration!', [], 'Modules.Recontactform.Module')
            );
        }

        return $this->module->displayConfirmation(
            $this->trans('Configuration was successfully updated!', [], 'Modules.Recontactform.Module')
        );
    }

    protected function trans(string $key, array $parameters = [], $domain = null, $locale = null): string
    {
        return $this->module->getTranslator()->trans($key, $parameters, $domain, $locale);
    }

    abstract protected function getLegend(): string;

    abstract protected function getFields(): array;

    private function process(): string
    {
        if (!\Tools::isSubmit($this->name . 'Submit')) {
            return '';
        }

        if ($this->validate()) {
            return $this->update();
        }

        $text = implode(' ', [
            $this->trans('Form field values doesn\'t meet the validation criteria.', [], 'Modules.Recontactform.Module'),
            $this->trans('Are you sure all values are correct?', [], 'Modules.Recontactform.Module'),
        ]);

        return $this->module->displayError($text);
    }

    private function form(): array
    {
        $form = [
            'form' => [
                'id_form' => strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $this->name)),
                'legend' => [
                    'title' => $this->getLegend(),
                    'icon' => 'icon-' . $this->icon,
                ],
                'input' => [],
                'submit' => [
                    'title' => $this->trans('Save', [], 'Modules.Recontactform.Module'),
                ],
            ],
        ];

        foreach ($this->getFields() as $name => $field) {
            $field['name'] = $name;
            $field['desc'] = isset($field['desc']) ? implode(' ', (array) $field['desc']) : null;

            $form['form']['input'][] = $field;
        }

        return ['form' => $form];
    }
}
