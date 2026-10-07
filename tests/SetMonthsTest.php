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

class SetMonthsTest extends DashgoalsTestCase
{
    public function testReadsTwelveMonthsWithoutWriting()
    {
        ConfigurationKPI::updateValue('DASHGOALS_TRAFFIC_01_2024', 111);
        ConfigurationKPI::updateValue('DASHGOALS_CONVERSION_01_2024', 1.5);
        ConfigurationKPI::updateValue('DASHGOALS_AVG_CART_VALUE_01_2024', 40);
        ConfigurationKPI::updateValue('DASHGOALS_TRAFFIC_12_2024', 999);
        $before = ConfigurationKPI::$store;

        $months = $this->module->setMonths(2024);

        $this->assertEquals($before, ConfigurationKPI::$store);
        $this->assertCount(12, $months);
        $this->assertEquals('January', $months['01_2024']['label']);
        $this->assertEquals(111, $months['01_2024']['values']['traffic']);
        $this->assertEquals(999, $months['12_2024']['values']['traffic']);
        $this->assertFalse($months['02_2024']['values']['traffic']);
    }

    public function testSubmitWritesFloatsAndRereadsThem()
    {
        Tools::$submit['submitDashGoals'] = true;
        for ($m = 1; $m <= 12; $m++) {
            $mm = sprintf('%02d', $m);
            $prefix = $mm . '_2024';
            Tools::$values['dashgoals_traffic_' . $prefix] = 100 + $m;
            Tools::$values['dashgoals_conversion_' . $prefix] = 1.5;
            Tools::$values['dashgoals_avg_cart_value_' . $prefix] = 40;
        }

        $months = $this->module->setMonths(2024);
        $this->assertEquals(101.0, ConfigurationKPI::$store['DASHGOALS_TRAFFIC_01_2024']);
        $this->assertEquals(101, $months['01_2024']['values']['traffic']);

        Tools::$submit = array();
        $monthsAgain = $this->module->setMonths(2024);
        $this->assertEquals(101, $monthsAgain['01_2024']['values']['traffic']);
    }
}
