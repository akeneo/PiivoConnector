<?php

namespace Piivo\Bundle\ConnectorBundle\Controller;

use Piivo\Bundle\ConnectorBundle\Repository\VersionRepository;
use Pim\Bundle\CatalogBundle\Doctrine\ORM\Repository\AttributeRepository;
use Pim\Component\Catalog\Query\Filter\Operators;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 */
class VersionController
{
    /** @var VersionRepository */
    protected $versionRepository;

    /** @var AttributeRepository */
    protected $attributeRepository;

    /** @var array */
    protected $apiConfiguration;

    /** @var string[] */
    protected $authorizedFieldFilters = ['logged_at'];

    /** @var string[] */
    protected $entityClasses = [
        'attribute' => 'Pim\Bundle\CatalogBundle\Entity\Attribute',
        'category'  => 'Pim\Bundle\CatalogBundle\Entity\Category',
        'family'    => 'Pim\Bundle\CatalogBundle\Entity\Family',
        'product'   => 'Pim\Component\Catalog\Model\Product'
    ];

    /**
     * @param VersionRepository $versionRepository
     * @param AttributeRepository $attributeRepository
     * @param array $apiConfiguration
     */
    public function __construct(
        VersionRepository $versionRepository,
        AttributeRepository $attributeRepository,
        array $apiConfiguration
    ) {
        $this->versionRepository   = $versionRepository;
        $this->attributeRepository = $attributeRepository;
        $this->apiConfiguration    = $apiConfiguration;
    }

    /**
     * @param Request $request
     */
    public function listAction(Request $request, $entityName)
    {
        $searchCriterias = $this->validateSearchCriterias($request);

        $entityClass = $this->getEntityClass($entityName);
        $versions = $this->versionRepository->searchAfterOffset($entityClass, $searchCriterias);

        $identifier = $this->getClassIdentifier($entityName);
        $versionCodes = [];
        foreach ($versions as $version) {
            $versionCodes[] = ['code' => $version['snapshot'][$identifier]];
        }

        return new JsonResponse(['_embedded' => ['items' => $versionCodes]]);
    }

    /**
     * Maps entity name with its class name
     *
     * @param string $entityName
     *
     * @return string
     */
    protected function getEntityClass($entityName)
    {
        if (isset($this->entityClasses[$entityName])) {
            return $this->entityClasses[$entityName];
        }

        throw new UnprocessableEntityHttpException(sprintf('Entity name "%s" unknown', $entityName));
    }

    /**
     * Returns entity identifier from an entity name
     *
     * @param string $entityName
     *
     * @return string
     */
    protected function getClassIdentifier($entityName)
    {
        if ('product' === $entityName) {
            return $this->attributeRepository->getIdentifierCode();
        }

        return 'code';
    }

    /**
     * Prepares criterias from search parameters
     * It throws exceptions if search parameters are not correctly filled
     * Only activated = filter is authorized today
     *
     * @param Request $request
     *
     * @throws UnprocessableEntityHttpException
     * @throws BadRequestHttpException
     * @return array
     */
    protected function validateSearchCriterias(Request $request)
    {
        if (!$request->query->has('search')) {
            return [];
        }

        $searchString = $request->query->get('search', '');
        $searchParameters = json_decode($searchString, true);

        if (null === $searchParameters) {
            throw new BadRequestHttpException('Search query parameter should be valid JSON.');
        }

        foreach ($searchParameters as $searchParameter => $searchValue) {
            if (!in_array($searchParameter, $this->authorizedFieldFilters)) {
                throw new UnprocessableEntityHttpException(
                    sprintf(
                        'Filter on property "%s" is not supported.',
                        $searchParameter
                    )
                );
            }

            $datetime = \DateTime::createFromFormat(\DateTime::ISO8601, $searchValue);
            $searchParameters['loggedAt'] = $datetime->format('Y-m-d H:i:s');
            unset($searchParameters['logged_at']);
        }

        return $searchParameters;
    }
}
