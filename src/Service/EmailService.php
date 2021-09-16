<?php

namespace App\Service;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

use Exception;

class EmailService
{
	private $mailer;

	public function __construct(MailerInterface $mailer)
	{
		$this->mailer = $mailer;
	}
	/**
	 * Generate pdf
	 */
	private function generatePDF()
	{
		try {
			// Generate pdf
		} catch (\Exception $e) {
			print_r($e->getMessage());
		}
	}

	/**
	 * Generate and send email
	 *
	 * @param  string $email
	 *
	 * @return
	 */
	public function sendEmail(string $email)
	{
		// Get email address

		// Build pdf of schedule
		$this->generatePDF();

		try {
			// Send email with pdf attachment
			$email = (new Email())
				->from("hello@example.com")
				->to($email)
				//->cc('cc@example.com')
				//->bcc('bcc@example.com')
				//->replyTo('fabien@example.com')
				//->priority(Email::PRIORITY_HIGH)
				// ->attachFromPath("/path/to/documents/terms-of-use.pdf")
				->subject("Time for Symfony Mailer!")
				->text("Sending emails is fun again!")
				->html(
					"<p>See Twig integration for better HTML integration!</p>"
				);
			$this->mailer->send($email);
		} catch (TransportExceptionInterface $e) {
			print_r($e);
		}
	}
}