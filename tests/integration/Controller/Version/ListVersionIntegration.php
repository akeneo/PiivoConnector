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

        $this->loadAttribute(
            ['code' => 'name_attribute', 'type' => 'pim_catalog_text']
        );
        $this->loadAttribute(
            ['code' => 'deleted_attribute', 'type' => 'pim_catalog_text']
        );

        $this->loadCategory(['code' => 'deleted_tree', 'parent' => null]);
        $this->loadCategory(['code' => 'node', 'parent' => 'deleted_tree']);
        $this->loadCategory(['code' => 'deleted_node', 'parent' => 'deleted_tree']);

        $this->loadFamily(['code' => 'tshirt_family']);
        $this->loadFamily(['code' => 'tie_family']);
        $this->loadFamily(['code' => 'deleted_family']);

        $this->loadProduct('my_sku');
        $this->loadProduct('deleted_product');
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->removeAttribute('name_attribute');
        $this->removeAttribute('deleted_attribute');

        $this->removeCategory('node');
        $this->removeCategory('deleted_tree');
        $this->removeCategory('deleted_node');

        $this->removeFamily('tshirt_family');
        $this->removeFamily('tie_family');
        $this->removeFamily('deleted_family');

        $this->removeProduct('my_sku');
        $this->removeProduct('deleted_product');

        parent::tearDown();
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
     * @param array $data
     */
    protected function loadCategory(array $data)
    {
        $category = $this->get('pim_catalog.factory.category')->create();
        $this->get('pim_catalog.updater.category')->update($category, $data);
        $this->get('pim_catalog.saver.category')->save($category);
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
     * @param array
     */
    protected function loadProduct($identifier)
    {
        $product = $this->get('pim_catalog.builder.product')->createProduct($identifier);
        $this->get('pim_catalog.saver.product')->save($product);
    }

    /**
     * @param string $code
     */
    protected function removeAttribute($code)
    {
        $attribute = $this->get('pim_catalog.repository.attribute')->findOneByIdentifier($code);
        if (null !== $attribute) {
            $this->get('pim_catalog.remover.attribute')->remove($attribute);
        }
    }

    /**
     * @param string $code
     */
    protected function removeCategory($code)
    {
        $category = $this->get('pim_catalog.repository.category')->findOneByIdentifier($code);
        if (null !== $category) {
            $this->get('pim_catalog.remover.category')->remove($category);
        }
    }

    /**
     * @param string $code
     */
    protected function removeFamily($code)
    {
        $family = $this->get('pim_catalog.repository.family')->findOneByIdentifier($code);
        if (null !== $family) {
            $this->get('pim_catalog.remover.family')->remove($family);
        }
    }

    /**
     * @param string $code
     */
    protected function removeProduct($code)
    {
        $product = $this->get('pim_catalog.repository.product')->findOneByIdentifier($code);
        if (null !== $product) {
            $this->get('pim_catalog.remover.product')->remove($product);
        }
    }

    public function testListDeletedFamilies()
    {
        $this->removeFamily('deleted_family');

        $client = $this->createAuthenticatedClient();
        $datetime = new \DateTime('yesterday');
        $dateString = $datetime->format(\DateTime::ISO8601);

        $searchParameters= json_encode([
            'logged_at' => $dateString
        ]);
        $client->request('GET', 'api/rest/v1/versions/deleted/family', ['search' => $searchParameters]);

        $expected = <<<JSON
{
    "_embedded": {
        "items": [
            {
                "code" : "deleted_family"
            }
        ]
    }
}
JSON;

        $response = $client->getResponse();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString($expected, $response->getContent());
    }

    public function testListDeletedAttributes()
    {
        $this->removeAttribute('deleted_attribute');

        $client = $this->createAuthenticatedClient();
        $datetime = new \DateTime('yesterday');
        $dateString = $datetime->format(\DateTime::ISO8601);

        $searchParameters= json_encode([
            'logged_at' => $dateString
        ]);
        $client->request('GET', 'api/rest/v1/versions/deleted/attribute', ['search' => $searchParameters]);

        $expected = <<<JSON
{
    "_embedded": {
        "items": [
            {
                "code" : "deleted_attribute"
            }
        ]
    }
}
JSON;

        $response = $client->getResponse();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString($expected, $response->getContent());
    }

    public function testListDeletedCategories()
    {
        $this->removeCategory('deleted_node');

        $client = $this->createAuthenticatedClient();
        $datetime = new \DateTime('yesterday');
        $dateString = $datetime->format(\DateTime::ISO8601);

        $searchParameters= json_encode([
            'logged_at' => $dateString
        ]);
        $client->request('GET', 'api/rest/v1/versions/deleted/category', ['search' => $searchParameters]);

        $expected = <<<JSON
{
    "_embedded": {
        "items": [
            {
                "code" : "deleted_node"
            }
        ]
    }
}
JSON;

        $response = $client->getResponse();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString($expected, $response->getContent());
    }

    public function testListDeletedCategoriesDeletingTree()
    {
        $this->removeCategory('deleted_tree');

        $client = $this->createAuthenticatedClient();
        $datetime = new \DateTime('yesterday');
        $dateString = $datetime->format(\DateTime::ISO8601);

        $searchParameters= json_encode([
            'logged_at' => $dateString
        ]);
        $client->request('GET', 'api/rest/v1/versions/deleted/category', ['search' => $searchParameters]);

        $expected = <<<JSON
{
    "_embedded": {
        "items": [
            {
                "code" : "deleted_tree"
            }
        ]
    }
}
JSON;

        $response = $client->getResponse();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString($expected, $response->getContent());
    }

    public function testListDeletedProducts()
    {
        $this->removeProduct('deleted_product');

        $client = $this->createAuthenticatedClient();
        $datetime = new \DateTime('yesterday');
        $dateString = $datetime->format(\DateTime::ISO8601);

        $searchParameters= json_encode([
            'logged_at' => $dateString
        ]);
        $client->request('GET', 'api/rest/v1/versions/deleted/product', ['search' => $searchParameters]);

        $expected = <<<JSON
{
    "_embedded": {
        "items": [
            {
                "code" : "deleted_product"
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
        return new Configuration([Configuration::getMinimalCatalogPath()]);
    }
}
