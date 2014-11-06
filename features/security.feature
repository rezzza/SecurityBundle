Feature: Check security config works
    As a web user
    In order to access protected resource
    I should provide right signature

    Scenario: No signature provided should lead to 403
        When I am on "/hello"
        Then the response status code should be 403

    Scenario: Good signature provided should lead to 200
        When I am on "/hello" with good signature
        Then the response status code should be 200
        And I should see "posay"

    Scenario: Signature life expired should lead to 403
        When I am on "/hello" with good signature but I wait 3 seconds before perform request
        Then the response status code should be 403
