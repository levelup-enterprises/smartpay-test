<?php

namespace App\Service;

use Exception;

class LoanCalculator
{
	
	/**
	 * @param  int  $principal
	 * @param  float  $interestRate
	 * @param  int  $term
	 *
	 * @return float
	 * @throws Exception
	 */
	public function getMonthlyPayment(int $principal, float $interestRate, int $term): float
	{
		//	Check for valid term
		if ($term < 0) {
			throw new Exception('invalid term');
		}
		
		if ($interestRate < 0) {
			throw new Exception('invalid Interest rate');
		}
		
		if ($principal < 0) {
			throw new Exception('invalid principal amount');
		}
		
		//  special case when full principal needs would be due immediately (prevents division by zero)
		if ($term == 0) {
			return $principal;
		}
		
		if ($interestRate > 0) {
			//	Get equivalent monthly interest rate as a multiplier
			$monthlyInterestRate = $interestRate / (12 * 100);
			
			$monthly_payment = $principal * ($monthlyInterestRate / (1 - pow((1 + $monthlyInterestRate), -$term)));
		} else {
			$monthly_payment = $principal / $term;
		}
		
		return round($monthly_payment, 2);
	}
	
}
