<?php

namespace App\Controller;

use App\Form\EmailPDF;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use App\Controller\PDFController;

class MailerController extends AbstractController
{
	/**
	 * @Route("/email", name="email", condition="request.isXmlHttpRequest()")
	 * @Method({"POST"})
	 */
	public function index(Request $request, PDFController $pdf): JsonResponse
	{
		$form = $this->createForm(EmailPDF::class);

		//# Form handling
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$data = $form->getData();

			//  Process submitted data
			try {
				// Handle email
				if (isset($data["email"])) {
					$this->sendEmail($data["email"], $pdf->index());
					// return new JsonResponse(["success" => $data["email"]]);
				}
			} catch (\Exception $e) {
				return new JsonResponse(["error" => $e->getMessage()]);
			}
		}

		return new JsonResponse(["success" => "Email has been sent!"]);
	}

	public function sendEmail(string $address, $pdf = null): JsonResponse
	{
		try {
			$transport = Transport::fromDsn(
				"smtp://support@levelup.enterprises:erGfd345sE@smtp.titan.email:465"
			);
			$mailer = new Mailer($transport);

			$email = (new Email())
				->from("support@levelup.enterprises")
				->to($address)
				->attachFromPath($pdf)
				->subject("Heres your loan details!")
				->text("Thank you for coming to us to get a loan!")
				->html("<h3>Thank you for coming to us to get a loan!</h3>");

			$mailer->send($email);

			return new JsonResponse(["success" => "Email has been sent!"]);
		} catch (\Exception $e) {
			return new JsonResponse(["error" => $e->getMessage()]);
		}
	}
}