<?php

namespace Blog\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BlogArticleType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user_id', 'hidden')
             ->add('article_id', 'hidden')
             ->add('title',null,array('attr' => array('placeholder' => 'title'),'label' => '章节标题'))
             ->add('blog', 'textarea',array('attr' => array('style' => 'resize: none; font-family: Monaco,Menlo,Consolas,&quot;Courier New&quot;,monospace; width: 546px; height: 253px;')))
             ->add('token', 'hidden')
             ->add('chapter',null,array('label' => '是章节?','required' => false))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Blog\BlogBundle\Entity\BlogArticle'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'blog_blogbundle_blogarticle';
    }
}
