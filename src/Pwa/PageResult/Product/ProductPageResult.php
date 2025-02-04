<?php declare(strict_types=1);

namespace SwagShopwarePwa\Pwa\PageResult\Product;

use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Content\Property\PropertyGroupCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\AggregationResult\AggregationResultCollection;
use SwagShopwarePwa\Pwa\PageResult\AbstractPageResult;

class ProductPageResult extends AbstractPageResult
{
    /**
     * @var SalesChannelProductEntity
     */
    protected $product;

    /**
     * @var PropertyGroupCollection
     */
    protected $configurator;

    /**
     * @var array
     */
    protected $breadcrumb;

    /**
     * @return SalesChannelProductEntity
     */
    public function getProduct(): SalesChannelProductEntity
    {
        return $this->product;
    }

    /**
     * @param SalesChannelProductEntity $product
     */
    public function setProduct(SalesChannelProductEntity $product)
    {
        $this->product = $product;
    }

    /**
     * @return PropertyGroupCollection
     */
    public function getConfigurator(): PropertyGroupCollection
    {
        return $this->configurator;
    }

    /**
     * @param PropertyGroupCollection $configurator
     */
    public function setConfigurator(PropertyGroupCollection $configurator)
    {
        $this->configurator = $configurator;
    }

    /**
     * @return array
     */
    public function getBreadcrumb(): ?array
    {
        return $this->breadcrumb;
    }

    public function setBreadcrumb(?array $breadcrumb)
    {
        $this->breadcrumb = $breadcrumb;
    }
}
