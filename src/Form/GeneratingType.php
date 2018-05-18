<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class GeneratingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numberOfCodes', Type\NumberType::class, [
                'attr' => [
                    'placeholder' => 'Number of codes'
                ],
                'label' => 'Number of codes',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Number of codes cannot be empty']),
                ]
            ])
            ->add('codeLength', Type\NumberType::class, [
                'attr' => [
                    'placeholder' => 'Code length',
                ],
                'label' => 'Code length',
                'constraints' => new Assert\NotBlank(['message' => 'Code length cannot be empty'])
            ])
            ->add('submit', Type\SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-success'
                ],
                'label' => 'Generate'
            ]);
    }
}