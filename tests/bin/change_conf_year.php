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

if ($argc < 3) {
    fwrite(STDERR, "Usage: change_conf_year.php <result-file> <year> [kpi-json]\n");
    exit(2);
}

$resultFile = $argv[1];
$year = $argv[2];
$kpiSeed = array();
if (isset($argv[3]) && $argv[3] !== '') {
    $kpiSeed = json_decode($argv[3], true);
    if (!is_array($kpiSeed)) {
        fwrite(STDERR, "Invalid KPI JSON\n");
        exit(2);
    }
}

require dirname(__DIR__) . '/bootstrap.php';

foreach ($kpiSeed as $key => $value) {
    ConfigurationKPI::updateValue($key, $value);
}
Configuration::updateValue('PS_DASHGOALS_CURRENT_YEAR', 2020);

Tools::$values['year'] = $year;

$module = new dashgoals();
$controller = new AdminDashgoalsController();
$controller->module = $module;
$controller->context = Context::getContext();

register_shutdown_function(function () use ($resultFile) {
    $payload = array(
        'year' => Configuration::get('PS_DASHGOALS_CURRENT_YEAR'),
        'kpi' => ConfigurationKPI::$store,
        'smarty' => Context::getContext()->smarty->lastAssign,
        'display' => Module::$displayCalls,
    );
    file_put_contents($resultFile, json_encode($payload));
});

$controller->ajaxProcessChangeConfYear();
