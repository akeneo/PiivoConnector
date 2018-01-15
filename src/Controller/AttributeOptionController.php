<?php

namespace Piivo\Bundle\ConnectorBundle\Controller;

use Akeneo\Component\StorageUtils\Saver\SaverInterface;
use Pim\Bundle\CatalogBundle\Doctrine\Common\Saver\ProductSaver;
use Pim\Component\Catalog\Model\ProductInterface;
use Pim\Component\Catalog\Query\Filter\Operators;
use Pim\Component\Catalog\Query\ProductQueryBuilderFactoryInterface;
use Pim\Component\Catalog\Repository\AttributeRepositoryInterface;
use PimEnterprise\Bundle\CatalogBundle\Security\Doctrine\Common\Saver\FilteredEntitySaver;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 */
class AttributeOptionController
{
    /** @var ProductQueryBuilderFactoryInterface */
    protected $pqbFactory;

    /** @var ProductSaver */
    protected $productSaver;

    /** @var AttributeRepositoryInterface */
    protected $attributeRepository;

    /** @var array */
    protected $apiConfiguration;

    /**
     * @param ProductQueryBuilderFactoryInterface $pqbFactory
     * @param SaverInterface $productSaver
     * @param AttributeRepositoryInterface $attributeRepository
     * @param array $apiConfiguration
     */
    public function __construct(
        ProductQueryBuilderFactoryInterface $pqbFactory,
        FilteredEntitySaver $productSaver,
        AttributeRepositoryInterface $attributeRepository,
        array $apiConfiguration
    ) {
        $this->pqbFactory          = $pqbFactory;
        $this->productSaver        = $productSaver;
        $this->attributeRepository = $attributeRepository;
        $this->apiConfiguration    = $apiConfiguration;
    }

    /**
     * @param Request $request
     * @param string $attributeCode
     * @param string $item
     */
    public function deleteItemAction(Request $request, $attributeCode)
    {
        $item = $request->get('item');

        $attribute = $this->attributeRepository->findOneByIdentifier($attributeCode);
        if (null === $attribute) {
            throw new NotFoundHttpException(
                sprintf('Attribute "%s" not found', $attributeCode)
            );
        }

        if ("pim_catalog_text_collection" !== $attribute->getAttributeType()) {
            throw new UnprocessableEntityHttpException(
                sprintf('Attribute "%s" is not of type "%s"', $attributeCode, "pim_catalog_text_collection")
            );
        }

        $pqb = $this->pqbFactory->create();
        $pqb->addFilter($attributeCode, Operators::CONTAINS, $item);
        $products = $pqb->execute();

        foreach ($products as $product) {
            $this->removeProductItem($product, $attributeCode, $item);
            $this->productSaver->save($product);
        }

        return new JsonResponse();
    }

    /**
     * @param ProductInterface $product
     * @param string $attributeCode
     * @param string $item
     */
    protected function removeProductItem(ProductInterface $product, $attributeCode, $item)
    {
        $value = $product->getValue($attributeCode);
        $value->removeItem($item);
    }
}
