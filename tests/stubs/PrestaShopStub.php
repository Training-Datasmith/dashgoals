<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 */
class PrestaShopStub
{
    public static function reset()
    {
        Configuration::$store = [];
        ConfigurationKPI::$store = [];
        Tools::$submit = [];
        Tools::$values = [];
        Tab::$lastAdded = null;
        Tab::$deletedIds = [];
        Tab::$addResult = true;
        Tab::$idMap = [];
        Module::$installResult = true;
        Module::$uninstallResult = true;
        Module::$registerHookResults = [];
        Module::$registeredHooks = [];
        Module::$displayCalls = [];
        AdminStatsController::$visitsCalls = [];
        AdminStatsController::$ordersCalls = [];
        AdminStatsController::$salesCalls = [];
        AdminStatsController::$visitsSeries = [];
        AdminStatsController::$ordersSeries = [];
        AdminStatsController::$salesSeries = [];
        AdminDashboardController::$jsPaths = [];
        Context::$instance = null;
        $_GET = [];
        $_POST = [];
    }
}

class Module
{
    public $name;
    public $context;
    public $_path;
    public static $installResult = true;
    public static $uninstallResult = true;
    public static $registerHookResults = [];
    public static $registeredHooks = [];
    public static $displayCalls = [];

    public function __construct()
    {
        $this->context = Context::getContext();
        if ($this->name) {
            $this->_path = '/modules/' . $this->name . '/';
        }
    }

    public function trans($id, $parameters = [], $domain = null, $locale = null)
    {
        return $id;
    }

    public function install()
    {
        return self::$installResult;
    }

    public function uninstall()
    {
        return self::$uninstallResult;
    }

    public function registerHook($hookName)
    {
        if (isset(self::$registerHookResults[$hookName])) {
            $result = self::$registerHookResults[$hookName];
        } else {
            $result = true;
        }
        if ($result) {
            self::$registeredHooks[] = $hookName;
        }

        return $result;
    }

    public function display($file, $template)
    {
        self::$displayCalls[] = ['file' => $file, 'template' => $template];

        return $template;
    }
}

class Context
{
    public $controller;
    public $currency;
    public $link;
    public $smarty;

    /** @var Context */
    public static $instance;

    public static function getContext()
    {
        if (!self::$instance) {
            self::$instance = new self();
            self::$instance->currency = new stdClass();
            self::$instance->currency->iso_code = 'EUR';
            self::$instance->currency->sign = '€';
            self::$instance->currency->format = 1;
            self::$instance->currency->blank = 0;
            self::$instance->link = new Link();
            self::$instance->smarty = new SmartyStub();
        }

        return self::$instance;
    }
}

class Link
{
    public function getAdminLink($controller)
    {
        return 'index.php?controller=' . $controller;
    }
}

class SmartyStub
{
    public $lastAssign = [];

    public function assign($data)
    {
        $this->lastAssign = $data;
    }
}

class Configuration
{
    public static $store = [];

    public static function updateValue($key, $value)
    {
        self::$store[$key] = $value;
    }

    public static function get($key)
    {
        return array_key_exists($key, self::$store) ? self::$store[$key] : false;
    }
}

class ConfigurationKPI
{
    public static $store = [];

    public static function updateValue($key, $value)
    {
        self::$store[$key] = $value;
    }

    public static function get($key)
    {
        return array_key_exists($key, self::$store) ? self::$store[$key] : false;
    }
}

class Tools
{
    public static $submit = [];
    public static $values = [];

    public static function strtoupper($string)
    {
        return strtoupper($string);
    }

    public static function isSubmit($key)
    {
        return !empty(self::$submit[$key]);
    }

    public static function getValue($key, $default = false)
    {
        return array_key_exists($key, self::$values) ? self::$values[$key] : $default;
    }
}

class Tab
{
    public $id;
    public $active;
    public $class_name;
    public $name = [];
    public $id_parent;
    public $module;

    public function __construct($id = null)
    {
        $this->id = $id;
    }

    public static $lastAdded = null;
    public static $deletedIds = [];
    public static $addResult = true;
    public static $idMap = [];

    public function add()
    {
        self::$lastAdded = [
            'active' => $this->active,
            'class_name' => $this->class_name,
            'name' => $this->name,
            'id_parent' => $this->id_parent,
            'module' => $this->module,
        ];

        return self::$addResult;
    }

    public function delete()
    {
        self::$deletedIds[] = $this->id;

        return true;
    }

    public static function getIdFromClassName($className)
    {
        return isset(self::$idMap[$className]) ? (int) self::$idMap[$className] : 0;
    }
}

class Language
{
    public static function getLanguages($active = true)
    {
        return [
            ['id_lang' => 1],
            ['id_lang' => 2],
        ];
    }
}

class AdminStatsController
{
    public static $visitsCalls = [];
    public static $ordersCalls = [];
    public static $salesCalls = [];
    public static $visitsSeries = [];
    public static $ordersSeries = [];
    public static $salesSeries = [];

    public static function getVisits($unique, $dateFrom, $dateTo, $granularity = false)
    {
        self::$visitsCalls[] = [$unique, $dateFrom, $dateTo, $granularity];

        return self::$visitsSeries;
    }

    public static function getOrders($dateFrom, $dateTo, $granularity = false)
    {
        self::$ordersCalls[] = [$dateFrom, $dateTo, $granularity];

        return self::$ordersSeries;
    }

    public static function getTotalSales($dateFrom, $dateTo, $granularity = false)
    {
        self::$salesCalls[] = [$dateFrom, $dateTo, $granularity];

        return self::$salesSeries;
    }
}

class ModuleAdminController
{
    public $module;
    public $context;

    public function __construct()
    {
        $this->context = Context::getContext();
    }
}

class AdminDashboardController
{
    public static $jsPaths = [];

    public function addJs($path)
    {
        AdminDashboardController::$jsPaths[] = $path;
    }
}

class AdminOtherController
{
}
