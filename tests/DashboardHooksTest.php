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

class DashboardHooksTest extends DashgoalsTestCase
{
    public function testZoneTwoAssignsSmartyAndRendersTheTemplate()
    {
        Configuration::updateValue('PS_DASHGOALS_CURRENT_YEAR', 2024);
        ConfigurationKPI::updateValue('DASHGOALS_TRAFFIC_01_2024', 111);

        $html = $this->module->hookDashboardZoneTwo(array());
        $this->assertEquals('dashboard_zone_two.tpl', $html);
        $assign = Context::getContext()->smarty->lastAssign;
        $this->assertEquals(2024, $assign['goals_year']);
        $this->assertEquals(111, $assign['goals_months']['01_2024']['values']['traffic']);
        $this->assertEquals('index.php?controller=AdminDashgoals', $assign['dashgoals_ajax_link']);
    }

    public function testZoneTwoPersistsGoalsWhenTheDashboardFormWasSubmitted()
    {
        Configuration::updateValue('PS_DASHGOALS_CURRENT_YEAR', 2024);
        Tools::$submit['submitDashGoals'] = true;
        Tools::$values['dashgoals_traffic_01_2024'] = 222;
        Tools::$values['dashgoals_conversion_01_2024'] = 3;
        Tools::$values['dashgoals_avg_cart_value_01_2024'] = 55;
        for ($m = 2; $m <= 12; $m++) {
            $mm = sprintf('%02d', $m);
            Tools::$values['dashgoals_traffic_' . $mm . '_2024'] = 100;
            Tools::$values['dashgoals_conversion_' . $mm . '_2024'] = 2;
            Tools::$values['dashgoals_avg_cart_value_' . $mm . '_2024'] = 40;
        }

        $this->module->hookDashboardZoneTwo(array());
        $this->assertEquals(222.0, ConfigurationKPI::$store['DASHGOALS_TRAFFIC_01_2024']);
    }

    public function testMediaHookAddsJavascriptOnlyForTheDashboardController()
    {
        Context::getContext()->controller = new AdminDashboardController();
        $this->module->hookActionAdminControllerSetMedia();
        $this->assertEquals(array('/modules/dashgoals/views/js/dashgoals.js'), AdminDashboardController::$jsPaths);

        PrestaShopStub::reset();
        $this->module = new dashgoals();
        Context::getContext()->controller = new AdminOtherController();
        $this->module->hookActionAdminControllerSetMedia();
        $this->assertCount(0, AdminDashboardController::$jsPaths);
    }
}
