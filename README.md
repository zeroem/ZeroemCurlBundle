# RemoteHttpKernel
The RemoteHttpKernel is provided as an alternative to the standard HttpKernel in Symfony.  Rather than using the
local application, it processes the Request object via cURL, parsing the results into a proper Response object.

# Installation
First, checkout a copy of the code. Just add the following to the ``deps`` 
file of your Symfony Standard Distribution:

    [remotehttpkernel]
        git=git://github.com/zeroem/ZeroemCurlBundle.git
        target=/bundles/Zeroem/CurlBundle/

Then add the bundle to your AppKernel and register the namespace with the autoloader:

```php
    // app/AppKernel.php
    $bundles = array(
        // ...
        new Zeroem\CurlBundle\ZeroemCurlBundle(),        
        // ...
    );
```

```php
    // app/autoload.php
    $loader->registerNamespaces(array(
        // ...
        'Zeroem'              => __DIR__.'/../vendor/bundles'
        // ...
    ));
```

Now use the ``vendors`` script to clone the newly added repository into your project:

```shell
    php bin/vendors install
```

# Example Usage

```php
<?php

use Symfony\Component\HttpFoundation\Request;
use Zeroem\CurlBundle\HttpKernel\RemoteHttpKernel;

$request = Request::create("http://www.symfony.com");
$remoteKernel = new RemoteHttpKernel();
$response = $remoteKernel->handle($request);

$response->send();

```

# Goals
Provide a clean, Object Oriented solution for interacting with remote HTTP services that doesn't require
knowing the ins and outs of all the various cURL api options nor invents it's own HTTP Request/Response
architecture.

# Motivation
I was originally looking for a Symfony Bundle that provided an OO interface to the curl_* functions.  Along the 
way, I realized that I didn't actually want a cURL wrapper, I wanted something that could execute HTTP requests 
and return the result in a meaningful way.  So I decided to use the existing Symfony components and build a 
custom HttpKernelInterprovided, relegating cURL to an implementation detail rather than the purpose of the 
project.
