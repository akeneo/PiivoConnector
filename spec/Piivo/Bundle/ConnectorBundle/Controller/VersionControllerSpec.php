<?php

namespace spec\Piivo\Bundle\ConnectorBundle\Controller;

use PhpSpec\ObjectBehavior;
use Piivo\Bundle\ConnectorBundle\Controller\VersionController;
use Piivo\Bundle\ConnectorBundle\Repository\VersionRepository;
use Pim\Bundle\CatalogBundle\Doctrine\ORM\Repository\AttributeRepository;

class VersionControllerSpec extends ObjectBehavior
{
    function let(
        VersionRepository $versionRepository,
        AttributeRepository $attributeRepository
    ) {
        $this->beConstructedWith($versionRepository, $attributeRepository, []);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(VersionController::class);
    }
}
