# PiiVO Connector

This extension purposes new API possibilities to connect PiiVO DAM with Akeneo PIM.
We are trying to create this extension to add more features on the current version but keep in mind we don't engage on its stability.


## Requirements

| PiiVO Connector     | Akeneo PIM Community Edition |
|:-------------------:|:----------------------------:|
| v1.0.*              | v1.7.*                       |

## Installation
You can install this bundle with composer (see requirements section):

```bash
    php composer.phar require akeneo/piivo-connector:1.0.*
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

### (Optionnal) Example bundles

This connector is shipped with complete example bundle, especially to override the ProductValue model.
This is needed to use the new TextCollection attribute type.

The easiest way to enable it is to use a symbolic link:

```
cd src
ln -s ../vendor/akeneo/extended-attribute-type/doc/example/Pim Pim
```

In Community edition,
After that, you will be able to use the example bundles in `app/AppKernel.php`:

```php
    protected function registerProjectBundles()
    {
        return [
            new Pim\Bundle\ExtendedAttributeTypeBundle\PimExtendedAttributeTypeBundle(),
            new Pim\Bundle\PiivoConnectorBundle\PimPiivoConnectorBundle(),
            new Pim\Bundle\ExtendedCeBundle\ExtendedCeBundle(),   // example CE bundle to activate the extended attributes
        ];
    }
```

```
akeneo_storage_utils:
    mapping_overrides:
        -
            original: Pim\Component\Catalog\Model\ProductValue
            override: Pim\Bundle\ExtendedCeBundle\Model\ProductValue
```

In Enterprise edition:
After that, you will be able to use the example bundles in `app/AppKernel.php`:

```php
    protected function registerProjectBundles()
    {
        return [
            new Pim\Bundle\ExtendedAttributeTypeBundle\PimExtendedAttributeTypeBundle(),
            new Pim\Bundle\PiivoConnectorBundle\PimPiivoConnectorBundle(),
            new Pim\Bundle\ExtendedEeBundle\ExtendedEeBundle(), // example EE bundle to activate the extended attributes
        ];
    }
```

```
akeneo_storage_utils:
    mapping_overrides:
        -
            original: PimEnterprise\Component\Catalog\Model\ProductValue
            override: Pim\Bundle\ExtendedEeBundle\Model\ProductValue
        -
            original: PimEnterprise\Component\Workflow\Model\PublishedProductValue
            override: Pim\Bundle\ExtendedEeBundle\Model\PublishedProductValue
```

## Documentation

Some example of API usages can be found here: https://api.akeneo.com/
We keep API main rules for new routes and filters.


## Contributing

If you want to contribute to this open-source project, thank you to read and sign the following [contributor agreement](http://www.akeneo.com/contributor-license-agreement/)
