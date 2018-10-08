@group register
Feature: Account Registration

  Scenario: I should be able to create new account
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a "POST" request to "/api/account/register" with body:
    """
    {
      "name": "My Awesome Account Name",
      "email": "my-new-account-email@maesto.net",
      "password": "secret1234"
    }
    """
    And the response status code should be 201
    And the response should be in JSON
    And the JSON node "name" should be equal to "My Awesome Account Name"
    And the JSON node "email" should be equal to "my-new-account-email@maesto.net"

  Scenario: I should get error when utilizing invalid email
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a "POST" request to "/api/account/register" with body:
    """
    {
      "name": "My Awesome Account Name",
      "email": "thisis-not-real-email@dev.fake",
      "password": "fgffgfasfdfgsd"
    }
    """
    And the response status code should be 400
    And the response should be in JSON
