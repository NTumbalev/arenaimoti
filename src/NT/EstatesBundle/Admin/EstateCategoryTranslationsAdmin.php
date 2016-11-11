<?php

namespace NT\EstatesBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;

class EstateCategoryTranslationsAdmin extends Admin
{

    /**
     * {@inheritdoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('image', 'sonata_type_model_list', array(
                'label' => 'form.image',
                'translation_domain' => 'NTEstatesBundle'
            ), array(
                'link_parameters' => array(
                    'context' => 'nt_estate_category_image'
                ))
            )
        ->end();
    }
}
