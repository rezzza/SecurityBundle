SecurityBundle
==============

[![Build Status](https://travis-ci.com/rezzza/SecurityBundle.png)](https://travis-ci.com/#!/rezzza/SecurityBundle)

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
- content - RAW_DATA (posted fields)

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
    ->set('request.method', 'POST')
    ->set('request.host', 'subdomain.domain.tld')
    ->set('request.path_info', '/path/to/resources')
    ->set('request.signature_time', time())
    ->set('request.content', 'myfield=myresult&my......');

$builder   = $this->get('rezzza.security.request_signature.builder');
$signature = $builder->build($context);
```

# Obfuscate request

If you have critical data coming on your application, you may not want to expose them into symfony profiler. You can easily define which data will not appear on this one on each routes.

```
rezzza_security:
    request_obfuscator:
        enabled: 1
```

In your route:

```

use \Rezzza\SecurityBundle\Controller\Annotations\ObfuscateRequest;

/**
 * @ObfuscateRequest()
 */
public function indexAction(Request $request)
{
}
```

Will obfuscate all datas on symfony profiler.

```
@obfuscate("content=*") // obfuscate $request->getContent()
@obfuscate("headers={'foobar'}") // obfuscate $request->headers->get('foobar')
@obfuscate("request_request={"customer[password]"}") // obfuscate $request->request->get('customer')['password']
```

Keys to obfuscate are:

- format
- content
- content_type
- status_text
- status_code
- request_query ($_GET)
- request_request ($_POST)
- request_headers ($_HEADER)
- request_server ($_SERVER)
- request_cookies ($_COOKIES)
- request_attributes ($request->attributes)
- response_headers
- session_metadata
- session_attributes
- flashes
- path_info
- controller
- locale

# WishList

- QueryString or HTTP Headers
- Unit Tests with atoum
