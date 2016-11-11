<?php

namespace NT\NewsBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;

class NewsCategoriesTranslationsAdmin extends Admin
{
    /**
     * {@inheritdoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
        ->add('image', 'sonata_type_model_list', array(
            'label' => 'form.image',
            'translation_domain' => 'NTNewsBundle'
        ), array(
            'link_parameters' => array(
                'context' => 'nt_news_categories_images'
            ))
        )
        ->end();
    }
}
