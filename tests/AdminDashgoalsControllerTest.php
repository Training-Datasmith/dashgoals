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

class AdminDashgoalsControllerTest extends DashgoalsTestCase
{
    private function runChangeConfYearChild($year, $kpiSeed = array())
    {
        $resultFile = tempnam(sys_get_temp_dir(), 'dashgoals-change-year-');
        $this->assertNotFalse($resultFile);

        $script = realpath(dirname(__FILE__) . '/bin/change_conf_year.php');
        $kpiJson = json_encode($kpiSeed);
        $cmd = escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($script) . ' '
            . escapeshellarg($resultFile) . ' ' . escapeshellarg((string) $year) . ' '
            . escapeshellarg($kpiJson);

        $output = array();
        $exitCode = 0;
        exec($cmd . ' 2>&1', $output, $exitCode);

        $this->assertEquals(0, $exitCode, implode("\n", $output));
        $this->assertFileExists($resultFile);
        $payload = json_decode(file_get_contents($resultFile), true);
        $this->assertTrue(is_array($payload));
        unlink($resultFile);

        return $payload;
    }

    public function testChangeYearPersistsIntegerYearAndRendersConfig()
    {
        $payload = $this->runChangeConfYearChild(
            '2024',
            array('DASHGOALS_TRAFFIC_01_2024' => 333)
        );

        $this->assertSame(2024, $payload['year']);
        $this->assertEquals(333, $payload['kpi']['DASHGOALS_TRAFFIC_01_2024']);
        $this->assertEquals('config.tpl', $payload['display'][0]['template']);
        $this->assertEquals(2024, $payload['smarty']['goals_year']);
        $this->assertEquals(333, $payload['smarty']['goals_months']['01_2024']['values']['traffic']);
    }
}
