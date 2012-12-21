SecurityBundle
==============

# Installation

## With Composer

```json
    "require": {
        'rezzza/security-bundle': '1.*',
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

## On symfony 2.0

Add factory to your `security.yml`

```yml
security:
    factories:
        - "%kernel.root_dir%/../vendor/bundles/Rezzza/SecurityBundle/Resources/config/services/security.xml"
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

$context = new \Rezzza\SecurityBundle\Security\Firewall\Context();
$context->set('request.method', 'GET')
    ->set('request.host', 'subdomain.domain.tld')
    ->set('request.path_info', '/path/to/resources')
    ->set('request.signature_time', time())
    ->set('firewall.replay_protection', 'replayProtectionDefinedOnFirewall')
    ->set('firewall.algorithm', 'algorithmDefinedOnFirewall')
    ->set('firewall.secret', 'secretDefinedOnFirewall')
    ;

$builder   = $this->get('rezzza.security.request_signature.builder');
$signature = $builder->build($context);
```

You can define distant firewall on a config:

```yml
rezzza_security:
    firewalls:
        my_firewall:
            # algorithm:        'SHA1' default
            secret:            'IseeDeadPeopleEverywhere'
            # replay_protection: true # default

```

And then:

```php
$context = $this->get('rezzza.security.firewall.my_firewall.context')
    ->set('request.host', 'subdomain.domain.tld')
    ->set('request.path_info', '/path/to/resources')
    ->set('request.signature_time', time());

$builder   = $this->get('rezzza.security.request_signature.builder');
$signature = $builder->build($context);
```

# WishList

- QueryString or HTTP Headers
- Unit Tests with atoum
