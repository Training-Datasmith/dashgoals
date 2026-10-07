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
        Configuration::$store = array();
        ConfigurationKPI::$store = array();
        Tools::$submit = array();
        Tools::$values = array();
        Tab::$lastAdded = null;
        Tab::$deletedIds = array();
        Tab::$addResult = true;
        Tab::$idMap = array();
        Module::$installResult = true;
        Module::$uninstallResult = true;
        Module::$registerHookResults = array();
        Module::$registeredHooks = array();
        Module::$displayCalls = array();
        AdminStatsController::$visitsCalls = array();
        AdminStatsController::$ordersCalls = array();
        AdminStatsController::$salesCalls = array();
        AdminStatsController::$visitsSeries = array();
        AdminStatsController::$ordersSeries = array();
        AdminStatsController::$salesSeries = array();
        AdminDashboardController::$jsPaths = array();
        Context::$instance = null;
        $_GET = array();
        $_POST = array();
    }
}

class Module
{
    public $name;
    public $context;
    public $_path;
    public static $installResult = true;
    public static $uninstallResult = true;
    public static $registerHookResults = array();
    public static $registeredHooks = array();
    public static $displayCalls = array();

    public function __construct()
    {
        $this->context = Context::getContext();
        if ($this->name) {
            $this->_path = '/modules/' . $this->name . '/';
        }
    }

    public function trans($id, $parameters = array(), $domain = null, $locale = null)
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
        self::$displayCalls[] = array('file' => $file, 'template' => $template);

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
    public $lastAssign = array();

    public function assign($data)
    {
        $this->lastAssign = $data;
    }
}

class Configuration
{
    public static $store = array();

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
    public static $store = array();

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
    public static $submit = array();
    public static $values = array();

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
    public $active;
    public $class_name;
    public $name = array();
    public $id_parent;
    public $module;

    public static $lastAdded = null;
    public static $deletedIds = array();
    public static $addResult = true;
    public static $idMap = array();

    public function add()
    {
        self::$lastAdded = array(
            'active' => $this->active,
            'class_name' => $this->class_name,
            'name' => $this->name,
            'id_parent' => $this->id_parent,
            'module' => $this->module,
        );

        return self::$addResult;
    }

    public function delete()
    {
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
        return array(
            array('id_lang' => 1),
            array('id_lang' => 2),
        );
    }
}

class AdminStatsController
{
    public static $visitsCalls = array();
    public static $ordersCalls = array();
    public static $salesCalls = array();
    public static $visitsSeries = array();
    public static $ordersSeries = array();
    public static $salesSeries = array();

    public static function getVisits($unique, $dateFrom, $dateTo, $granularity = false)
    {
        self::$visitsCalls[] = array($unique, $dateFrom, $dateTo, $granularity);

        return self::$visitsSeries;
    }

    public static function getOrders($dateFrom, $dateTo, $granularity = false)
    {
        self::$ordersCalls[] = array($dateFrom, $dateTo, $granularity);

        return self::$ordersSeries;
    }

    public static function getTotalSales($dateFrom, $dateTo, $granularity = false)
    {
        self::$salesCalls[] = array($dateFrom, $dateTo, $granularity);

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
    public static $jsPaths = array();

    public function addJs($path)
    {
        AdminDashboardController::$jsPaths[] = $path;
    }
}

class AdminOtherController
{
}
