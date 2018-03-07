<?php

namespace spec\Piivo\Bundle\ConnectorBundle\Controller;

use Akeneo\Component\StorageUtils\Factory\SimpleFactoryInterface;
use Akeneo\Component\StorageUtils\Saver\SaverInterface;
use Akeneo\Component\StorageUtils\Updater\ObjectUpdaterInterface;
use PhpSpec\ObjectBehavior;
use Piivo\Bundle\ConnectorBundle\Controller\CategoryController;
use Pim\Bundle\ApiBundle\Controller\CategoryController as BaseApiCategoryController;
use Pim\Bundle\ApiBundle\Stream\StreamResourceResponse;
use Pim\Component\Api\Pagination\PaginatorInterface;
use Pim\Component\Api\Pagination\ParameterValidatorInterface;
use Pim\Component\Api\Repository\ApiResourceRepositoryInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CategoryControllerSpec extends ObjectBehavior
{
    function let(
        ApiResourceRepositoryInterface $repository,
        NormalizerInterface $normalizer,
        SimpleFactoryInterface $factory,
        ObjectUpdaterInterface $updater,
        ValidatorInterface $validator,
        SaverInterface $saver,
        RouterInterface $router,
        PaginatorInterface $paginator,
        ParameterValidatorInterface $parameterValidator,
        StreamResourceResponse $partialUpdateStreamResource
    ) {
        $this->beConstructedWith(
            $repository,
            $normalizer,
            $factory,
            $updater,
            $validator,
            $saver,
            $router,
            $paginator,
            $parameterValidator,
            $partialUpdateStreamResource,
            []
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CategoryController::class);
        $this->shouldBeAnInstanceOf(BaseApiCategoryController::class);
    }
}
