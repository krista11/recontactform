<?php

class AdminReContactController extends ModuleAdminController
{
    /** @var bool Is bootstrap used */
    public $bootstrap = true;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->className = 'Re\ContactForm\Module\Classes\Contact';
        $this->table = 're_contact_form';
        $this->identifier = 'id_contact';

        parent::__construct();

        $this->initList();
    }

    public function initContent()
    {
        if (in_array($this->display, ['add', 'edit'])) {
            $this->initForm();
        } else if ($this->display == 'view') {
            $contact = $this->loadObject();
            $image = '';

            if ($contact->file_name) {
                $path = _PS_UPLOAD_DIR_ . $contact->file_name;
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
            $this->context->smarty->assign([
                'contact' => $contact,
                'type' => $contact->type == 1 ? $this->trans('Normal form', [], 'Modules.Recontactform.Shop') : $this->trans('Get the master\'s recommendation', [], 'Modules.Recontactform.Shop'),
                'image' => $base64,
            ]);

            $this->content = $this->context->smarty->fetch($this->module->getLocalPath() . '/views/templates/admin/contact.tpl');
        }

        parent::initContent();
    }

    private function initList()
    {
        $this->fields_list = [
            'id_contact' => [
                'title' => $this->trans('ID', [], 'Admin.Global'),
                'align' => 'center',
                'filter_key' => 'a!id_contact',
                'class' => 'fixed-width-xs',
            ],
            'name' => [
                'title' => $this->trans('Title', [], 'Admin.Global'),
                'filter_key' => 'a!name',
            ],
            'phone' => [
                'title' => $this->trans('Phone', [], 'Admin.Global'),
                'filter_key' => 'a!phone',
            ],
            'email' => [
                'title' => $this->trans('Email', [], 'Admin.Global'),
                'filter_key' => 'a!email',
            ],
        ];

        $this->actions = ['view', 'edit', 'delete'];
    }

    public function initForm()
    {
        if (!$this->loadObject(true)) {
            return;
        }

        $this->fields_form = [
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->trans('Name', [], 'Admin.Global'),
                    'name' => 'name',
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Phone', [], 'Admin.Global'),
                    'name' => 'phone',
                ],
            ],
            'submit' => [
                'title' => $this->trans('Save', [], 'Admin.Global'),
            ],
        ];

    }
}
