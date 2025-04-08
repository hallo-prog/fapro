<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\OfferSubCategory;
use App\Entity\QuestionArea;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OfferSubCategoryGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('questionAreas', CollectionType::class, [
            'entry_type' => QuestionAreaType::class,
            'allow_add' => true,
            'delete_empty' => true,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'allow_extra_fields' => true,
            'data_class' => OfferSubCategory::class,
        ]);
    }
}
