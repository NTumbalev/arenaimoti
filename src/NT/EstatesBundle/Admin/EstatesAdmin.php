<?php
/**
 * This file is part of the NTEstatesBundle.
 *
 * (c) Nikolay Tumbalev <n.tumbalev@nt.bg>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NT\EstatesBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;

/**
 *  Admin class for Estates
 *
 * @package NTEstatesBundle
 * @author Nikolay Tumbalev <n.tumbalev@nt.bg>
 */
class EstatesAdmin extends Admin
{
    /**
     * @inheritdoc
     */
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt',
    );

    protected $types = array(
        'sell' => 'Продажба',
        'rent' => 'Наем',
        //'buy' => 'Покупка'
    );

    /**
     * {@inheritdoc}
     */
    public function getBatchActions()
    {
        $actions = parent::getBatchActions();

        $actions['hide'] = [
            'label'            => $this->trans('action_hide', array(), 'NTCoreBundle'),
            'ask_confirmation' => true, // If true, a confirmation will be asked before performing the action
        ];
        $actions['show'] = [
            'label'            => $this->trans('action_show', array(), 'NTCoreBundle'),
            'ask_confirmation' => true, // If true, a confirmation will be asked before performing the action
        ];

        return $actions;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        parent::configureTabMenu($menu, $action, $childAdmin);

        if ($action == 'history') {
            $id = $this->getRequest()->get('id');
            $menu->addChild(
                "General",
                array('uri' => $this->generateUrl('history', array('id' => $id)))
            );

            $locales = $this->getConfigurationPool()->getContainer()->getParameter('locales');

            foreach ($locales as $value) {
                $menu->addChild(
                    strtoupper($value),
                    array('uri' => $this->generateUrl('history', array('id' => $id, 'locale' => $value)))
                );
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('General')
                ->add('title')
                ->add('created_at')
                ->add('updated_at')
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection->add('history', $this->getRouterIdParameter().'/history');
        $collection->add('history_view_revision', $this->getRouterIdParameter().'/preview/{revision}');
        $collection->add('history_revert_to_revision', $this->getRouterIdParameter().'/revert/{revision}');
        $collection->add('order', 'order');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('number', null, array('label' => 'form.number'))
            ->add('title', null, array('label' => 'form.title'))
            ->add('estateCategories', null, array('label' => 'Категории'));
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('number', null, array('label' => 'form.number'))
            ->addIdentifier('title', null, array('label' => 'list.title'))
            ->add('isHomepage', null, array('label' => 'form.isHomepage', 'editable' => true))
            ->add('estateCategories', null, array('label' => 'form.categories'))
            ->add('publishWorkflow.isActive', null, array('label' => 'list.isActive', 'editable' => true))
            ->add('_action', 'actions', array(
                    'actions' => array(
                        'edit' => array(),
                        'delete' => array(),
                        #'history' => array('template' => 'NTCoreBundle:Admin:list_action_history.html.twig'),
                    ), 'label' => 'actions',
                ))
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $mediaAdmin = $this->configurationPool->getAdminByClass("Application\\Sonata\\MediaBundle\\Entity\\Media");
        $galleryAdmin = $this->configurationPool->getAdminByClass("Application\\Sonata\\MediaBundle\\Entity\\Gallery");
        $translationAdmin = $this->configurationPool->getAdminByAdminCode('nt.estates.admin.estates_translations');
        $ffds = $translationAdmin->getFormFieldDescriptions();
        $ffds['image']->setAssociationAdmin($mediaAdmin);
        $ffds['gallery']->setAssociationAdmin($galleryAdmin);

        $currencies = array('bgn' => 'лв.', 'eur' => 'Евро');

        $formMapper
            ->with('tab.general', array('tab' => true))
                ->add('number', null, array(
                    'label' => 'form.number',
                    'required' => false
                ))
                ->add('type', 'choice', array(
                    'label' => 'form.type',
                    'required' => true,
                    'choices' => $this->types
                ))
                ->add('estateCategories', 'entity', array(
                    'label' => 'form.category',
                    'class' => 'NT\EstatesBundle\Entity\EstateCategory',
                    'required' => false,
                    'multiple' => false
                ))
                ->add('location', 'entity', array(
                    'label' => 'form.location',
                    'class' => 'NT\EstatesBundle\Entity\Location',
                    'required' => true,
                    'multiple' => false,
                    'empty_value' => '-= Моля изберете стойност =-'
                ))
                ->add('latitude', null, array(
                    'label' => 'form.latitude',
                    'required' => false
                ))
                ->add('longitude', null, array(
                    'label' => 'form.longitude',
                    'required' => false
                ))
                //->add('shareIcons', null, array('label' => 'form.showSocialIcons'))
                ->add('translations', 'a2lix_translations', array(
                    'fields' => array(
                        'slug' => array(
                            'field_type' => 'text',
                            'label' => 'form.slug',
                            'required' => false
                        ),
                        'title' => array(
                            'field_type' => 'text',
                            'label' => 'form.title'
                        ),
                        'simpleDescription' => array(
                            'field_type' => 'textarea',
                            'label' => 'form.simpleDescription',
                            'required' => false,
                        ),
                        'description' => array(
                            'field_type' => 'textarea',
                            'label' => 'form.description',
                            'required' => false,
                            'attr' => array(
                                'class' => 'tinymce',
                                'data-theme' => 'bbcode'
                            )
                        ),
                        'image' => array(
                            'label' => 'Изображение (Препоръчителен минимален размер 600px x 400px)',
                            'required' => false,
                            'field_type' => 'sonata_type_model_list',
                            'model_manager' => $this->getModelManager(),
                            'sonata_field_description' => $ffds['image'],
                            'class' => $mediaAdmin->getClass(),
                            'sonata_admin' => $mediaAdmin->getClass(),
                            'translation_domain' => 'NTEstatesBundle',
                        ),
                        'gallery' => array(
                            'label' => 'form.gallery',
                            'required' => false,
                            'field_type' => 'sonata_type_model_list',
                            'model_manager' => $this->getModelManager(),
                            'sonata_field_description' => $ffds['gallery'],
                            'class' => $galleryAdmin->getClass(),
                            'translation_domain' => 'NTNewsBundle',
                        ),
                        'price' => array(
                            'field_type' => 'number',
                            'label' => 'form.price',
                            'required' => false
                        ),
                        'currency' => array(
                            'field_type' => 'choice',
                            'label' => 'form.currency',
                            'required' => false,
                            'choices' => $currencies
                        ),
                    ),
                    'translation_domain' => 'NTEstatesBundle',
                    'label' => 'form.translations',
                ))
                ->add('area', null, array(
                    'label' => 'form.area',
                    'required' => false
                ))
                ->add('beds', null, array(
                    'label' => 'form.beds',
                    'required' => false
                ))
                ->add('baths', null, array(
                    'label' => 'form.baths',
                    'required' => false
                ))
                ->add('parking', null, array(
                    'label' => 'form.parking',
                    'required' => false
                ))
                ->add('attributes', 'sonata_type_model', array(
                    'label' => 'form.attributes',
                    'required' => false,
                    'multiple' => true,
                    'btn_add' => false
                ))
                ->add('isHomepage', null, array(
                    'label' => 'form.isHomepage',
                    'required' => false
                ))
                ->add('relatedEstates', 'entity', array(
                    'label' => 'form.relatedEstates',
                    'required' => false,
                    'class' => 'NT\EstatesBundle\Entity\Estate',
                    'multiple' => true
                ))
                ->end()
            ->end()
            ->with('SEO', array('tab' => true))
                ->with('SEO', array('collapsed' => true, 'class' => 'col-md-12'))
                    ->add('metaData', 'meta_data')
                ->end()
            ->end()
            ->with('tab.publish_workflow', array('tab' => true))
                ->with('Publish Workflow', array('class' => 'col-md-12', 'label' => 'form.general', 'translation_domain' => 'NTAttributesBundle'))
                    ->add('publishWorkflow', 'nt_publish_workflow', array(
                        'is_active' => $this->getSubject()->getPublishWorkflow() ? $this->getSubject()->getPublishWorkflow()->getIsActive() : true,
                    ))
                ->end()
            ->end();
    }

    private function setEstateNumber($estate)
    {
        if (!$estate->getNumber()) {
            $estate->setNumber(1000 + $estate->getId());
            $em = $this->getConfigurationPool()->getContainer()->get('doctrine')->getManager();
            $em->persist($estate);
            $em->flush();
        }
    }

    public function postPersist($estate)
    {
        $this->setEstateNumber($estate);
    }

    public function postUpdate($estate)
    {
        $this->setEstateNumber($estate);
    }
}
