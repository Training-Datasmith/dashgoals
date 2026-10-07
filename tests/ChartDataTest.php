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

class ChartDataTest extends DashgoalsTestCase
{
    public function testNonSimulationJanuaryFebruaryAprilAndZeroMonths()
    {
        ChartTestSupport::seed2024Grid();
        $chart = $this->module->getChartData(2024);

        $this->assertEquals('bar_chart_goals', $chart['chart_type']);
        $this->assertCount(12, $chart['data']);
        foreach ($chart['data'] as $series) {
            $this->assertCount(12, $series['values']);
        }

        $byKey = ChartTestSupport::seriesByKey($chart);
        $this->assertEquals('traffic_real', $byKey['traffic_real']['key']);
        $this->assertEquals('Goal not reached', $byKey['traffic_less']['zone_text']);
        $this->assertEquals('Goal set:', $byKey['traffic_less']['empty_zone_text']);

        $janTraffic = ChartTestSupport::monthPoint($byKey['traffic_real'], 0);
        $this->assertEquals(1, $janTraffic['y'], '', 0.001);
        $this->assertEquals(100, $janTraffic['goal_diff'], '', 0.001);

        $janConv = ChartTestSupport::monthPoint($byKey['conversion_real'], 0);
        $this->assertEquals(-20, $janConv['goal_diff'], '', 0.001);

        $marTrafficLess = ChartTestSupport::monthPoint($byKey['traffic_less'], 2);
        $this->assertEquals(1000, $marTrafficLess['goal']);
        $this->assertArrayNotHasKey('goal_diff', ChartTestSupport::monthPoint($byKey['traffic_real'], 2));

        $this->assertEquals(
            array(false, '2024-01-01', '2024-12-31', 'month'),
            AdminStatsController::$visitsCalls[0]
        );
    }

    public function testMissingStatsAreZeroAndPresentGoalsRemain()
    {
        ChartTestSupport::seed2024Grid();
        AdminStatsController::$visitsSeries = array();
        AdminStatsController::$ordersSeries = array();
        AdminStatsController::$salesSeries = array();

        $chart = $this->module->getChartData(2024);
        $byKey = ChartTestSupport::seriesByKey($chart);
        $jan = ChartTestSupport::monthPoint($byKey['traffic_less'], 0);
        $this->assertEquals(1000, $jan['goal']);
        $this->assertEquals(1, $jan['y'], '', 0.001);
    }

    public function testSimulationUsesNonCurrentYearAndBoundedActuals()
    {
        $year = (int) date('Y') + 1;
        Configuration::updateValue('PS_DASHBOARD_SIMULATION', '1');

        $chart = $this->module->getChartData($year);
        $this->assertCount(0, AdminStatsController::$visitsCalls);

        $byKey = ChartTestSupport::seriesByKey($chart);
        for ($i = 0; $i < 12; $i++) {
            $traffic = ChartTestSupport::monthPoint($byKey['traffic_real'], $i);
            $this->assertGreaterThanOrEqual(2000, $traffic['traffic']);
            $this->assertLessThanOrEqual(5000, $traffic['traffic']);

            $conversion = ChartTestSupport::monthPoint($byKey['conversion_real'], $i);
            $this->assertGreaterThanOrEqual(0.8, $conversion['conversion']);
            $this->assertLessThanOrEqual(5, $conversion['conversion']);

            $cart = ChartTestSupport::monthPoint($byKey['avg_cart_value_real'], $i);
            $this->assertGreaterThanOrEqual(30, $cart['avg_cart_value']);
            $this->assertLessThanOrEqual(225, $cart['avg_cart_value']);

            $sales = ChartTestSupport::monthPoint($byKey['sales_real'], $i);
            $this->assertGreaterThanOrEqual(3000, $sales['sales']);
            $this->assertLessThanOrEqual(9000, $sales['sales']);
        }

        $janGoal = ExpectedChartMath::fakeTrafficGoal(1);
        $decGoal = ExpectedChartMath::fakeTrafficGoal(12);
        $this->assertEquals($janGoal, ChartTestSupport::monthPoint($byKey['traffic_real'], 0)['goal'], '', 0.001);
        $this->assertEquals($decGoal, ChartTestSupport::monthPoint($byKey['traffic_real'], 11)['goal'], '', 0.001);
        $this->assertEquals(
            ExpectedChartMath::fakeSalesGoal(1),
            ChartTestSupport::monthPoint($byKey['sales_real'], 0)['goal'],
            '',
            0.001
        );
    }

    public function testSimulationOffWhenFlagIsFalseZeroOrMissing()
    {
        ChartTestSupport::seed2024Grid();

        Configuration::updateValue('PS_DASHBOARD_SIMULATION', false);
        $this->module->getChartData(2024);
        $this->assertCount(1, AdminStatsController::$visitsCalls);

        PrestaShopStub::reset();
        ChartTestSupport::seed2024Grid();
        Configuration::updateValue('PS_DASHBOARD_SIMULATION', '0');
        $this->module = new dashgoals();
        $this->module->getChartData(2024);
        $this->assertCount(1, AdminStatsController::$visitsCalls);

        PrestaShopStub::reset();
        ChartTestSupport::seed2024Grid();
        $this->module = new dashgoals();
        $this->module->getChartData(2024);
        $this->assertCount(1, AdminStatsController::$visitsCalls);
    }
}
