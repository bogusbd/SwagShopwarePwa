<?php declare(strict_types=1);

namespace SwagShopwarePwa\Pwa\Controller;

use Shopware\Core\Content\Cms\Exception\PageNotFoundException;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use SwagShopwarePwa\Pwa\PageLoader\Context\PageLoaderContextBuilder;
use SwagShopwarePwa\Pwa\PageLoader\Context\PageLoaderContext;
use SwagShopwarePwa\Pwa\PageLoader\PageLoaderInterface;
use SwagShopwarePwa\Pwa\PageResult\AbstractPageResult;
use SwagShopwarePwa\Pwa\Response\CmsPageRouteResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    /**
     * Placeholder, because these routes names may change during implementation
     *
     * string
     */
    const PRODUCT_PAGE_ROUTE = 'frontend.detail.page';

    const NAVIGATION_PAGE_ROUTE = 'frontend.navigation.page';

    /**
     * @var PageLoaderContextBuilder
     */
    private $pageLoaderContextBuilder;

    /**
     * @var PageLoaderInterface[]
     */
    private $pageLoaders;

    public function __construct(PageLoaderContextBuilder $pageLoaderContextBuilder, iterable $pageLoaders)
    {
        $this->pageLoaderContextBuilder = $pageLoaderContextBuilder;

        /** @var PageLoaderInterface $pageLoader */
        foreach($pageLoaders as $pageLoader)
        {
            $this->pageLoaders[$pageLoader->getResourceType()] = $pageLoader;
        }
    }

    /**
     * @Route("/sales-channel-api/v{version}/vsf/page", name="sales-channel-api.vsf.page", methods={"POST"})
     * @RouteScope(scopes={"sales-channel-api"})
     *
     * Resolve a page for a given resource and resource identification or path
     * First, a PageLoaderContext object is assembled, which includes information about the resource, request and context.
     * Then, the page is loaded through the page loader only given the page loader context.
     *
     * @param Request $request
     * @param SalesChannelContext $context
     *
     * @deprecated since v0.1.0, use store-api.pwa.page instead
     *
     * @return JsonResponse
     */
    public function resolveOld(Request $request, SalesChannelContext $context): JsonResponse
    {
        return $this->resolve($request, $context);
    }

    /**
     * @Route("/store-api/v{version}/pwa/page", name="store-api.pwa.cms-page-resolve", methods={"POST"})
     * @RouteScope(scopes={"store-api"})
     *
     * Resolve a page for a given resource and resource identification or path
     * First, a PageLoaderContext object is assembled, which includes information about the resource, request and context.
     * Then, the page is loaded through the page loader only given the page loader context.
     *
     * @param Request $request
     * @return CmsPageRouteResponse
     */
    public function resolve(Request $request, SalesChannelContext $context): JsonResponse
    {
        /** @var PageLoaderContext $pageLoaderContext */
        $pageLoaderContext = $this->pageLoaderContextBuilder->build($request, $context);

        $pageLoader = $this->getPageLoader($pageLoaderContext);

        if(!$pageLoader)
        {
            throw new PageNotFoundException($pageLoaderContext->getResourceType() . $pageLoaderContext->getResourceIdentifier());
        }

        /** @var AbstractPageResult $result */
        $result = $this->getPageResult($pageLoader, $pageLoaderContext);

        return new JsonResponse(
            $this->getPageResult($pageLoader, $pageLoaderContext)
        );
    }

    /**
     * Determines the correct page loader for a given resource type
     *
     * @param PageLoaderContext $pageLoaderContext
     * @return PageLoaderInterface|null
     */
    private function getPageLoader(PageLoaderContext $pageLoaderContext): ?PageLoaderInterface
    {
        return $this->pageLoaders[$pageLoaderContext->getResourceType()] ?? null;
    }

    /**
     * Loads the page given the correct page loader and context and returns the assembled page result.
     *
     * @param PageLoaderInterface $pageLoader
     * @param PageLoaderContext $pageLoaderContext
     * @return AbstractPageResult
     */
    private function getPageResult(PageLoaderInterface $pageLoader, PageLoaderContext $pageLoaderContext): AbstractPageResult
    {

        /** @var AbstractPageResult $pageResult */
        $pageResult = $pageLoader->load($pageLoaderContext);

        $pageResult->setResourceType($pageLoaderContext->getResourceType());
        $pageResult->setResourceIdentifier($pageLoaderContext->getResourceIdentifier());

        return $pageResult;
    }
}
