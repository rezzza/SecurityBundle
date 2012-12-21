SecurityBundle
==============

# Installation

## With Composer

```json
    "require": {
        'rezzza/security-bundle': '1.*',
        ....
    }
```

## Enable Bundle

In `AppKernel`:

```php
    $bundles = array(
        //....
        new Rezzza\SecurityBundle\RezzzaSecurityBundle(),
        //....
    );
```

# Request signature checker

Validate a signature sent by client in query string, this signature can have a lifetime.

Criterias are:

- Time send on signature (if replay_protection activated)
- RequestMethod
- http host
- path info
- content - RAW_DATA (query string has not this information)

It'll hash all theses criterias with a secret defined on `security.yml`, example:

```yaml
# security.yml
    firewalls:
        api:
            pattern: ^/api/.*
            request_signature:
                algorithm: SHA1
                # you can easily ignore this when use functional tests by example
                ignore:    %request_signature.ignore%
                # secret of symfony application or an other one
                secret:    %secret%
                # http://.............?_signature=....
                parameter: _signature
                # Do you want to add a lifetime criteria ? By this way the signature will be transitory
                replay_protection:
                    enabled:   true
                    lifetime:  600
                    parameter: _signature_ttl

```

Build the signature:

```php
use \Rezzza\SecurityBundle\Security\Authentication\RequestDataCollector;

$entryPoint = $this->get('rezzza.security.request_signature.entry_point.api');  // api is the name of firewall.
$builder    = $this->get('rezzza.security.request_signature.builder');

// create accepts 5 arguments: method, host, pathInfo, content, time where the request is executed (default time())
$dataCollector = RequestDataCollector::create('GET', 'subdomain.domain.tld', '/path/to/resources.format');
$signature     = $builder->build($dataCollector, $entryPoint);
```


# WishList

- QueryString or HTTP Headers
- Unit Tests with atoum
