<?php

namespace spec\Piivo\Bundle\ConnectorBundle\Controller;

use Akeneo\Component\StorageUtils\Factory\SimpleFactoryInterface;
use Akeneo\Component\StorageUtils\Saver\SaverInterface;
use Akeneo\Component\StorageUtils\Updater\ObjectUpdaterInterface;
use PhpSpec\ObjectBehavior;
use Piivo\Bundle\ConnectorBundle\Controller\AttributeController;
use Pim\Bundle\ApiBundle\Controller\AttributeController as BaseApiController;
use Pim\Bundle\ApiBundle\Stream\StreamResourceResponse;
use Pim\Component\Api\Pagination\PaginatorInterface;
use Pim\Component\Api\Pagination\ParameterValidatorInterface;
use Pim\Component\Api\Repository\AttributeRepositoryInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AttributeControllerSpec extends ObjectBehavior
{
    function let(
        AttributeRepositoryInterface $repository,
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
        $this->shouldHaveType(AttributeController::class);
        $this->shouldBeAnInstanceOf(BaseApiController::class);
    }
}
