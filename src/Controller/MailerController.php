<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerController extends AbstractController
{
	/**
	 * @Route("/")
	 */
	public function sendEmail(
		Request $request,
		MailerInterface $mailer
	): Response {
		// Get email address
		print_r($request);
		die();

		// Build pdf of schedule

		// Send email with pdf attachment
		$email = (new Email())
			->from("hello@example.com")
			->to("you@example.com")
			//->cc('cc@example.com')
			//->bcc('bcc@example.com')
			//->replyTo('fabien@example.com')
			//->priority(Email::PRIORITY_HIGH)
			// ->attachFromPath("/path/to/documents/terms-of-use.pdf")
			->subject("Time for Symfony Mailer!")
			->text("Sending emails is fun again!")
			->html("<p>See Twig integration for better HTML integration!</p>");
		$mailer->send($email);
		try {
		} catch (\Exception $e) {
			print_r($e->getMessage());
			$formErrors = ["Please check your inputs and resubmit the form"];
		}
	}
}