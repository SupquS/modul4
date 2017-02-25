<?php

namespace AppBundle\Form;

use AppBundle\Entity\Category;
use AppBundle\Entity\News;
use AppBundle\Form\Type\DateTimePickerType;
use AppBundle\Form\Type\TagsInputType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('isPublished', ChoiceType::class, [
            'choices' => [
                'published' => true,
                'unpublished' => false,
            ],
        ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
            ])
            ->add('title', null, [
            'attr' => ['autofocus' => true],
            'label' => 'News title',
            ])
            ->add('slug')
            ->add('excerpt', null, [
                'attr' => [
                    'rows' => 10,
                    'cols' => 80,
                ],
                'label' => 'Excerpt content',
            ])
            ->add('content', TextareaType::class, [
                'attr' => [
//                    'class' => 'tinymce',
                    'rows' => 20,
                    'cols' => 80,
                ],
                'label' => 'News content',
            ])
            ->add('created', DateTimePickerType::class, [
                'label' => 'Published time',
            ])
//            ->add('author')
            ->add('tags', TagsInputType::class, [
                'label' => 'Tags',
                'by_reference' => false,
//                'required' => false,
//                'mapped' => false,
            ])
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => News::class,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_news';
    }


}
