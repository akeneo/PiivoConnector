<?php

namespace Piivo\Bundle\ConnectorBundle\tests\integration\Controller\Version;

use Akeneo\Test\Integration\Configuration;
use Pim\Bundle\ApiBundle\tests\integration\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class ListVersionIntegration extends ApiTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->loadFamily(['code' => 'tshirt_family']);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->removeFamily('tshirt_family');

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

    public function testListDeletedFamilies()
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
        $client->request('GET', 'api/rest/v1/versions/deleted/family'/*, ['search' => $searchParameters]*/);

        $expected = <<<JSON
{
    "_links": {
        "self": {
            "href": "http://localhost/api/rest/v1/versions/deleted/family?page=1&limit=10&with_count=false&%s"
        },
        "first": {
            "href": "http://localhost/api/rest/v1/versions/deleted/family?page=1&limit=10&with_count=false&%s"
        }
    },
    "current_page": 1,
    "_embedded": {
        "items": [
            {
                "code" : "shoes_family"
            },{
                "code" : "tshirt_family"
            }
        ]
    }
}
JSON;

        $queryString = $client->getRequest()->getQueryString();
        $expected = sprintf($expected, $queryString, $queryString);

        $response = $client->getResponse();
        var_dump($response->getContent());
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
