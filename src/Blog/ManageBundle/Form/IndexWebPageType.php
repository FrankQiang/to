<?php

namespace Blog\ManageBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class IndexWebPageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user_id', 'hidden')
            ->add('category_id', 'hidden')
            ->add('title')
            ->add('url')
            ->add('token', 'hidden')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Blog\ManageBundle\Entity\IndexWebPage'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'blog_managebundle_indexwebpage';
    }
}
