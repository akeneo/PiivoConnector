<?php

namespace Piivo\Bundle\ConnectorBundle\Controller;

use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Pim\Bundle\ApiBundle\Controller\FamilyController as BaseApiFamilyController;
use Pim\Component\Api\Exception\PaginationParametersException;
use Pim\Component\Catalog\Query\Filter\Operators;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 */
class FamilyController extends BaseApiFamilyController
{
    /** @var string[] */
    protected $authorizedFieldFilters = ['updated'];

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @AclAncestor("pim_api_family_list")
     */
    public function listAction(Request $request)
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
        $families = $this->repository->searchAfterOffset($criterias, ['code' =>'ASC'], $queryParameters['limit'], $offset);

        $parameters = [
            'query_parameters'    => $queryParameters,
            'list_route_name'     => 'pim_api_family_list',
            'item_route_name'     => 'pim_api_family_get',
        ];

        $count = true === $request->query->getBoolean('with_count') ? $this->repository->count() : null;
        $paginatedFamilies = $this->paginator->paginate(
            $this->normalizer->normalize($families, 'external_api'),
            $parameters,
            $count
        );

        return new JsonResponse($paginatedFamilies);
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
        if (!is_array($searchParameters)) {
            throw new UnprocessableEntityHttpException(
                sprintf('Search query parameter has to be an array, "%s" given.', gettype($searchParameters))
            );
        }
        foreach ($searchParameters as $searchKey => $searchParameter) {
            if (!is_array($searchParameters) || !isset($searchParameter[0])) {
                throw new UnprocessableEntityHttpException(
                    sprintf(
                        'Structure of filter "%s" should respect this structure: %s.',
                        $searchKey,
                        sprintf('{"%s":[{"operator": "my_operator", "value": "my_value"}]}', $searchKey)
                    )
                );
            }

            foreach ($searchParameter as $operatorIndex => $searchOperator) {
                if (!isset($searchOperator['operator'])) {
                    throw new UnprocessableEntityHttpException(
                        sprintf('Operator is missing for the property "%s".', $searchKey)
                    );
                }

                if (!in_array($searchKey, $this->authorizedFieldFilters)) {
                    throw new UnprocessableEntityHttpException(
                        sprintf(
                            'Filter on property "%s" is not supported or does not support operator "%s".',
                            $searchKey,
                            $searchOperator['operator']
                        )
                    );
                }

                // Check value property
                switch ($searchOperator['operator']) {
                    case Operators::EQUALS:
                        if (!isset($searchOperator['value'])) {
                            throw new UnprocessableEntityHttpException(
                                sprintf('Value is missing for the property "%s".', $searchKey)
                            );
                        }

                        break;
                    default:
                        if (!isset($searchOperator['value'])) {
                            throw new UnprocessableEntityHttpException(
                                sprintf('Value is missing for the property "%s".', $searchKey)
                            );
                        }
                }
            }
        }

        return $searchParameters;
    }

    /**
     * Prepares search criterias
     * For now, only enabled filter with operator "=" are managed
     * Value is a boolean
     *
     * @param array $searchParameters
     *
     * @return array
     */
    protected function prepareSearchCriterias(array $searchParameters)
    {
        if (empty($searchParameters)) {
            return [];
        }

        if (isset($searchParameters['updated'][0])) {
            $searchParameters['updated'] = $searchParameters['updated'][0];
            unset($searchParameters['updated'][0]);
        }

        return $searchParameters;
    }
}
