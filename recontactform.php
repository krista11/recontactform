<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    UAB "Reprezentuok" <info@reprezentuok.lt>
 * @copyright Reprezentuok, UAB, 2023
 * @license   https://opensource.org/licenses/AFL-3.0
 */
use Module as PrestaModule;
use \PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use Re\ContactForm\Module;
use Re\ContactForm\Module\Forms;
use Re\ContactForm\Module\Hooks;

if (!defined('_PS_VERSION_')) {
    exit;
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

class ReContactForm extends PrestaModule implements WidgetInterface
{
    public const MODULE_ADMIN_CONTROLLER = 'AdminReContact';

    public const FORM_TYPES = [
        'normal' => 1,
        'master' => 2,
    ];

    // Hooks
    use Hooks\FrontOfficeHooks;

    /** @var array */
    public $hooks = [
        // Front office
        'displayHeader',
        'displayHome',
    ];

    /** @var array */
    public $forms = [
        Forms\FormSettings::class,
    ];

    public function __construct()
    {
        $this->name = 'recontactform';
        $this->version = '1.0.0';
        $this->author = 'Reprezentuok';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('Custom Contact Form', [],
            'Modules.Recontactform.Admin');
        $this->description = $this->trans('Custom Contact Form',
            [], 'Modules.Recontactform.Admin');
        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => _PS_VERSION_];

        $this->tabs = [
            self::MODULE_ADMIN_CONTROLLER => [
                'title' => $this->trans('Contacts', [], 'Modules.Recontactform.Module'),
                'parent_tab' => 24,
            ],
        ];

    }

    public function install(): bool
    {
        return parent::install()
            && (new Module\Installer($this))();
    }

    public function uninstall(): bool
    {
        return (new Module\Uninstaller($this))()
            && parent::uninstall();
    }

    public function getContent(): string
    {
        return (new Module\Configure($this))();
    }

    public function isUsingNewTranslationSystem(): bool
    {
        return true;
    }
}
