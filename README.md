SecurityBundle
==============

# Request signature checker

It'll validate a signature send by client in query string (later on headers), this signature can have a lifetime.

Criterias are:
    - Time send on signature (if replay_protection activated)
    - RequestMethod
    - http host
    - path info
    - content (query string are not part of this)

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


# WishList

- QueryString or HTTP Headers
- Unit Tests
