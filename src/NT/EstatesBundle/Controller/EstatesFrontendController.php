<?php

namespace NT\EstatesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Menu\MenuFactory;
use Knp\Menu\Renderer\ListRenderer;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\Matcher\Voter\UriVoter;

class EstatesFrontendController extends Controller
{
    use \NT\FrontendBundle\Traits\NTHelperTrait;

    protected $matcher, $router;
    protected $contentPageId             = 8;
    protected $mainRootName              = 'estates_list';
    protected $estatesCategoriesPerPage = 1000;
    protected $estatesPerPage           = 1000;
    protected $itemsRepo                 = 'NTEstatesBundle:Estate';
    protected $itemsCategoriesRepo       = 'NTEstatesBundle:EstateCategory';

    /**
     * @Route("/estate/rent/{page}", name="estates_rent", requirements={"page": "\d+"})
     * @Template("NTEstatesBundle:Frontend:estates_list.html.twig")
     */
    public function estatesRentAction(Request $request, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getLocale();
        $estatesRepo = $em->getRepository($this->itemsRepo);

        $query = $estatesRepo->getRentListingQuery(null, $locale, $page, $this->estatesCategoriesPerPage);
        $estates = new Paginator($query, true);

        $content = $em->getRepository("NTContentBundle:Content")->findOneById(21);
        if (!$content) {
            throw $this->createNotFoundException("rent page not found");
        }

        $this->generateSeoAndOgTags($content);

        return array(
            'content'            => $content,
            'estates'  => $estates,
            'breadCrumbs'        => $this->generateBreadCrumbs($request),
            'sideBar'            => $this->getSideBar($request),
        );
    }

    /**
     * @Template("NTEstatesBundle:Frontend:homepageEstates.html.twig")
     */
    public function homepageEstatesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getLocale();

        $estateCategories = $em->getRepository($this->itemsCategoriesRepo)->findAllOnHomepageByLocale($locale);

        $estates = array();

        foreach ($estateCategories as $key => $pc) {
            $estatesOnHomepage = $em->getRepository($this->itemsRepo)->findAllOnHomepageByCategoryAndLocale($pc->getId(), $locale);
            if (count($estatesOnHomepage) > 0) {
                $estates[$pc->getId()]['category'] = $pc;
                $estates[$pc->getId()]['estates'] = $estatesOnHomepage;
            }
        }

