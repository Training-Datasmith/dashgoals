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
class InstallUninstallTest extends DashgoalsTestCase
{
    public function testConstructorSetsModuleIdentity()
    {
        $this->assertEquals('dashgoals', $this->module->name);
        $this->assertEquals('administration', $this->module->tab);
        $this->assertEquals('2.0.4', $this->module->version);
        $this->assertEquals('PrestaShop', $this->module->author);
        $this->assertEquals('Dashboard Goals', $this->module->displayName);
        $this->assertEquals(
            'Enrich your stats: add a block with your store’s forecast to always be one step ahead!',
            $this->module->description
        );
        $this->assertEquals('1.7.1.0', $this->module->ps_versions_compliancy['min']);
        $this->assertEquals(_PS_VERSION_, $this->module->ps_versions_compliancy['max']);
    }

    public function testInstallWritesDefaultsTabAndHooks()
    {
        $year = date('Y');
        $this->assertTrue($this->module->install());
        $this->assertEquals($year, Configuration::get('PS_DASHGOALS_CURRENT_YEAR'));
        $this->assertEquals(600, ConfigurationKPI::get('DASHGOALS_TRAFFIC_01_' . $year));
        $this->assertEquals('AdminDashgoals', Tab::$lastAdded['class_name']);
        $this->assertEquals(
            ['dashboardZoneTwo', 'dashboardData', 'actionAdminControllerSetMedia'],
            Module::$registeredHooks
        );
    }

    public function testInstallPreservesExistingGoalsIncludingZero()
    {
        $year = date('Y');
        ConfigurationKPI::updateValue('DASHGOALS_TRAFFIC_01_' . $year, 0);
        ConfigurationKPI::updateValue('DASHGOALS_CONVERSION_01_' . $year, '0');
        ConfigurationKPI::updateValue('DASHGOALS_AVG_CART_VALUE_01_' . $year, 15);

        $this->module->install();

        $this->assertSame(0, ConfigurationKPI::get('DASHGOALS_TRAFFIC_01_' . $year));
        $this->assertSame('0', ConfigurationKPI::get('DASHGOALS_CONVERSION_01_' . $year));
        $this->assertSame(15, ConfigurationKPI::get('DASHGOALS_AVG_CART_VALUE_01_' . $year));
        $this->assertEquals(600, ConfigurationKPI::get('DASHGOALS_TRAFFIC_02_' . $year));
    }

    public function testInstallReturnsFalseWhenTabAddFails()
    {
        Tab::$addResult = false;
        $this->assertFalse($this->module->install());
        $this->assertCount(0, Module::$registeredHooks);
    }

    public function testInstallReturnsFalseWhenParentInstallFails()
    {
        Module::$installResult = false;
        $this->assertFalse($this->module->install());
        $this->assertCount(0, Module::$registeredHooks);
    }

    public function testInstallStopsWhenAHookFails()
    {
        Module::$registerHookResults['dashboardData'] = false;
        $this->assertFalse($this->module->install());
        $this->assertEquals(['dashboardZoneTwo'], Module::$registeredHooks);
    }

    public function testUninstallDeletesTheTabAndKeepsConfiguration()
    {
        Tab::$idMap['AdminDashgoals'] = 7;
        ConfigurationKPI::updateValue('DASHGOALS_TRAFFIC_01_2024', 50);
        Configuration::updateValue('PS_DASHGOALS_CURRENT_YEAR', 2024);

        $this->assertTrue($this->module->uninstall());
        $this->assertSame([7], Tab::$deletedIds);
        $this->assertEquals(50, ConfigurationKPI::get('DASHGOALS_TRAFFIC_01_2024'));
        $this->assertEquals(2024, Configuration::get('PS_DASHGOALS_CURRENT_YEAR'));
    }

    public function testUninstallSkipsDeleteWhenTheTabIsAbsent()
    {
        Tab::$idMap['AdminDashgoals'] = 0;
        $this->assertTrue($this->module->uninstall());
        $this->assertSame([], Tab::$deletedIds);
    }

    public function testUninstallReturnsFalseWhenParentFails()
    {
        Tab::$idMap['AdminDashgoals'] = 7;
        Module::$uninstallResult = false;
        $this->assertFalse($this->module->uninstall());
        $this->assertSame([7], Tab::$deletedIds);
    }
}
