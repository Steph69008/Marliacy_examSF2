<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', null, [
                'label' => 'Adresse e-mail',
                'attr' => ['class' => 'form-control input-field'],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe',
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password',
                           'class' => 'form-control input-field'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                    new Regex([
//                        'pattern' => '/^(?=.[A-Za-z])(?=.\d).{8,}$/',
                        'pattern' => '/^(?=.*[a-z])(?=.*\d).+$/',
//                        'pattern' => '/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9!@#$%^&*()_]+){8,20}$"/',
                        'message' => 'Your password should contain at least 1 number and 1 letter',
                    ]),
                ],
            ])
            ->add('name', null, [
                'label' => 'Nom',
                'attr' => ['class' => 'form-control input-field'],
            ])
            ->add('firstname', null, [
                'label' => 'Prénom',
                'attr' => ['class' => 'form-control input-field'],
            ])
            ->add('picture', FileType::class, [
                "attr" => [
                    "class" => "form-control"
                ],
                'label' => 'Image',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                        ],
                        'mimeTypesMessage' => 'Uniquement les formats JPG et PNG !',
                    ])
                ],
            ])
            ->add('sector', ChoiceType::class, [
                'label' => 'Secteur',
                'choices' => [
                    'RH' => 'RH',
                    'Informatique' => 'Informatique',
                    'Comptabilité' => 'Comptabilité',
                    'Direction' => 'Direction',
                ],
            ])
            ->add('contract', ChoiceType::class, [
                'label' => 'Contrat',
                'choices' => [
                    'CDI' => 'CDI',
                    'CDD' => 'CDD',
                    'Interim' => 'Interim',
                ],
            ])
            ->add('release_date', null, [
                'label' => 'Date de fin de contrat',
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            if ($data->getContract() === 'CDD' || $data->getContract() === 'Interim') {
                $form->add('release_date');
            }
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            if ($data['contract'] === 'CDD' || $data['contract'] === 'Interim') {
                $form->add('release_date');
            }
        });

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => true
        ]);
    }
}
