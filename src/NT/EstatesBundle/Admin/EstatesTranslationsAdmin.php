<?php

namespace NT\EstatesBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;

class EstatesTranslationsAdmin extends Admin
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
                    'context' => 'nt_estates_image'
                ))
            )
            ->add('gallery', 'sonata_type_model_list', array(
                'label' => 'form.gallery',
                'translation_domain' => 'NTEstatesBundle'
            ), array(
                'link_parameters' => array(
                    'context' => 'nt_estates_gallery'
                ))
            )
        ->end();
    }
}
