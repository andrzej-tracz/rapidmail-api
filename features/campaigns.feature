
@group api-campaigns
Feature: Campaigns Management

  Scenario: I should not be able to create Subscriber when I am not logged in
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a "POST" request to "/api/campaigns" with body:
    """
    {
      "name": "Test Campaign"
    }
    """
    And the response status code should be 401
    And the response should be in JSON

  Scenario: When I have valid access token I should get error when request payload is invalid
    When there is a valid access token "secret-access-token-campaigns" which belongs to general user
    When I add "Content-Type" header equal to "application/json"
    When I add "Authorization" header equal to "Bearer secret-access-token-campaigns"
    And I add "Accept" header equal to "application/json"
    And I send a "POST" request to "/api/campaigns" with body:
    """
    {
      "name": "Test Campaign",
      "title": "New Campaign",
      "preheader": "Test preheader",
      "fromName": "John Foo",
      "fromEmail": "john.foo@gmail.com",
      "replyTo": "john.foo@gmail.com",
      "isScheduled": "false",
    }
    """
    And the response status code should be 400
    And the response should be in JSON
