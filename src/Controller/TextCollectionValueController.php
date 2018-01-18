<?php

namespace Piivo\Bundle\ConnectorBundle\Controller;

use Akeneo\Component\StorageUtils\Saver\SaverInterface;
use Pim\Bundle\ExtendedAttributeTypeBundle\Model\TextCollectionValue;
use Pim\Component\Catalog\Model\EntityWithValuesInterface;
use Pim\Component\Catalog\Query\Filter\Operators;
use Pim\Component\Catalog\Query\ProductQueryBuilderFactoryInterface;
use Pim\Component\Catalog\Repository\AttributeRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 */
class TextCollectionValueController
{
    /** @var ProductQueryBuilderFactoryInterface */
    protected $productModelQueryBuilderFactory;

    /** @var SaverInterface */
    protected $productModelSaver;

    /** @var ProductQueryBuilderFactoryInterface */
    protected $productQueryBuilderFactory;

    /** @var SaverInterface */
    protected $productSaver;

    /** @var AttributeRepositoryInterface */
    protected $attributeRepository;

    /** @var array */
    protected $apiConfiguration;

    /**
     * @param ProductQueryBuilderFactoryInterface $productModelQueryBuilderFactory
     * @param SaverInterface $productModelSaver
     * @param ProductQueryBuilderFactoryInterface $productQueryBuilderFactory
     * @param SaverInterface $productSaver
     * @param AttributeRepositoryInterface $attributeRepository
     * @param array $apiConfiguration
     */
    public function __construct(
        ProductQueryBuilderFactoryInterface $productModelQueryBuilderFactory,
        SaverInterface $productModelSaver,
        ProductQueryBuilderFactoryInterface $productQueryBuilderFactory,
        SaverInterface $productSaver,
        AttributeRepositoryInterface $attributeRepository,
        array $apiConfiguration
    ) {
        $this->productModelQueryBuilderFactory = $productModelQueryBuilderFactory;
        $this->productModelSaver = $productModelSaver;
        $this->productQueryBuilderFactory = $productQueryBuilderFactory;
        $this->productSaver = $productSaver;
        $this->attributeRepository = $attributeRepository;
        $this->apiConfiguration = $apiConfiguration;
    }

    /**
     * @param Request $request
     * @param string $attributeCode
     *
     * @return JsonResponse
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

        $pmqb = $this->productModelQueryBuilderFactory->create();
        $pmqb->addFilter($attributeCode, Operators::CONTAINS, $item);
        $productModels = $pmqb->execute();

        foreach ($productModels as $productModel) {
            $this->removeItemFromTextCollection($productModel, $attributeCode, $item);
            $this->productModelSaver->save($productModel);
        }

        $pqb = $this->productQueryBuilderFactory->create();
        $pqb->addFilter($attributeCode, Operators::CONTAINS, $item);
        $products = $pqb->execute();

        foreach ($products as $product) {
            $this->removeItemFromTextCollection($product, $attributeCode, $item);
            $this->productSaver->save($product);
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param EntityWithValuesInterface $product
     * @param string $attributeCode
     * @param string $item
     */
    protected function removeItemFromTextCollection(EntityWithValuesInterface $product, $attributeCode, $item)
    {
        /** @var TextCollectionValue $value */
        $value = $product->getValue($attributeCode);
        $value->removeItem($item);
    }
}
