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
class GetValuesFromGoalsTest extends DashgoalsTestCase
{
    /** @var DashgoalsExposed */
    private $exposed;

    protected function setUp()
    {
        parent::setUp();
        $this->exposed = new DashgoalsExposed();
    }

    public function testUnderPerformance()
    {
        $zones = $this->exposed->exposeGetValuesFromGoals(100, 150, 75, 'January');
        $this->assertEquals(0.75, $zones['real']['y'], '', 0.001);
        $this->assertEquals(0.75, $zones['less']['y'], '', 0.001);
        $this->assertEquals(0, $zones['more']['y'], '', 0.001);
    }

    public function testExactFulfillment()
    {
        $zones = $this->exposed->exposeGetValuesFromGoals(100, 150, 150, 'January');
        $this->assertEquals(1.5, $zones['real']['y'], '', 0.001);
        $this->assertEquals(0, $zones['less']['y'], '', 0.001);
        $this->assertEquals(0, $zones['more']['y'], '', 0.001);
    }

    public function testOverPerformance()
    {
        $zones = $this->exposed->exposeGetValuesFromGoals(100, 150, 300, 'January');
        $this->assertEquals(1.5, $zones['real']['y'], '', 0.001);
        $this->assertEquals(0, $zones['less']['y'], '', 0.001);
        $this->assertEquals(1.5, $zones['more']['y'], '', 0.001);
    }

    public function testZeroValueStillDrawsTheRemainingGoal()
    {
        $zones = $this->exposed->exposeGetValuesFromGoals(100, 150, 0, 'January');
        $this->assertEquals(0, $zones['real']['y'], '', 0.001);
        $this->assertEquals(1.5, $zones['less']['y'], '', 0.001);
        $this->assertEquals(0, $zones['more']['y'], '', 0.001);
    }

    public function testZeroGoalYieldsFlatBars()
    {
        $zones = $this->exposed->exposeGetValuesFromGoals(100, 0, 10, 'January');
        $this->assertEquals(0, $zones['real']['y'], '', 0.001);
        $this->assertEquals(0, $zones['less']['y'], '', 0.001);
        $this->assertEquals(0, $zones['more']['y'], '', 0.001);
    }

    public function testZeroAverageYieldsFlatBars()
    {
        $zones = $this->exposed->exposeGetValuesFromGoals(0, 150, 75, 'January');
        $this->assertEquals(0, $zones['real']['y'], '', 0.001);
        $this->assertEquals(0, $zones['less']['y'], '', 0.001);
        $this->assertEquals(0, $zones['more']['y'], '', 0.001);
    }

    public function testFulfillmentRoundedToTwoDecimalsCountsAsExact()
    {
        $zones = $this->exposed->exposeGetValuesFromGoals(100, 100.4, 100, 'January');
        $this->assertEquals(1, $zones['real']['y'], '', 0.001);
        $this->assertEquals(0, $zones['less']['y'], '', 0.001);
        $this->assertEquals(0, $zones['more']['y'], '', 0.001);
    }
}
