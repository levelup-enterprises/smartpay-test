<?php

namespace App\Service;

use App\Form\LoanType;
use App\Service\LoanParameter;
use Exception;
use DateTime;

class LoanCalculator
{
	private $amount;
	private $principal;
	private $interestRate;
	private $term;
	private $years;
	private $fee;
	private $payment;

	function __construct(int $amount, float $interestRate, int $term, int $fee)
	{
		$this->amount = $amount;
		$this->principal = $amount + $fee;
		$this->interestRate = $interestRate;
		$this->term = $term;
		$this->years = $term / 12;
		$this->fee = $fee;
	}

	/**
	 *
	 * @return float
	 * @throws Exception
	 */
	public function getMonthlyPayment(): float
	{
		//	Check for valid term
		if ($this->term < 0) {
			throw new Exception("invalid term");
		}

		if ($this->interestRate < 0) {
			throw new Exception("invalid Interest rate");
		}

		if ($this->principal < 0) {
			throw new Exception("invalid principal amount");
		}

		//  special case when full principal needs would be due immediately (prevents division by zero)
		if ($this->term == 0) {
			return $this->principal;
		}

		if ($this->interestRate > 0) {
			//	Get equivalent monthly interest rate as a multiplier
			$monthlyInterestRate = $this->interestRate / (12 * 100);

			$monthly_payment =
				$this->principal *
				($monthlyInterestRate /
					(1 - pow(1 + $monthlyInterestRate, -$this->term)));
		} else {
			$monthly_payment = $this->principal / $this->term;
		}

		return $this->payment = round($monthly_payment, 2);
	}

	/**
	 *
	 * @return array
	 * @throws Exception
	 */
	public function getPaymentSchedule()
	{
		$payments = [];

		// Build payments for each month in term
		for ($i = 0; $i < $this->term; $i++) {
			// Set date in 1 month increments
			$key = $i + 1;
			$date = new DateTime();
			$date = $date->modify("+${key} month")->format("m-01-Y");

			$balance = round($this->payment * ($this->term - $i), 2);

			// Get interest for current loan amount
			$interestTotal = LoanParameter::getInterestAccrued(
				$balance,
				$this->interestRate,
				$this->years
			);

			// Single payment values
			$interest = round($interestTotal / $this->term, 2);
			$principal = round($this->payment - $interest, 2);

			$payments[$key] = [
				"id" => $key,
				"date" => $date,
				"amount" => number_format($this->payment, 2, ".", ""),
				"interest" => number_format($interest, 2, ".", ""),
				"principal" => number_format($principal, 2, ".", ""),
				"balance" => number_format($balance, 2, ".", ""),
			];
		}
		// show each payment's ( amount, interest, principal, and remaining balance)
		return $payments;
	}
}