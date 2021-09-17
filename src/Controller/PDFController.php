<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;

class PDFController extends AbstractController
{
	private $requestStack;

	public function __construct(RequestStack $requestStack)
	{
		$this->requestStack = $requestStack;
	}

	/**
	 *
	 * @return
	 * @throws Exception
	 */
	public function index()
	{
		// Get values for pdf
		$session = $this->requestStack->getSession();
		$loanData = $session->get("loanData");
		$schedule = $session->get("schedule");

		// Configure Dompdf according to your needs
		$pdfOptions = new Options();
		$pdfOptions->set("defaultFont", "Arial");

		// Instantiate Dompdf with our options
		$dompdf = new Dompdf($pdfOptions);

		// Retrieve the HTML generated in our twig file
		$html = $this->renderView("pdf.html.twig", [
			"loanData" => $loanData,
			"schedule" => $schedule,
		]);

		// Load HTML to Dompdf
		$dompdf->loadHtml($html);
		$dompdf->setPaper("A4", "portrait");
		$dompdf->render();

		// PDF file
		$output = $dompdf->output();

		// Save as temp directory
		$location =
			DIRECTORY_SEPARATOR .
			trim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) .
			DIRECTORY_SEPARATOR .
			ltrim("loan-estimate.pdf", DIRECTORY_SEPARATOR);
		file_put_contents($location, $output);

		return $location;

		// Output the generated PDF to Browser (force download)
		// $dompdf->stream("loan-estimate.pdf", [
		// 	"Attachment" => true,
		// ]);
	}
}