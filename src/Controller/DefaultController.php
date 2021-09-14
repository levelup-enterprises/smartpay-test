<?php

namespace App\Controller;

use App\Form\LoanType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\LoanParameter;
use App\Service\LoanCalculator;

/**
 * @Route("/")
 */
class DefaultController extends AbstractController
{
	
	/**
	 * @Route("/", name="home")
	 */
	public function home(Request $request, LoanParameter $loanParameterService, LoanCalculator $loanCalculatorService): Response
	{
		$form = $this->createForm(LoanType::class);
		
		$formErrors = [];
		$loanData = [];
		
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$data = $form->getData();
			
			//  Process submitted data
			try {
				//  Check for requesting too large of an amount
				if ($data['amount'] > $loanParameterService->getMaxAmount($data['creditScore'])) {
					$formErrors[] = 'The maximum loan amount for your credit score is: $'.$loanParameterService->getMaxAmount($data['creditScore']);
				}
			} catch (\Exception $e) {
				$formErrors = ['Please check your inputs and resubmit the form'];
			}
			
			
			if (empty($formErrors)) {
				try {
					$interestRate = $loanParameterService->getInterestRate($data['term'], $data['creditScore']);
					
					$fee = $loanParameterService->getOriginationFee($data['amount']);
					
					$payment = $loanCalculatorService->getMonthlyPayment($data['amount'] + $fee, $interestRate, $data['term']);
					
					$loanData['interestRate'] = $interestRate;
					$loanData['fee'] = $fee;
					$loanData['payment'] = $payment;
				} catch (\Exception $e) {
					$formErrors = ['Please check your inputs and resubmit the form'];
				}
			}
		}
		
		return $this->render(
			'index.html.twig',
			[
				'form'       => $form->createView(),
				'formErrors' => $formErrors,
				'loanData'   => $loanData,
			]
		);
	}
	
	
}
