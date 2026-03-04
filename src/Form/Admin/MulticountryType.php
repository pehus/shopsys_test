<?php

declare(strict_types=1);

namespace App\Form\Admin;

use Shopsys\FrameworkBundle\Model\Country\Country;
use Shopsys\FrameworkBundle\Model\Country\CountryFacade;
use Shopsys\FormTypesBundle\YesNoType;
use Symfony\Component\Form\AbstractType;
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
        $domainId = (int) $options['domain_id'];

        $countries = $this->countryFacade->getAll();

        $countries = array_values(array_filter(
            $countries,
            static fn (Country $country): bool => $country->isEnabled($domainId),
        ));

        usort(
            $countries,
            static fn (Country $a, Country $b): int => $a->getPriority($domainId) <=> $b->getPriority($domainId),
        );

        $entryType = $options['entry_type'];
        $entryOptions = $options['entry_options'];

        foreach ($countries as $country) {
            $builder->add(
                'domain_id_' . $country->getId(),
                $entryType,
                array_replace(
                    [
                        'label' => $country->getName(),
                    ],
                    $entryOptions,
                ),
            );
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['domain_id']);
        $resolver->setAllowedTypes('domain_id', 'int');

        $resolver->setDefaults([
            'data_class' => null,
            'entry_type' => YesNoType::class,
            'entry_options' => [],
        ]);

        $resolver->setAllowedTypes('entry_type', 'string');
        $resolver->setAllowedTypes('entry_options', 'array');
    }
}
