<?php

namespace Piivo\Bundle\ConnectorBundle\tests\integration\Controller\Category;

use Akeneo\Component\Classification\Model\Category;
use Akeneo\Test\Integration\Configuration;
use Pim\Bundle\ApiBundle\tests\integration\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class ListCategoryIntegration extends ApiTestCase
{

    protected function setUp()
    {
        static::bootKernel();
        self::$count++;

        $this->loadCategory(['code' => 'tree1', 'parent' => null]);
        $this->loadCategory(['code' => 'tree2', 'parent' => null]);
        $this->loadCategory(['code' => 'parent1', 'parent' => 'tree1']);
        $this->loadCategory(['code' => 'parent2', 'parent' => 'tree1']);
        $this->loadCategory(['code' => 'leaf1', 'parent' => 'parent1']);
        $this->loadCategory(['code' => 'leaf2', 'parent' => 'parent2']);
        $this->loadCategory(['code' => 'leaf3', 'parent' => 'tree2']);
    }

    protected function tearDown()
    {
        $this->removeCategory('tree1');
        $this->removeCategory('tree2');
        $this->removeCategory('parent1');
        $this->removeCategory('parent2');
        $this->removeCategory('leaf1');
        $this->removeCategory('leaf2');
        $this->removeCategory('leaf3');

        parent::tearDown();
    }

    protected function loadCategory(array $data)
    {
        $category = $this->get('pim_catalog.factory.category')->create();
        $this->get('pim_catalog.updater.category')->update($category, $data);
        $this->get('pim_catalog.saver.category')->save($category, ['flush' => false]);
    }

    protected function removeCategory($code)
    {
        $category = $this->get('pim_catalog.repository.category')->findOneByIdentifier($code);
        $this->get('pim_catalog.remover.category')->remove($category);

    }

    public function testListCategories()
    {
        $client = $this->createAuthenticatedClient();

        $client->request('GET', 'api/rest/v1/categories');

        $expected = <<<JSON
{
    "_links": {
        "self": {
            "href": "http://localhost/api/rest/v1/categories?page=1&limit=10&with_count=false"
        },
        "first": {
            "href": "http://localhost/api/rest/v1/categories?page=1&limit=10&with_count=false"
        }
    },
    "current_page": 1,
    "_embedded": {
        "items": [
            {
                "_links": {
                    "self": {
                        "href": "http://localhost/api/rest/v1/categories/master"
                    }
                },
                "code": "master",
                "parent": null,
                "labels": {}
            },
            {
                "_links": {
                    "self": {
                        "href": "http://localhost/api/rest/v1/categories/categoryA"
                    }
                },
                "code": "categoryA",
                "parent": "master",
                "labels": {
                    "en_US": "Category A",
                    "fr_FR": "Catégorie A"
                }
            },
            {
                "_links": {
                    "self": {
                        "href": "http://localhost/api/rest/v1/categories/categoryA1"
                    }
                },
                "code": "categoryA1",
                "parent": "categoryA",
                "labels": {}
            },
            {
                "_links": {
                    "self": {
                        "href": "http://localhost/api/rest/v1/categories/categoryA2"
                    }
                },
                "code": "categoryA2",
                "parent": "categoryA",
                "labels": {}
            },
            {
                "_links": {
                    "self": {
                        "href": "http://localhost/api/rest/v1/categories/categoryB"
                    }
                },
                "code": "categoryB",
                "parent": "master",
                "labels": {}
            },
            {
                "_links": {
                    "self": {
                        "href": "http://localhost/api/rest/v1/categories/master_china"
                    }
                },
                "code": "master_china",
                "parent": null,
                "labels": {}
            }
        ]
    }
}
JSON;

        $response = $client->getResponse();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString($expected, $response->getContent());
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        var_dump(Configuration::getTechnicalCatalogPath());

        return new Configuration(
            [Configuration::getTechnicalCatalogPath()],
            false
        );
    }
}
