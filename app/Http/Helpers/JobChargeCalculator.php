<?php

namespace App\Helpers;

class JobChargeCalculator
{
    /**
     * Calculate the service charge percentage based on category and invoice value
     * 
     * @param string $category 'import' or 'export'
     * @param float $invoiceValueUSD The invoice value in USD
     * @return array ['percentage' => float, 'amount' => float, 'imp_exp_value' => float]
     */
    public static function calculate($category, $invoiceValueUSD, $impExpValue = null)
    {
        // If imp_exp_value not provided, use invoice value
        $value = $impExpValue ?? $invoiceValueUSD;

        $percentage = 0;
        $categoryLower = strtolower($category);

        // IMPORT Category
        if (str_contains($categoryLower, 'import')) {
            if ($value <= 20000) {
                $percentage = 0.13;
            } elseif ($value <= 50000) {
                $percentage = 0.12;
            } elseif ($value <= 100000) {
                $percentage = 0.09;
            } else {
                $percentage = 0.07;
            }
        }
        // EXPORT Category
        elseif (str_contains($categoryLower, 'export')) {
            if ($value <= 50000) {
                $percentage = 0.11;
            } elseif ($value <= 100000) {
                $percentage = 0.08;
            } else {
                $percentage = 0.06;
            }
        }

        elseif (str_contains($categoryLower, '')) {
            if ($value <= 50000) {
                $percentage = 0.11;
            } elseif ($value <= 100000) {
                $percentage = 0.08;
            } else {
                $percentage = 0.06;
            }
        }

        // Calculate the actual amount
        $serviceCharge = ($value * $percentage) / 100;

        return [
            'percentage' => $percentage,
            'amount' => round($serviceCharge, 2),
            'imp_exp_value' => $value,
            'category' => $category,
        ];
    }
}
