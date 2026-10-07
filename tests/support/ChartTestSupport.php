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

class ChartTestSupport
{
    public static function seriesByKey($chartData)
    {
        $map = array();
        foreach ($chartData['data'] as $series) {
            $map[$series['key']] = $series;
        }

        return $map;
    }

    public static function monthPoint($series, $monthIndex)
    {
        return $series['values'][$monthIndex];
    }

    public static function seed2024Grid()
    {
        $year = 2024;
        for ($m = 1; $m <= 12; $m++) {
            $mm = sprintf('%02d', $m);
            ConfigurationKPI::updateValue('DASHGOALS_TRAFFIC_' . $mm . '_' . $year, 1000);
            ConfigurationKPI::updateValue('DASHGOALS_CONVERSION_' . $mm . '_' . $year, 2.5);
            ConfigurationKPI::updateValue('DASHGOALS_AVG_CART_VALUE_' . $mm . '_' . $year, 40);
        }

        AdminStatsController::$visitsSeries = array(
            strtotime('2024-01-01') => 2000,
            strtotime('2024-02-01') => 500,
            strtotime('2024-04-01') => 1000,
        );
        AdminStatsController::$ordersSeries = array(
            strtotime('2024-01-01') => 40,
            strtotime('2024-02-01') => 5,
            strtotime('2024-04-01') => 25,
        );
        AdminStatsController::$salesSeries = array(
            strtotime('2024-01-01') => 5000,
            strtotime('2024-02-01') => 100,
            strtotime('2024-04-01') => 1000,
        );
        Configuration::updateValue('PS_DASHBOARD_SIMULATION', false);
    }
}
