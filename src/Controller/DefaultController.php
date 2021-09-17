<?php

namespace App\Controller;

use App\Form\LoanType;
use App\Form\ResetForm;
use App\Form\EmailPDF;
use Symfony\Component\HttpFoundation\RequestStack;
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
	private $requestStack;

	public function __construct(RequestStack $requestStack)
	{
		$this->requestStack = $requestStack;
	}
	/**
	 * @Route("/", name="home")
	 */
	public function home(
		Request $request,
		LoanParameter $loanParameterService
	): Response {
		$form = $this->createForm(LoanType::class);
		$resetForm = $this->createForm(ResetForm::class);
		$emailForm = $this->createForm(EmailPDF::class);

		$formErrors = [];
		$loanData = [];

		//# Loan form handling
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$data = $form->getData();

			//  Process submitted data
			try {
				//  Check for requesting too large of an amount
				if (
					$data["amount"] >
					$loanParameterService->getMaxAmount($data["creditScore"])
				) {
					$formErrors[] =
						'The maximum loan amount for your credit score is: $' .
						$loanParameterService->getMaxAmount(
							$data["creditScore"]
						);
				}
			} catch (\Exception $e) {
				$formErrors = [
					"Please check your inputs and resubmit the form",
				];
			}

			if (empty($formErrors)) {
				try {
					$interestRate = $loanParameterService->getInterestRate(
						$data["term"],
						$data["creditScore"]
					);

					$fee = $loanParameterService->getOriginationFee(
						$data["amount"]
					);

					$apr = $loanParameterService->getAPR(
						$data["amount"],
						$interestRate,
						$data["term"],
						$fee
					);

					/** -------------------------------------------
					 *  Init loan calculator with all required
					 * 		values for both payment and payment
					 * 		schedule.
					 */
					$loanCalculatorService = new LoanCalculator(
						$data["amount"],
						$interestRate,
						$data["term"],
						$fee
					);
					$payment = $loanCalculatorService->getMonthlyPayment();
					$schedule = $loanCalculatorService->getPaymentSchedule();

					// Check if payment is not more than 15% of gross income
					if (
						!$loanParameterService->checkAffordability(
							$payment,
							$data["monthlyGrossIncome"]
						)
					) {
						$formErrors = [
							"Unfortunately, we cannot provide an adequate quote for you at this time.",
						];
					}

					// Loan values
					$loanData["interestRate"] = $interestRate;
					$loanData["fee"] = $fee;
					$loanData["payment"] = $payment;
					$loanData["apr"] = $apr;
					// Submitted values
					$loanData["amount"] = $data["amount"];
					$loanData["income"] = $data["monthlyGrossIncome"];
					$loanData["term"] = $data["term"];
					$loanData["creditScore"] = $data["creditScore"];

					// Save as session values for pdf build
					$session = $this->requestStack->getSession();
					$session->set("loanData", $loanData);
					$session->set("schedule", $schedule);
				} catch (\Exception $e) {
					$formErrors = [
						"Please check your inputs and resubmit the form",
					];
				}
			}
		}

		return $this->render("index.html.twig", [
			"form" => $form->createView(),
			"formErrors" => $formErrors,
			"loanData" => $loanData,
			"schedule" => isset($schedule) ? $schedule : "",
			"resetForm" => $resetForm->createView(),
			"emailPDF" => $emailForm->createView(),
		]);
	}
}