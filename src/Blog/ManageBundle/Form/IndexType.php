<?php

namespace Blog\ManageBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class IndexType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user_id', 'hidden')
            ->add('title',null,array('attr' => array('placeholder' => 'title'),'label' => '标题'))
            ->add('url',null, array('attr' => array('placeholder' => '内容链接'),'label' => '内容链接'))
            ->add('summary','textarea',array('required' =>false,'label' => '内容介绍','attr' => array('placeholder' => '内容介绍','style' => 'resize: none; font-family: Monaco,Menlo,Consolas,&quot;Courier New&quot;,monospace; width: 546px; height: 253px;')))
            ->add('expired', null,array('label' => '过期','required' =>false))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Blog\ManageBundle\Entity\Index'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'blog_managebundle_index';
    }
}
