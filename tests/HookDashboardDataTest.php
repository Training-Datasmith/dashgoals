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
class HookDashboardDataTest extends DashgoalsTestCase
{
    private function seedYearGoals()
    {
        Configuration::updateValue('PS_DASHGOALS_CURRENT_YEAR', 2020);
        ConfigurationKPI::updateValue('DASHGOALS_TRAFFIC_01_1971', 222);
        ConfigurationKPI::updateValue('DASHGOALS_TRAFFIC_01_2020', 111);
        ConfigurationKPI::updateValue('DASHGOALS_TRAFFIC_01_2024', 333);
        ConfigurationKPI::updateValue('DASHGOALS_TRAFFIC_01_2998', 444);
        for ($m = 1; $m <= 12; ++$m) {
            $mm = sprintf('%02d', $m);
            ConfigurationKPI::updateValue('DASHGOALS_CONVERSION_' . $mm . '_2020', 2);
            ConfigurationKPI::updateValue('DASHGOALS_AVG_CART_VALUE_' . $mm . '_2020', 40);
        }
        Configuration::updateValue('PS_DASHBOARD_SIMULATION', false);
    }

    public function testWrapperShape()
    {
        $this->seedYearGoals();
        $payload = $this->module->hookDashboardData(['extra' => 2024]);
        $this->assertEquals('bar_chart_goals', $payload['data_chart']['dash_goals_chart1']['chart_type']);
        $byKey = ChartTestSupport::seriesByKey($payload['data_chart']['dash_goals_chart1']);
        $this->assertEquals(333, ChartTestSupport::monthPoint($byKey['traffic_real'], 0)['goal']);
    }

    /**
     * @dataProvider validExtraYears
     */
    public function testExtraYearInsideRange($extra, $expectedGoal, $yearPrefix)
    {
        $this->seedYearGoals();
        $payload = $this->module->hookDashboardData(['extra' => $extra]);
        $this->assertEquals(
            [false, $yearPrefix . '-01-01', $yearPrefix . '-12-31', 'month'],
            AdminStatsController::$visitsCalls[0]
        );
        $byKey = ChartTestSupport::seriesByKey($payload['data_chart']['dash_goals_chart1']);
        $this->assertEquals($expectedGoal, ChartTestSupport::monthPoint($byKey['traffic_real'], 0)['goal']);
    }

    public function validExtraYears()
    {
        return [
            [1971, 222, '1971'],
            [2998, 444, '2998'],
            ['2024', 333, '2024'],
        ];
    }

    /**
     * @dataProvider invalidExtraYears
     */
    public function testExtraOutsideRangeFallsBackToConfiguredYear($extra)
    {
        $this->seedYearGoals();
        $params = [];
        if ($extra !== '__missing__') {
            $params['extra'] = $extra;
        }
        $payload = $this->module->hookDashboardData($params);
        $byKey = ChartTestSupport::seriesByKey($payload['data_chart']['dash_goals_chart1']);
        $this->assertEquals(111, ChartTestSupport::monthPoint($byKey['traffic_real'], 0)['goal']);
        $this->assertEquals(
            [false, '2020-01-01', '2020-12-31', 'month'],
            AdminStatsController::$visitsCalls[0]
        );
    }

    public function invalidExtraYears()
    {
        return [
            [1970],
            [2999],
            ['abc'],
            [''],
            [false],
            ['__missing__'],
        ];
    }
}
