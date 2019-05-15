<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\File;

class ImportTableType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('POST')
            ->add('importFile',FileType::class, [
                'label' => 'Импорт продуктов',
                'constraints' => [
                    new NotBlank(),
                    new File([
                        'mimeTypes' => [],
                        'mimeTypesMessage' => 'Файл должен иметь расширение .csv',
                        'maxSize' => '10M',
                        'maxSizeMessage' => 'Размер файла должен быть меньше {{ limit }} {{ suffix }}.',
                        'notFoundMessage' => 'Файл не найден',
                        ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
