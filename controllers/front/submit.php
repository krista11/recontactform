<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    UAB "Reprezentuok" <info@reprezentuok.lt>
 * @copyright Reprezentuok, UAB, 2023
 * @license   https://opensource.org/licenses/AFL-3.0
 */
if (!defined('_PS_VERSION_')) {
    return;
}

use Re\ContactForm\Module\Classes\Contact;

class ReContactFormSubmitModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        $eicaptcha = Module::getInstanceByName('eicaptcha');

        if ($eicaptcha && $eicaptcha->active) {
            if (!$eicaptcha->hookActionValidateCaptcha()) {
                $this->exitWithResponse([
                    'success' => false,
                    'message' => $this->trans('reCaptcha verification failed.', [], 'Module.Recontactform.Shop'),
                ]);
            }
        }

        try {
            $data = Tools::getAllValues();
            if (isset($data['action']) && $data['action'] === 'submitContact') {
                $message = $this->validateContact($data);

                if ($message === true) {
                    $this->exitWithResponse([
                        'success' => true,
                        'message' => $this->trans('Thank you for your request.', [], 'Module.Recontactform.Shop'),
                    ]);
                } else {
                    $this->exitWithResponse([
                        'success' => false,
                        'message' => $message,
                    ]);
                }

            } else {
                $this->exitWithResponse([
                    'success' => false,
                    'message' => $this->trans('An error occurred while saving request.', [], 'Module.Recontactform.Shop'),
                ]);
            }

        } catch (Exception $exception) {
            $this->exitWithResponse([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);
        }

        exit;
    }

    private function validateContact($data)
    {
        $error = '';
        $extension = ['.png', '.jpeg', '.jpg'];

        $contact = new Contact();
        $contact->type = $this->module::FORM_TYPES[$data['formType']] ?? 0;
        $contact->name = $data['name'] ?? '';
        $contact->city = $data['city'] ?? '';
        $contact->phone = $data['phone'] ?? '';
        $contact->email = $data['email'] ?? '';
        $contact->square = $data['square'] ?? '';
        $contact->date = $data['date'] ?? '';
        $contact->info = $data['information'] ?? '';

        $fileAttachment = Tools::fileAttachment('file-upload');

        if (!empty($fileAttachment['name']) && $fileAttachment['error'] != 0) {
            $error = $this->trans('An error occurred during the file-upload process.', [], 'Modules.Recontactform.Shop');
        }

        if (!empty($fileAttachment['name']) &&
            !in_array(Tools::strtolower(Tools::substr($fileAttachment['name'], -4)), $extension) &&
            !in_array(Tools::strtolower(Tools::substr($fileAttachment['name'], -5)), $extension)
        ) {
            $error = $this->trans('Bad file extension', [], 'Modules.Recontactform.Shop');
        }

        $testFileUpload = (isset($fileAttachment['rename']) && !empty($fileAttachment['rename']));
        if ($testFileUpload && rename($fileAttachment['tmp_name'], _PS_UPLOAD_DIR_ . basename($fileAttachment['rename']))) {
            $contact->file_name = $fileAttachment['rename'];
        }

        if (empty($errors) && $contact->validateFields(false)) {
            if ($contact->file_name) {
                @chmod(_PS_UPLOAD_DIR_ . basename($fileAttachment['rename']), 0664);
            }

            $contact->save();
            return true;
        }

        return $this->trans('Incorrectly filled out form. Try to fix it and try again', [], 'Modules.Recontactform.Shop');
    }

    private function exitWithResponse(array $response = [])
    {
        ob_end_clean();
        header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        header('Content-Type: application/json;charset=utf-8');
        header('X-Robots-Tag: noindex, nofollow');

        if (!empty($response)) {
            echo json_encode($response, JSON_UNESCAPED_SLASHES);
        }

        exit;
    }
}
