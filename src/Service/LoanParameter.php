<?php

namespace App\Service;

use Exception;

class LoanParameter
{
	//  In a real application these options would come from either the database or another component, for the purpose of this exercise repeating them helps to keep the scope limited
	private $creditOptions = ["poor", "average", "good", "excellent"];

	/**
	 * returns the max loan amount for their credit rating
	 *
	 * @throws Exception
	 */
	public function getMaxAmount(string $credit): float
	{
		if (!in_array($credit, $this->creditOptions)) {
			throw new Exception("invalid credit option");
		}

		//  Poor credit has a limit of $5000
		if ($credit == "poor") {
			return 5000;
		}

		//  For everyone else, $75,000
		return 75000;
	}

	/**
	 * @throws Exception
	 */
	public function getInterestRate(int $term, string $credit): float
	{
		if (!in_array($credit, $this->creditOptions)) {
			throw new Exception("invalid credit option");
		}

		if ($term < 12 || $term > 60) {
			throw new Exception("invalid term option");
		}

		if ($credit == "average") {
			return 22.5;
		} elseif ($credit == "good") {
			return 15.5;
		} elseif ($credit == "excellent") {
			if ($term == 12) {
				return 6.99;
			} elseif ($term <= 24) {
				return 9.99;
			} elseif ($term == 36) {
				return 12;
			} else {
				return 15;
			}
		}

		return 25;
	}

	/**
	 * Origination fee is 5% of the first $5000, and then 2% of the rest, rounded to the nearest dollar
	 *
	 * @param  int  $amount
	 *
	 * @return int
	 * @throws Exception
	 */
	public function getOriginationFee(int $amount): int
	{
		if ($amount < 1) {
			throw new Exception("amount must be above zero");
		}

		if ($amount <= 5000) {
			return round($amount * 0.05);
		} else {
			return round(250 + ($amount - 5000) * 0.02);
		}
	}

	/**
	 * Get APR
	 *
	 * @param  int  $principle
	 * @param  int  $rate
	 * @param  int  $term
	 * @param  int  $fee
	 *
	 * @return int
	 * @throws Exception
	 */
	public function getAPR(int $principle, int $rate, int $term, int $fee): int
	{
		if ($principle < 1) {
			throw new Exception("principle must be above zero");
		}

		// Get years in term
		$yrs = $term / 12;

		// Get simple interest accrued
		$interest = self::getInterestAccrued($principle, $rate, $yrs);

		return round((($fee + $interest) / $principle / $yrs) * 1 * 100, 2);
	}

	/**
	 * Check if the loan payment is not more than 15% of monthly income
	 *
	 * @param  int  $payment
	 * @param  int  $income
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function checkAffordability(int $payment, int $income): bool
	{
		if ($income < 1) {
			throw new Exception("amount must be above zero");
		}

		$percent = ($income / 100) * 15;

		if ($payment <= $percent) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get simple interest accrued for the term
	 *
	 * @param  int  $principle
	 * @param  float  $rate
	 * @param  int  $yrs
	 *
	 * @return float
	 */
	public static function getInterestAccrued(
		int $principle,
		float $rate,
		int $yrs
	): float {
		// Set rate as percentage
		$rate = round($rate / 100, 2);
		// Get total interest
		return round($principle * (1 + $rate * $yrs) - $principle, 2);
	}
}