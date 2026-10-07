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

class ExpectedChartMath
{
    public static function zoneYs($averageGoal, $monthGoal, $value)
    {
        $real = 0.0;
        $less = 0.0;
        $more = 0.0;

        $fulfillment = 0.0;
        if ($value && $monthGoal) {
            $fulfillment = round($value / $monthGoal, 2);
        }

        $baseRate = 0.0;
        if ($averageGoal && $monthGoal) {
            $baseRate = $monthGoal / $averageGoal;
        }

        if ($fulfillment == 1) {
            $real = round($baseRate, 2);
        } elseif ($fulfillment < 1) {
            $real = round($fulfillment * $baseRate, 2);
            $less = round($baseRate - ($fulfillment * $baseRate), 2);
        } elseif ($fulfillment > 1) {
            $real = round($baseRate, 2);
            $more = round(($fulfillment * $baseRate) - $baseRate, 2);
        }

        return array('real' => $real, 'less' => $less, 'more' => $more);
    }

    public static function fakeTrafficGoal($month)
    {
        return 3000 * (1 + ((int) $month - 1) / 10);
    }

    public static function fakeSalesGoal($month)
    {
        return self::fakeTrafficGoal($month) * 2 / 100 * 90;
    }
}
