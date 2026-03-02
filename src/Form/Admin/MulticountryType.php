<?php

declare(strict_types=1);

namespace App\Form\Admin;

use Shopsys\FrameworkBundle\Model\Country\CountryFacade;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class MulticountryType extends AbstractType
{
    public function __construct(
        private readonly CountryFacade $countryFacade,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $countries = $this->countryFacade->getAll();

        foreach ($countries as $country) {
            $builder->add(
                'domain_id_' . $country->getId(),
                ChoiceType::class,
                [
                    'label' => $country->getName(),
                    'required' => false,
                    'expanded' => true,
                    'multiple' => false,
                    'placeholder' => false,
                    'choices' => [
                        'Ano' => 1,
                        'Ne' => 0,
                    ],
                    'choice_value' => static fn (?int $value): string => $value === null ? '' : (string)$value,
                    'data' => 0,
                ],
            );
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