        return array(
            'estates' => $estates
        );
    }

    /**
     * Route("/search", name="estate_search")
     * @Template("NTEstatesBundle:Frontend:homepageSearch.html.twig")
     */
    public function homepageSearchAction(Request $request, $params = null)
    {
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getLocale();

        $categories = $em->getRepository($this->itemsCategoriesRepo)->findAllByLocale($locale);

        $locations = $em->getRepository('NTEstatesBundle:Location')->findAllByLocale($locale);

        $allParams = array(
            "number" => isset($params['number']) ? $params['number'] : '',
            "category" => isset($params['category']) ? $params['category'] : '',
            "type" => isset($params['type']) ? $params['type'] : '',
            "location" => isset($params['location']) ? $params['location'] : '',
            "min_beds" => isset($params['min_beds']) ? $params['min_beds'] : '',
            "max_beds" => isset($params['max_beds']) ? $params['max_beds'] : '',
            "min_price" => isset($params['min_price']) ? $params['min_price'] : '',
            "max_price" => isset($params['max_price']) ? $params['max_price'] : '',
            "min_area" => isset($params['min_area']) ? $params['min_area'] : '',
            "max_area" => isset($params['max_area']) ? $params['max_area'] : '',
        );

        return array(
            'categories' => $categories,
            'locations' => $locations,
            'params' => $allParams
        );
    }

    /**
     * @Route("/estates/{page}", name="estates_list", requirements={"page": "\d+"})
     * @Template("NTEstatesBundle:Frontend:estates_list.html.twig")
     */
    public function estatesListAction(Request $request, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getLocale();
        $estatesRepo = $em->getRepository($this->itemsRepo);
        $estatesCategoriesRepo = $em->getRepository($this->itemsCategoriesRepo);

        //$estatesCategories = $estatesCategoriesRepo->findAllMainCategoriesByLocale($request->getLocale());
        // if (count($estatesCategories) > 0) {
        //     return $this->forward('NTEstatesBundle:EstatesFrontend:estatesCategoriesList', array('_route' => 'estates_categories_list', 'page'=> $page));
        // }

        $content = $this->getContentPage();

        $query = $estatesRepo->getEstatesListingQuery(null, $locale, $page, $this->estatesPerPage);
        $estates = new Paginator($query, true);

        $this->generateSeoAndOgTags($content);

        return array(
            'estates'    => $estates,
            'content'     => $content,
            'breadCrumbs' => $this->generateBreadCrumbs($request),
            'sideBar'     => $this->getSideBar($request),
        );
    }

    /**
     * @Route("/estates/{page}", name="estates_categories_list", requirements={"page": "\d+"})
     * @Template("NTEstatesBundle:Frontend:estates_categories_list.html.twig")
     */
    public function estatesCategoriesListAction(Request $request, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getLocale();
        $estatesCategoriesRepo = $em->getRepository($this->itemsCategoriesRepo);

        $query = $estatesCategoriesRepo->getCategoriesListingQuery(null, $locale, $page, $this->estatesCategoriesPerPage);
        $estatesCategories = new Paginator($query, true);

        $content = $this->getContentPage();

        $this->generateSeoAndOgTags($content);

        return array(
            'content'            => $content,
            'estatesCategories' => $estatesCategories,
            'breadCrumbs'        => $this->generateBreadCrumbs($request),
            'sideBar'            => $this->getSideBar($request),
        );
    }

    /**
     * @Route("/estates/{categorySlug}/{page}", name="estates_categories_category_view", requirements={"page": "\d+"})
     * @Template("NTEstatesBundle:Frontend:estates_categories_category_view.html.twig")
     */
    public function estatesCategoriesCategoryViewAction(Request $request, $categorySlug, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getLocale();
        $estatesCategoriesRepo = $em->getRepository($this->itemsCategoriesRepo);

        $estateCategory = $estatesCategoriesRepo->findOneBySlugAndLocale($categorySlug, $locale);
        if (!$estateCategory) {
            throw $this->createNotFoundException(sprintf("Estate category '%s' not found", $categorySlug));
        }

        $query = $estatesCategoriesRepo->getCategoriesListingQuery($estateCategory->getId(), $locale, $page, $this->estatesCategoriesPerPage);
        $estateCategoryChildren = new Paginator($query, true);

        if ($estateCategoryChildren->count() <= 0) {
            //if no categories go to render estates
            $estatesRepo = $em->getRepository($this->itemsRepo);
            $query = $estatesRepo->getEstatesListingQuery($estateCategory->getId(), $locale, $page, $this->estatesPerPage);
            $categoryEstates = new Paginator($query, true);

            $this->generateSeoAndOgTags($estateCategory);

            return $this->render('NTEstatesBundle:Frontend:estates_category_estates_list.html.twig', array(
                'estateCategory'  => $estateCategory,
                'categoryEstates' => $categoryEstates,
                'content'          => $this->getContentPage(),
                'breadCrumbs'      => $this->generateBreadCrumbs($request),
                'sideBar'          => $this->getSideBar($request),
            ));
        }

        $this->generateSeoAndOgTags($estateCategory);

        return array(
            'estateCategory'         => $estateCategory,
            'estateCategoryChildren' => $estateCategoryChildren,
            'content'                 => $this->getContentPage(),
            'breadCrumbs'             => $this->generateBreadCrumbs($request),
            'sideBar'                 => $this->getSideBar($request),
        );
    }

    /**
     * @Route("/estates/{categorySlug}/{slug}", name="estates_category_estate_view")
     * @Template("NTEstatesBundle:Frontend:estates_category_estate_view.html.twig")
     */
    public function estatesCategoryEstateViewAction(Request $request, $categorySlug = null, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getLocale();
        $estatesRepo = $em->getRepository($this->itemsRepo);
        $estatesCategoriesRepo = $em->getRepository($this->itemsCategoriesRepo);

        $estate = $estatesRepo->findOneBySlugAndLocale($slug, $locale);
        if (!$estate) {
            throw $this->createNotFoundException(sprintf('Estate "%s" not found', $slug));
        }

        $estateCategory = $estatesCategoriesRepo->findOneBySlugAndLocale($categorySlug, $locale);
        if (!$estateCategory) {
            throw $this->createNotFoundException(sprintf('Category "%s" not found', $categorySlug));
        }

        $sameCategoryEstates = $estatesRepo->findSameCategoryEstates($estateCategory->getId(), $estate->getId(), $locale, 3);

        $dispatcher = $this->get('event_dispatcher');
        $event = new \NT\SEOBundle\Event\SeoEvent($estate);
        $dispatcher->dispatch('nt.seo', $event);

        $params = $this->getImageUrlFromGallery($estate->getTranslations()->get($locale)->getGallery());
        $this->generateSeoAndOgTags($estate, $params);

        return array(
            'estate'              => $estate,
            'estateCategory'      => $estateCategory,
            'sameCategoryEstates' => $sameCategoryEstates,
            'content'              => $this->getContentPage(),
            'breadCrumbs'          => $this->generateBreadCrumbs($request),
            'sideBar'              => $this->getSideBar($request),
        );
    }

    /**
     * @Route("/estate/{slug}", name="estate_without_category")
     * @Template("NTEstatesBundle:Frontend:estate_without_category.html.twig")
     */
    public function estateWithoutCategoryAction(Request $request, $categorySlug = null, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getLocale();
        $estatesRepo = $em->getRepository($this->itemsRepo);

        $estate = $estatesRepo->findOneBySlugAndLocale($slug, $locale);
        if (!$estate) {
            throw $this->createNotFoundException(sprintf('Estate "%s" not found', $slug));
        }

        $attributes = $em->getRepository('NTEstatesBundle:Attribute')->findAllByLocale($locale);

        $relatedEstates = array();
        if (count($estate->getRelatedEstates())) {
            foreach ($estate->getRelatedEstates() as $rp) {
                if ($rp->getPublishWorkflow()->getIsActive()) {
                    $relatedEstates[$rp->getId()] = $rp;
                }
            }
        }

        $params = $this->getImageUrlFromGallery($estate->getTranslations()->get($locale)->getGallery());
        $this->generateSeoAndOgTags($estate, $params);

        return array(
            'estate'              => $estate,
            'content'              => $this->getContentPage(),
            'attributes'           => $attributes,
            'relatedEstates'      => $relatedEstates,
            'breadCrumbs'          => $this->generateBreadCrumbs($request),
            'sideBar'              => $this->getSideBar($request),
        );
    }

    /**
     * @Route("/estates/search/results/{page}", name="estates_search_results")
     * @Template("NTEstatesBundle:Frontend:estatesSearchResults.html.twig")
     */
    public function estatesSearchResultsAction(Request $request, $page)
    {
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getLocale();
        $estatesRepo = $em->getRepository($this->itemsRepo);

        $estates = new Paginator($estatesRepo->doSearch($request->query->all(), $locale, $page), true);

        $content = $em->getRepository('NTContentBundle:Content')->findOneById(10);
        if (!$content) {
            throw $this->createNotFoundException('Search page not found');
        }

        $this->generateSeoAndOgTags($content);

        return array(
            'content' => $content,
            'estates' => $estates,
            'params' => $request->query->all()
        );
    }

    private function getSideBar(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository($this->itemsCategoriesRepo);
        $locale = $request->getLocale();

        $sideBar = null;
        $menuChildrens = $repo->findAllMainCategoriesByLocale($locale);
        $factory = new MenuFactory();
        $sideBar = $factory->createItem('root', array());

        $this->router = $this->container->get("router");
        $this->matcher = new Matcher();
        $this->matcher->addVoter(new UriVoter($_SERVER['REQUEST_URI']));
        $this->createMenu($menuChildrens, $sideBar, $repo, $locale);
        $renderer = new ListRenderer(new \Knp\Menu\Matcher\Matcher());

        return $sideBar != null ? $renderer->render($sideBar, array('currentClass' => 'selected', 'ancestorClass'=>'selected')) : false;
    }

    private function createMenu($children, $menu, $repo, $locale)
    {
        $request = $this->container->get('request');
        $route = $request->get('_route');
        $slug = $request->get('slug');
        $categorySlug = $request->get('categorySlug');
        $requestUri = $request->getRequestUri();

        $params = array();

        foreach ($children as $itm) {
            $uri = $this->generateUrl($itm->getRoute(), $itm->getRouteParams());
            $subMenu = $menu->addChild($itm->getTitle(), array('uri' => $uri, 'currentClass' => 'selected'));
            if ($itm->getSlug() == $slug || $categorySlug == $itm->getSlug()) {
                if ($parentMenu = $subMenu->getParent()) {
                    $parentMenu->setAttribute('class', 'selected');
                }
                $subMenu->setAttribute('class', 'selected');
            }
            if (count($children = $repo->findAllChildrenCategoriesByLocale($itm->getId(), $locale))) {
                $subMenu->setAttribute('class', $subMenu->getAttribute('class').' hasDropdown');
                $subMenu->setChildrenAttribute('class', 'dropdown');
                $this->createMenu($children, $subMenu, $repo, $locale);
            }
        }
    }
}
