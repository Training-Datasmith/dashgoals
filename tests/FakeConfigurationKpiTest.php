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

class FakeConfigurationKpiTest extends DashgoalsTestCase
{
    /** @var DashgoalsExposed */
    private $exposed;

    protected function setUp()
    {
        parent::setUp();
        $this->exposed = new DashgoalsExposed();
    }

    public function testTrafficScalesByMonth()
    {
        $this->assertEquals(3000, $this->exposed->exposeFakeConfigurationKpiGet('DASHGOALS_TRAFFIC_01_2030'));
        $this->assertEquals(4500, $this->exposed->exposeFakeConfigurationKpiGet('DASHGOALS_TRAFFIC_06_2030'));
        $this->assertEquals(6000, $this->exposed->exposeFakeConfigurationKpiGet('DASHGOALS_TRAFFIC_11_2030'));
        $this->assertEquals(3300, $this->exposed->exposeFakeConfigurationKpiGet('DASHGOALS_TRAFFIC_02_2030'), '', 0.001);
        $this->assertEquals(6300, $this->exposed->exposeFakeConfigurationKpiGet('DASHGOALS_TRAFFIC_12_2030'), '', 0.001);
        $this->assertEquals(
            $this->exposed->exposeFakeConfigurationKpiGet('DASHGOALS_TRAFFIC_01_2030'),
            $this->exposed->exposeFakeConfigurationKpiGet('DASHGOALS_TRAFFIC_01_1999')
        );
    }

    public function testConversionAndAverageCartAreFlat()
    {
        $this->assertEquals(2, $this->exposed->exposeFakeConfigurationKpiGet('DASHGOALS_CONVERSION_03_2030'));
        $this->assertEquals(90, $this->exposed->exposeFakeConfigurationKpiGet('DASHGOALS_AVG_CART_VALUE_07'));
    }

    public function testUnrelatedKeyReturnsNull()
    {
        $this->assertNull($this->exposed->exposeFakeConfigurationKpiGet('NOT_A_KEY'));
    }
}
