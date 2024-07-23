<?php

namespace App\Form;

use App\Entity\TaskSchedule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskScheduleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('script_path')
            ->add('time_execution')
            ->add('outputlog_path')
            ->add('can_init')
            ->add('status')
            ->add('last_execution_time', null, [
                'widget' => 'single_text',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TaskSchedule::class,
        ]);
    }
}
