<?php

namespace Piivo\Bundle\ConnectorBundle\tests\integration\Controller\Attribute;

use Pim\Bundle\ApiBundle\tests\integration\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class ListAttributeIntegration extends ApiTestCase
{
    public function testListTextAttributes()
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'GET',
            'api/rest/v1/attributes?search={"type":[{"operator":"IN","value":["pim_catalog_text"]}]}'
        );

        $expected = [
            "_links"       => [
                "self"  => [
                    "href" => "http://localhost/api/rest/v1/attributes?page=1&limit=10&with_count=false&search=%7B%22type%22%3A%5B%7B%22operator%22%3A%22IN%22%2C%22value%22%3A%5B%22pim_catalog_text%22%5D%7D%5D%7D",
                ],
                "first" => [
                    "href" => "http://localhost/api/rest/v1/attributes?page=1&limit=10&with_count=false&search=%7B%22type%22%3A%5B%7B%22operator%22%3A%22IN%22%2C%22value%22%3A%5B%22pim_catalog_text%22%5D%7D%5D%7D",
                ],
            ],
            "current_page" => 1,
            "_embedded"    => [
                "items" => [
                    [
                        "_links"                 => [
                            "self" => [
                                "href" => "http://localhost/api/rest/v1/attributes/attr_text",
                            ],
                        ],
                        "code"                   => "attr_text",
                        "type"                   => "pim_catalog_text",
                        "group"                  => "other",
                        "unique"                 => false,
                        "useable_as_grid_filter" => false,
                        "allowed_extensions"     => [],
                        "metric_family"          => null,
                        "default_metric_unit"    => null,
                        "reference_data_name"    => null,
                        "available_locales"      => [],
                        "max_characters"         => null,
                        "validation_rule"        => null,
                        "validation_regexp"      => null,
                        "wysiwyg_enabled"        => null,
                        "number_min"             => null,
                        "number_max"             => null,
                        "decimals_allowed"       => null,
                        "negative_allowed"       => null,
                        "date_min"               => null,
                        "date_max"               => null,
                        "max_file_size"          => null,
                        "minimum_input_length"   => null,
                        "sort_order"             => 0,
                        "localizable"            => false,
                        "scopable"               => false,
                        "labels"                 => [],
                        "auto_option_sorting"    => null,
                    ],
                    [
                        "_links"                 => [
                            "self" => [
                                "href" => "http://localhost/api/rest/v1/attributes/attr_text_2",
                            ],
                        ],
                        "code"                   => "attr_text_2",
                        "type"                   => "pim_catalog_text",
                        "group"                  => "other",
                        "unique"                 => false,
                        "useable_as_grid_filter" => false,
                        "allowed_extensions"     => [],
                        "metric_family"          => null,
                        "default_metric_unit"    => null,
                        "reference_data_name"    => null,
                        "available_locales"      => [],
                        "max_characters"         => null,
                        "validation_rule"        => null,
                        "validation_regexp"      => null,
                        "wysiwyg_enabled"        => null,
                        "number_min"             => null,
                        "number_max"             => null,
                        "decimals_allowed"       => null,
                        "negative_allowed"       => null,
                        "date_min"               => null,
                        "date_max"               => null,
                        "max_file_size"          => null,
                        "minimum_input_length"   => null,
                        "sort_order"             => 0,
                        "localizable"            => false,
                        "scopable"               => false,
                        "labels"                 => [],
                        "auto_option_sorting"    => null,
                    ],
                ],
            ],
        ];

        if (class_exists('\PimEnterprise\Component\Catalog\Normalizer\Standard\AttributeNormalizer')) {
            foreach ($expected['_embedded']['items'] as $key => $item) {
                $expected['_embedded']['items'][$key]['is_read_only'] = false;
            }
        }

        $response = $client->getResponse();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode($expected, JSON_FORCE_OBJECT), $response->getContent());
    }

    public function testListImageAndFileAttributes()
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'GET',
            'api/rest/v1/attributes?search={"type":[{"operator":"IN","value":["pim_catalog_image","pim_catalog_file"]}]}'
        );

        $expected = [
            "_links"       => [
                "self"  => [
                    "href" => "http://localhost/api/rest/v1/attributes?page=1&limit=10&with_count=false&search=%7B%22type%22%3A%5B%7B%22operator%22%3A%22IN%22%2C%22value%22%3A%5B%22pim_catalog_image%22%2C%22pim_catalog_file%22%5D%7D%5D%7D",
                ],
                "first" => [
                    "href" => "http://localhost/api/rest/v1/attributes?page=1&limit=10&with_count=false&search=%7B%22type%22%3A%5B%7B%22operator%22%3A%22IN%22%2C%22value%22%3A%5B%22pim_catalog_image%22%2C%22pim_catalog_file%22%5D%7D%5D%7D",
                ],
            ],
            "current_page" => 1,
            "_embedded"    => [
                "items" => [
                    [
                        "_links"                 => [
                            "self" => [
                                "href" => "http://localhost/api/rest/v1/attributes/attr_file",
                            ],
                        ],
                        "code"                   => "attr_file",
                        "type"                   => "pim_catalog_file",
                        "group"                  => "other",
                        "unique"                 => false,
                        "useable_as_grid_filter" => false,
                        "allowed_extensions"     => [],
                        "metric_family"          => null,
                        "default_metric_unit"    => null,
                        "reference_data_name"    => null,
                        "available_locales"      => [],
                        "max_characters"         => null,
                        "validation_rule"        => null,
                        "validation_regexp"      => null,
                        "wysiwyg_enabled"        => null,
                        "number_min"             => null,
                        "number_max"             => null,
                        "decimals_allowed"       => null,
                        "negative_allowed"       => null,
                        "date_min"               => null,
                        "date_max"               => null,
                        "max_file_size"          => null,
                        "minimum_input_length"   => null,
                        "sort_order"             => 0,
                        "localizable"            => false,
                        "scopable"               => false,
                        "labels"                 => [],
                        "auto_option_sorting"    => null,
                    ],
                    [
                        "_links"                 => [
                            "self" => [
                                "href" => "http://localhost/api/rest/v1/attributes/attr_image",
                            ],
                        ],
                        "code"                   => "attr_image",
                        "type"                   => "pim_catalog_image",
                        "group"                  => "other",
                        "unique"                 => false,
                        "useable_as_grid_filter" => false,
                        "allowed_extensions"     => [],
                        "metric_family"          => null,
                        "default_metric_unit"    => null,
                        "reference_data_name"    => null,
                        "available_locales"      => [],
                        "max_characters"         => null,
                        "validation_rule"        => null,
                        "validation_regexp"      => null,
                        "wysiwyg_enabled"        => null,
                        "number_min"             => null,
                        "number_max"             => null,
                        "decimals_allowed"       => null,
                        "negative_allowed"       => null,
                        "date_min"               => null,
                        "date_max"               => null,
                        "max_file_size"          => null,
                        "minimum_input_length"   => null,
                        "sort_order"             => 0,
                        "localizable"            => false,
                        "scopable"               => false,
                        "labels"                 => [],
                        "auto_option_sorting"    => null,
                    ],
                ],
            ],
        ];

        if (class_exists('\PimEnterprise\Component\Catalog\Normalizer\Standard\AttributeNormalizer')) {
            foreach ($expected['_embedded']['items'] as $key => $item) {
                $expected['_embedded']['items'][$key]['is_read_only'] = false;
            }
        }

        $response = $client->getResponse();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode($expected, JSON_FORCE_OBJECT), $response->getContent());
    }

    public function testListLastUpdatedAttributes()
    {
        $client = $this->createAuthenticatedClient();
        $datetime = new \DateTime('yesterday');
        $dateString = $datetime->format(\DateTime::ISO8601);

        $searchParameters = json_encode(
            [
                'updated' => [
                    [
                        'operator' => '>',
                        'value'    => $dateString,
                    ],
                ],
            ]
        );
        $client->request('GET', 'api/rest/v1/attributes', ['search' => $searchParameters]);

        $expected = [
            "_links"       => [
                "self"  => [
                    "href" => "http://localhost/api/rest/v1/attributes?page=1&limit=10&with_count=false&%s",
                ],
                "first" => [
                    "href" => "http://localhost/api/rest/v1/attributes?page=1&limit=10&with_count=false&%s",
                ],
            ],
            "current_page" => 1,
            "_embedded"    => [
                "items" => [
                    [
                        "_links"                 => [
                            "self" => [
                                "href" => "http://localhost/api/rest/v1/attributes/attr_file",
                            ],
                        ],
                        "code"                   => "attr_file",
                        "type"                   => "pim_catalog_file",
                        "group"                  => "other",
                        "unique"                 => false,
                        "useable_as_grid_filter" => false,
                        "allowed_extensions"     => [],
                        "metric_family"          => null,
                        "default_metric_unit"    => null,
                        "reference_data_name"    => null,
                        "available_locales"      => [],
                        "max_characters"         => null,
                        "validation_rule"        => null,
                        "validation_regexp"      => null,
                        "wysiwyg_enabled"        => null,
                        "number_min"             => null,
                        "number_max"             => null,
                        "decimals_allowed"       => null,
                        "negative_allowed"       => null,
                        "date_min"               => null,
                        "date_max"               => null,
                        "max_file_size"          => null,
                        "minimum_input_length"   => null,
                        "sort_order"             => 0,
                        "localizable"            => false,
                        "scopable"               => false,
                        "labels"                 => [],
                        "auto_option_sorting"    => null,
                    ],
                    [
                        "_links"                 => [
                            "self" => [
                                "href" => "http://localhost/api/rest/v1/attributes/attr_image",
                            ],
                        ],
                        "code"                   => "attr_image",
                        "type"                   => "pim_catalog_image",
                        "group"                  => "other",
                        "unique"                 => false,
                        "useable_as_grid_filter" => false,
                        "allowed_extensions"     => [],
                        "metric_family"          => null,
                        "default_metric_unit"    => null,
                        "reference_data_name"    => null,
                        "available_locales"      => [],
                        "max_characters"         => null,
                        "validation_rule"        => null,
                        "validation_regexp"      => null,
                        "wysiwyg_enabled"        => null,
                        "number_min"             => null,
                        "number_max"             => null,
                        "decimals_allowed"       => null,
                        "negative_allowed"       => null,
                        "date_min"               => null,
                        "date_max"               => null,
                        "max_file_size"          => null,
                        "minimum_input_length"   => null,
                        "sort_order"             => 0,
                        "localizable"            => false,
                        "scopable"               => false,
                        "labels"                 => [],
                        "auto_option_sorting"    => null,
                    ],
                    [
                        "_links"                 => [
                            "self" => [
                                "href" => "http://localhost/api/rest/v1/attributes/attr_number",
                            ],
                        ],
                        "code"                   => "attr_number",
                        "type"                   => "pim_catalog_number",
                        "group"                  => "other",
                        "unique"                 => false,
                        "useable_as_grid_filter" => false,
                        "allowed_extensions"     => [],
                        "metric_family"          => null,
                        "default_metric_unit"    => null,
                        "reference_data_name"    => null,
                        "available_locales"      => [],
                        "max_characters"         => null,
                        "validation_rule"        => null,
                        "validation_regexp"      => null,
                        "wysiwyg_enabled"        => null,
                        "number_min"             => null,
                        "number_max"             => null,
                        "decimals_allowed"       => false,
                        "negative_allowed"       => false,
                        "date_min"               => null,
                        "date_max"               => null,
                        "max_file_size"          => null,
                        "minimum_input_length"   => null,
                        "sort_order"             => 0,
                        "localizable"            => false,
                        "scopable"               => false,
                        "labels"                 => [],
                        "auto_option_sorting"    => null,
                    ],
                    [
                        "_links"                 => [
                            "self" => [
                                "href" => "http://localhost/api/rest/v1/attributes/attr_text",
                            ],
                        ],
                        "code"                   => "attr_text",
                        "type"                   => "pim_catalog_text",
                        "group"                  => "other",
                        "unique"                 => false,
                        "useable_as_grid_filter" => false,
                        "allowed_extensions"     => [],
                        "metric_family"          => null,
                        "default_metric_unit"    => null,
                        "reference_data_name"    => null,
                        "available_locales"      => [],
                        "max_characters"         => null,
                        "validation_rule"        => null,
                        "validation_regexp"      => null,
                        "wysiwyg_enabled"        => null,
                        "number_min"             => null,
                        "number_max"             => null,
                        "decimals_allowed"       => null,
                        "negative_allowed"       => null,
                        "date_min"               => null,
                        "date_max"               => null,
                        "max_file_size"          => null,
                        "minimum_input_length"   => null,
                        "sort_order"             => 0,
                        "localizable"            => false,
                        "scopable"               => false,
                        "labels"                 => [],
                        "auto_option_sorting"    => null,
                    ],
                    [
                        "_links"                 => [
                            "self" => [
                                "href" => "http://localhost/api/rest/v1/attributes/attr_text_2",
                            ],
                        ],
                        "code"                   => "attr_text_2",
                        "type"                   => "pim_catalog_text",
                        "group"                  => "other",
                        "unique"                 => false,
                        "useable_as_grid_filter" => false,
                        "allowed_extensions"     => [],
                        "metric_family"          => null,
                        "default_metric_unit"    => null,
                        "reference_data_name"    => null,
                        "available_locales"      => [],
                        "max_characters"         => null,
                        "validation_rule"        => null,
                        "validation_regexp"      => null,
                        "wysiwyg_enabled"        => null,
                        "number_min"             => null,
                        "number_max"             => null,
                        "decimals_allowed"       => null,
                        "negative_allowed"       => null,
                        "date_min"               => null,
                        "date_max"               => null,
                        "max_file_size"          => null,
                        "minimum_input_length"   => null,
                        "sort_order"             => 0,
                        "localizable"            => false,
                        "scopable"               => false,
                        "labels"                 => [],
                        "auto_option_sorting"    => null,
                    ],
                    [
                        "_links"                 => [
                            "self" => [
                                "href" => "http://localhost/api/rest/v1/attributes/sku",
                            ],
                        ],
                        "code"                   => "sku",
                        "type"                   => "pim_catalog_identifier",
                        "group"                  => "other",
                        "unique"                 => true,
                        "useable_as_grid_filter" => true,
                        "allowed_extensions"     => [],
                        "metric_family"          => null,
                        "default_metric_unit"    => null,
                        "reference_data_name"    => null,
                        "available_locales"      => [],
                        "max_characters"         => null,
                        "validation_rule"        => null,
                        "validation_regexp"      => null,
                        "wysiwyg_enabled"        => null,
                        "number_min"             => null,
                        "number_max"             => null,
                        "decimals_allowed"       => null,
                        "negative_allowed"       => null,
                        "date_min"               => null,
                        "date_max"               => null,
                        "max_file_size"          => null,
                        "minimum_input_length"   => null,
                        "sort_order"             => 0,
                        "localizable"            => false,
                        "scopable"               => false,
                        "labels"                 => [
                            "en_US" => "SKU",
                        ],
                        "auto_option_sorting"    => null,
                    ],
                ],
            ],
        ];

        if (class_exists('\PimEnterprise\Component\Catalog\Normalizer\Standard\AttributeNormalizer')) {
            foreach ($expected['_embedded']['items'] as $key => $item) {
                $expected['_embedded']['items'][$key]['is_read_only'] = false;
            }
        }
        $expected = json_encode($expected, JSON_FORCE_OBJECT);

        $queryString = $client->getRequest()->getQueryString();
        $expected = sprintf($expected, $queryString, $queryString);

        $response = $client->getResponse();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString($expected, $response->getContent());
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->loadAttribute(
            [
                'code'             => 'attr_number',
                'type'             => 'pim_catalog_number',
                'decimals_allowed' => false,
                'negative_allowed' => false,
            ]
        );
        $this->loadAttribute(['code' => 'attr_text', 'type' => 'pim_catalog_text']);
        $this->loadAttribute(['code' => 'attr_text_2', 'type' => 'pim_catalog_text']);
        $this->loadAttribute(['code' => 'attr_file', 'type' => 'pim_catalog_file']);
        $this->loadAttribute(['code' => 'attr_image', 'type' => 'pim_catalog_image']);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->removeAttribute('attr_number');
        $this->removeAttribute('attr_text');
        $this->removeAttribute('attr_text_2');
        $this->removeAttribute('attr_file');
        $this->removeAttribute('attr_image');

        parent::tearDown();
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return $this->catalog->useMinimalCatalog();
    }

    /**
     * @param array
     */
    protected function loadAttribute(array $data)
    {
        $attribute = $this->get('pim_catalog.factory.attribute')->create();
        $data['group'] = 'other';
        $this->get('pim_catalog.updater.attribute')->update($attribute, $data);
        $this->get('pim_catalog.saver.attribute')->save($attribute);
    }

    /**
     * @param string $code
     */
    protected function removeAttribute($code)
    {
        $attribute = $this->get('pim_catalog.repository.attribute')->findOneByIdentifier($code);
        $this->get('pim_catalog.remover.attribute')->remove($attribute);
    }
}
