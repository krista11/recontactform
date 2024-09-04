<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    UAB "Reprezentuok" <info@reprezentuok.lt>
 * @copyright Reprezentuok, UAB, 2023
 * @license   https://opensource.org/licenses/AFL-3.0
 */

namespace Re\ContactForm\Module\Classes;

use ObjectModel;

class Contact extends ObjectModel
{
    public $id_contact;

    public $type;

    public $name;

    public $city;

    public $phone;

    public $email;

    public $square;

    public $date;

    public $file_name;

    public $info;

    public $date_add;

    public static $definition = [
        'table' => 're_contact_form',
        'primary' => 'id_contact',
        'fields' => [
            'type' => ['type' => self::TYPE_INT],
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isCustomerName', 'size' => 255],
            'city' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255],
            'phone' => ['type' => self::TYPE_STRING, 'validate' => 'isPhoneNumber', 'size' => 32],
            'email' => ['type' => self::TYPE_STRING, 'validate' => 'isEmail', 'size' => 255],
            'square' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255],
            'date' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'file_name' => ['type' => self::TYPE_STRING, 'size' => 18],
            'info' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 512],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
        ],
    ];
}
