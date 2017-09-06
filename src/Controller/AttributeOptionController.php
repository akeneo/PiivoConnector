<?php

namespace Piivo\Bundle\ConnectorBundle\Controller;

use Pim\Bundle\CatalogBundle\Doctrine\Common\Saver\ProductSaver;
use Pim\Component\Catalog\Model\ProductInterface;
use Pim\Component\Catalog\Query\Filter\Operators;
use Pim\Component\Catalog\Query\ProductQueryBuilderFactoryInterface;
use Pim\Component\Catalog\Repository\AttributeRepositoryInterface;
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
     * @param ProductSaver $productSaver
     * @param AttributeRepositoryInterface $attributeRepository
     * @param array $apiConfiguration
     */
    public function __construct(
        ProductQueryBuilderFactoryInterface $pqbFactory,
        ProductSaver $productSaver,
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
        error_log("\nDELETE ITEM ACTION\n", 3, '/tmp/test.txt');
        error_log("------------------\n", 3, '/tmp/test.txt');
        error_log(print_r($item, true), 3, '/tmp/test.txt');

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
        error_log(sprintf("\nPRODUCTS %s\n", count($products)), 3, '/tmp/test.txt');

        foreach ($products as $product) {
            error_log(sprintf("Product: '%s'\n", (string) $product), 3, '/tmp/test.txt');
            error_log(sprintf("Remove product item '%s': '%s'\n", $attributeCode, $item), 3, '/tmp/test.txt');
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
        $textCollection = (array) $value->getTextCollection();

        error_log(sprintf("Text collection value: '%s'\n", print_r($textCollection, true)), 3, '/tmp/test.txt');
        error_log(sprintf("Searching item: '%s'\n", $item), 3, '/tmp/test.txt');
        foreach ($textCollection as $key => $itemText) {
            if ($itemText === $item) {
                error_log("Found!\n", 3, '/tmp/test.txt');
                unset($textCollection[$key]);
                break;
            }
        }

        error_log(sprintf("New text collection value: '%s'\n", print_r($textCollection, true)), 3, '/tmp/test.txt');

        $textCollection = array_values($textCollection);
        error_log(sprintf("New text collection value: '%s'\n", print_r($textCollection, true)), 3, '/tmp/test.txt');

        $value->setTextCollection(array_values($textCollection));
    }
}
