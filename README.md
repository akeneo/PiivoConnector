# PiiVO Connector

This extension purposes new API possibilities to connect PiiVO DAM with Akeneo PIM.
We are trying to create this extension to add more features on the current version but keep in mind we don't engage on its stability.


## Requirements

| PiiVO Connector     | Akeneo PIM Community Edition |
|:-------------------:|:----------------------------:|
| v1.1.*              | v2.*                         |
| v1.0.*              | v1.7.*                       |

## Installation
You can install this bundle with composer (see requirements section):

```bash
    php composer.phar require akeneo/piivo-connector:1.1.*
```

and enable the bundle in the `app/AppKernel.php` file in the `registerBundles()` method:

```php
    $bundles = [
        // ...
        new Piivo\Bundle\ConnectorBundle\PiivoConnectorBundle(),
        new Pim\Bundle\ExtendedAttributeTypeBundle\PimExtendedAttributeTypeBundle(),
    ]
```

If your installation is already set up, don't forget to clean your cache in production environment.

Adds routing in `app/config/routing.yml`:
```
piivo_api:
   resource: "@PiivoConnectorBundle/Resources/config/routing.yml"
   prefix: /api
```

## Elasticsearch indexes
As explained in the [ExtendedAttributeTypeBundle's README](https://github.com/akeneo/ExtendedAttributeTypeBundle/blob/v2.0.2/README.md),
you will also have to register the new Elasticsearch configuration files; in `app/config/pim_parameters.yml`, edit the 
`elasticsearch_index_configuration_files` parameter and add the following values:

```yaml
elasticsearch_index_configuration_files:
    - '%kernel.root_dir%/../vendor/akeneo/pim-community-dev/src/Pim/Bundle/CatalogBundle/Resources/elasticsearch/index_configuration.yml'
    - '%kernel.root_dir%/../vendor/akeneo/extended-attribute-type/src/Resources/config/elasticsearch/index_configuration.yml'
```

For the Enterprise edition, there is another file to register:
```yaml
elasticsearch_index_configuration_files:
    - '%kernel.root_dir%/../vendor/akeneo/pim-community-dev/src/Pim/Bundle/CatalogBundle/Resources/elasticsearch/index_configuration.yml'
    - '%kernel.root_dir%/../vendor/akeneo/pim-enterprise-dev/src/PimEnterprise/Bundle/WorkflowBundle/Resources/elasticsearch/index_configuration.yml'
    - '%kernel.root_dir%/../vendor/akeneo/extended-attribute-type/src/Resources/config/elasticsearch/index_configuration.yml'
    - '%kernel.root_dir%/../vendor/akeneo/extended-attribute-type/src/Resources/config/elasticsearch/index_configuration_ee.yml'    
```

If this is a fresh install, you can then proceed with a standard installation.

From an existing PIM, on the other hand, you will have to re-create your elasticsearch indexes:
```
    php bin/console cache:clear --no-warmup --env=prod
    php bin/console akeneo:elasticsearch:reset-indexes --env=prod
    php bin/console pim:product-model:index --all --env=prod
    php bin/console pim:product:index --all --env=prod
```

## Documentation

Some example of API usages can be found here: https://api.akeneo.com/
We keep API main rules for new routes and filters.


## Contributing

If you want to contribute to this open-source project, thank you to read and sign the following [contributor agreement](http://www.akeneo.com/contributor-license-agreement/)
