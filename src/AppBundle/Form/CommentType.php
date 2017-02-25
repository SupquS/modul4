<?php

namespace AppBundle\Form;

use AppBundle\Entity\Comment;
use AppBundle\Form\Type\DateTimePickerType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    /**
     * {@inheritdoc}
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content')
//            ->add('published', DateTimePickerType::class, [
//                'label' => 'Published time',
//            ])
//            ->add('author')
//            ->add('news', NewsType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
