<?php

namespace Piivo\Bundle\ConnectorBundle\tests\integration\Controller\Family;

use Akeneo\Test\Integration\Configuration;
use Pim\Bundle\ApiBundle\tests\integration\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class ListFamilyIntegration extends ApiTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->loadFamily(['code' => 'tshirt_family']);
        $this->loadFamily(['code' => 'shoes_family']);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->removeFamily('tshirt_family');
        $this->removeFamily('shoes_family');

        parent::tearDown();
    }

    /**
     * @param array
     */
    protected function loadFamily(array $data)
    {
        $family = $this->get('pim_catalog.factory.family')->create();
        $this->get('pim_catalog.updater.family')->update($family, $data);
        $this->get('pim_catalog.saver.family')->save($family);
    }

    /**
     * @param string $code
     */
    protected function removeFamily($code)
    {
        $family = $this->get('pim_catalog.repository.family')->findOneByIdentifier($code);
        $this->get('pim_catalog.remover.family')->remove($family);
    }

    public function testListLastUpdatedFamilies()
    {
        $client = $this->createAuthenticatedClient();
        $datetime = new \DateTime('yesterday');
        $dateString = $datetime->format(\DateTime::ISO8601);

        $searchParameters= json_encode([
            'updated' => [[
                'operator' => '>',
                'value' => $dateString
            ]]
        ]);
        $client->request('GET', 'api/rest/v1/families', ['search' => $searchParameters]);

        $expected = <<<JSON
{
    "_links": {
        "self": {
            "href": "http://localhost/api/rest/v1/families?page=1&limit=10&with_count=false&%s"
        },
        "first": {
            "href": "http://localhost/api/rest/v1/families?page=1&limit=10&with_count=false&%s"
        }
    },
    "current_page": 1,
    "_embedded": {
        "items": [
            {
                "_links": {
                    "self": {
                        "href": "http://localhost/api/rest/v1/families/shoes_family"
                    }
                },
                "code"                   : "shoes_family",
                "labels"                 : {},
                "attributes"             : ["sku"],
                "attribute_as_label"     : "sku",
                "attribute_requirements" : {
                    "ecommerce": ["sku"]
                }
            },{
                "_links": {
                    "self": {
                        "href": "http://localhost/api/rest/v1/families/tshirt_family"
                    }
                },
                "code"                   : "tshirt_family",
                "labels"                 : {},
                "attributes"             : ["sku"],
                "attribute_as_label"     : "sku",
                "attribute_requirements" : {
                    "ecommerce": ["sku"]
                }
            }
        ]
    }
}
JSON;

        $queryString = $client->getRequest()->getQueryString();
        $expected = sprintf($expected, $queryString, $queryString);

        $response = $client->getResponse();
        var_dump(json_decode($response->getContent()));
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString($expected, $response->getContent());
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new Configuration([Configuration::getMinimalCatalogPath()]);
    }
}
