<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Task;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $client = new Client;
        $builder
            ->add('client', EntityType::class, ['class'=>Client::class,
            'choice_label'=>'prenom'])
            ->add('type', ChoiceType::class, [
                'choices'  => [
                    'Petite' => "PETITE",
                    'Moyenne' => "MOYENNE",
                    'Grosse' => "GROSSE",
                ]])
            ->add('description')
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
