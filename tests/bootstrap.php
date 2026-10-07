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
error_reporting(-1);
date_default_timezone_set('UTC');

if (!defined('_PS_VERSION_')) {
    define('_PS_VERSION_', '1.7.8.11');
}
if (!defined('_PS_MODULE_DIR_')) {
    define('_PS_MODULE_DIR_', '/modules/');
}

require __DIR__ . '/stubs/PrestaShopStub.php';
require dirname(__DIR__) . '/dashgoals.php';
require dirname(__DIR__) . '/controllers/admin/AdminDashgoalsController.php';
require __DIR__ . '/support/DashgoalsExposed.php';
require __DIR__ . '/support/ExpectedChartMath.php';
require __DIR__ . '/support/ChartTestSupport.php';
if (class_exists('PHPUnit_Framework_TestCase', false)) {
    require __DIR__ . '/DashgoalsTestCase.php';
}
