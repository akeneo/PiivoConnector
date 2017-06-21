<?php

namespace Piivo\Bundle\ConnectorBundle\Controller;
use Pim\Component\Api\Exception\PaginationParametersException;
use Pim\Component\Api\Pagination\PaginatorInterface;
use Pim\Component\Api\Pagination\ParameterValidatorInterface;
use Pim\Component\Api\Repository\ApiResourceRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;


/**
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 */
class VersionController
{
    /** @var ApiResourceRepositoryInterface */
    protected $repository;

    /** @var NormalizerInterface */
    protected $normalizer;

    /** @var RouterInterface */
    protected $router;

    /** @var PaginatorInterface */
    protected $paginator;

    /** @var ParameterValidatorInterface */
    protected $parameterValidator;

    /** @var array */
    protected $apiConfiguration;

    /**
     * @param ApiResourceRepositoryInterface $repository
     * @param NormalizerInterface $normalizer
     * @param RouterInterface $router
     * @param PaginatorInterface $paginator
     * @param ParameterValidatorInterface $parameterValidator
     * @param array $apiConfiguration
     */
    public function __construct(
        ApiResourceRepositoryInterface $repository,
        NormalizerInterface $normalizer,
        RouterInterface $router,
        PaginatorInterface $paginator,
        ParameterValidatorInterface $parameterValidator,
        array $apiConfiguration
    ) {
        $this->repository         = $repository;
        $this->normalizer         = $normalizer;
        $this->router             = $router;
        $this->paginator          = $paginator;
        $this->parameterValidator = $parameterValidator;
        $this->apiConfiguration   = $apiConfiguration;
    }

    /**
     * @param Request $request
     */
    public function listAction(Request $request, $entityName)
    {
        $searchCriterias = $this->validateSearchCriterias($request);
        $criterias = $this->prepareSearchCriterias($searchCriterias);

        try {
            $this->parameterValidator->validate($request->query->all());
        } catch (PaginationParametersException $e) {
            throw new UnprocessableEntityHttpException($e->getMessage(), $e);
        }
        $defaultParameters = [
            'page'       => 1,
            'limit'      => $this->apiConfiguration['pagination']['limit_by_default'],
            'with_count' => 'false',
        ];

        $queryParameters = array_merge($defaultParameters, $request->query->all());

        $offset = $queryParameters['limit'] * ($queryParameters['page'] - 1);
        $versions = $this->repository->searchAfterOffset($criterias, [], $queryParameters['limit'], $offset);

        $parameters = [
            'query_parameters'    => $queryParameters,
            'list_route_name'     => 'piivo_api_version_deleted_list',
        ];

        $count = true === $request->query->getBoolean('with_count') ? $this->repository->count() : null;
        $paginatedVersions = $this->paginator->paginate(
            $this->normalizer->normalize($versions, 'external_api'),
            $parameters,
            $count
        );

        return new JsonResponse($paginatedVersions);
    }
}
