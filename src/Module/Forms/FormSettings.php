<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    UAB "Reprezentuok" <info@reprezentuok.lt>
 * @copyright Reprezentuok, UAB, 2023
 * @license   https://opensource.org/licenses/AFL-3.0
 */

namespace Re\ContactForm\Module\Forms;

class FormSettings extends AbstractForm
{
    protected $icon = 'cog';

    protected function getLegend(): string
    {
        return $this->trans('Form settings', [], 'Modules.Recontactform.Module');
    }

    protected function getFields(): array
    {
        return [
            'RE_CONTACT_FORM_EXAMPLES_LINK' => [
                'type' => 'text',
                'label' => $this->trans('Link of examples', [], 'Modules.Recontactform.Module'),
                'lang' => true,
            ],
        ];
    }
}
