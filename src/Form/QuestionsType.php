<?php

namespace App\Form;

use App\Entity\Questions;
use App\Entity\Type;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // $typeRepository = new TypeRepository;
        // $type = $typeRepository->findBy([]);
        $builder
            ->add('description', TextType::class, ['required' => true])
            ->add('valid', CheckboxType::class, ['value' => false, 'disabled' => true]) // disabled fot the client but should be enabled for admins
            ->add('type', EntityType::class, [
                'class' => Type::class,
                'choice_label' => 'name'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Questions::class,
        ]);
    }
}