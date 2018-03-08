<?php

namespace spec\Piivo\Bundle\ConnectorBundle\Controller;

use Akeneo\Component\StorageUtils\Saver\SaverInterface;
use PhpSpec\ObjectBehavior;
use Piivo\Bundle\ConnectorBundle\Controller\TextCollectionValueController;
use Pim\Component\Catalog\Query\ProductQueryBuilderFactoryInterface;
use Pim\Component\Catalog\Repository\AttributeRepositoryInterface;

class TextCollectionValueControllerSpec extends ObjectBehavior
{
    function let(
        ProductQueryBuilderFactoryInterface $productModelQueryBuilderFactory,
        SaverInterface $productModelSaver,
        ProductQueryBuilderFactoryInterface $productQueryBuilderFactory,
        SaverInterface $productSaver,
        AttributeRepositoryInterface $attributeRepository
    ) {
        $this->beConstructedWith(
            $productModelQueryBuilderFactory,
            $productModelSaver,
            $productQueryBuilderFactory,
            $productSaver,
            $attributeRepository,
            []
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TextCollectionValueController::class);
    }
}
