<?php

namespace Piivo\Bundle\ConnectorBundle\tests\integration\Controller\AttributeOption;

use Akeneo\Test\Integration\Configuration;
use Pim\Bundle\ApiBundle\tests\integration\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class DeleteAttributeOptionIntegration extends ApiTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->loadAttribute([
            'code' => 'my_images',
            'type' => 'pim_catalog_text_collection'
        ]);
        $this->loadProduct('my_sku');
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->removeAttribute('my_images');
        $this->removeProduct('my_sku');

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
     * @param array
     */
    protected function loadProduct($identifier)
    {
        $product = $this->get('pim_catalog.builder.product')->createProduct($identifier);

        $productData = ['my_images' => [['data' => ['bar', 'foo'], 'locale' => null, 'scope' => null]]];

        $this->get('pim_catalog.updater.product')->update($product, ['values' => $productData]);
        $this->get('pim_catalog.saver.product')->save($product);
    }

    /**
     * @param string $code
     */
    protected function removeAttribute($code)
    {
        $attribute = $this->get('pim_catalog.repository.attribute')->findOneByIdentifier($code);
        $this->get('pim_catalog.remover.attribute')->remove($attribute);
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

    public function testListTextAttributes()
    {
        $client = $this->createAuthenticatedClient();
        $client->request('DELETE', 'api/rest/v1/attributes/my_images/items/foo');

        $response = $client->getResponse();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());

        $product = $this->get('pim_catalog.repository.product')->findOneByIdentifier('my_sku');
        $this->assertSame(['bar'], $product->getValue('my_images')->getTextCollection());
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new Configuration([Configuration::getMinimalCatalogPath()]);
    }
}
