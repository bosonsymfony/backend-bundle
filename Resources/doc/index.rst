1- Registrar los bundles en AppKernel

```php

    // ...
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new UCI\Boson\BackendBundle\BackendBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
        )
    }
```

2- Confugirar las rutas:

```yml
# app/config/routing.yml

# ...
backend:
    resource: "@BackendBundle/Resources/config/routing.yml"

fos_js_routing:
    resource: "@FOSJsRoutingBundle/Resources/config/routing/routing.xml"
# ...

3- Instalar los assets:


```bash

    php app/console assets:install --symlink
```

4- Abrir el navegador y poner la siguiente url
http://<ruta_de_la_app>/backend