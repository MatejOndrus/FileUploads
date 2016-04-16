<?php

namespace App\Forms;

use App\Model\PlaceRepository;
use Nette;
use Nette\Application\UI\Form;


/**
 * @author Matej Ondrus
 */
class PlaceFormFactory
{
	/** @var FormFactory */
	private $formFactory;

    /** @var PlaceRepository */
    private $placeRepository;


	public function __construct(FormFactory $factory, PlaceRepository $placeRepository)
	{
		$this->formFactory = $factory;
        $this->placeRepository = $placeRepository;
	}


	/**
	 * @return Form
	 */
	public function create()
	{
		$form = $this->formFactory->create();

        $attributesContainer = $form->addContainer('attributes');

		$attributesContainer->addText('name', 'Username:');

		$attributesContainer->addText('description', 'Description:');

        $attributesContainer->addText('price_per_unit', 'Price per unit:')
            ->addRule(Form::FLOAT)
        ;

        $attributesContainer->addText('price_per_extend', 'Price per extend:')
            ->addRule(Form::FLOAT)
        ;

        $attributesContainer->addText('street_name', 'Street name:');

		$form->addSubmit('send', 'Create');

		$form->onSuccess[] = array($this, 'formSucceeded');
		return $form;
	}


	public function formSucceeded(Form $form, $values)
	{
        $this->placeRepository->insert($values['attributes']);
	}
}
