<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EmailPDF extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add("email", EmailType::class, [
				"required" => true,
				"label" => "Email",
				"attr" => ["placeholder" => "john.doe@email.com"],
				"help" => "Send yourself an email with this payment schedule",
			])
			->add("emailPDF", SubmitType::class, [
				"label" => "Email PDF",
				"attr" => ["class" => "btn-warning"],
			]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			// Configure your form options here
		]);
	}
}