# PiivoConnectorBundle

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
    ]
```

If your installation is already set up, don't forget to clean your cache in production environment.


## Documentation

Some example of API usages can be found here: https://api.akeneo.com/
We keep API main rules for new routes and filters.


## Contributing

If you want to contribute to this open-source project, thank you to read and sign the following [contributor agreement](http://www.akeneo.com/contributor-license-agreement/)
