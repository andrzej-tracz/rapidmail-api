Feature: OAuth authorization

  Scenario: I should get error when I utilize invalid Oauth credentials
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a "POST" request to "/oauth/v2/token" with body:
    """
    {
      "grant_type": "password",
      "client_id": "the-client-id-that-not-exists",
      "client_secret": "the-client-secret-that-not-exits"
    }
    """
    Then the response status code should be 400
    And the response should be in JSON
    And the JSON node "error" should not be null

  Scenario: Login via Client Credentials
    Given there are created OAuth Clients:
      | grant_type                | client_id                                              | secret                                            |
      | client_credentials        | 66disxxpi7c4o848og80oc0wgwogoocsc4wog0sg00gk4g0kwo     | zv2e2tzx2pwkgosk4owocwcookg84co4s0kk4484cogo400k8 |
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a "POST" request to "/oauth/v2/token" with body:
    """
    {
      "grant_type": "client_credentials",
      "client_id": "66disxxpi7c4o848og80oc0wgwogoocsc4wog0sg00gk4g0kwo",
      "client_secret": "zv2e2tzx2pwkgosk4owocwcookg84co4s0kk4484cogo400k8"
    }
    """
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "access_token" should not be null
    And the JSON nodes should contain:
      | token_type                | bearer     |

  Scenario: I should obtain access token when I logged via password
    Given there are created OAuth Clients:
      | grant_type      | client_id         | secret            |
      | password        | the_cliend_id     | the_client_secret |
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a "POST" request to "/oauth/v2/token" with body:
    """
    {
      "grant_type": "password",
      "username": "test-user@maesto.net",
      "password": "secret",
      "client_id": "the_cliend_id",
      "client_secret": "the_client_secret"
    }
    """
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "access_token" should not be null
    And the JSON nodes should contain:
      | token_type                | bearer     |

  Scenario: I should not obtain access token when I utilize bad credentials
    Given there are created OAuth Clients:
      | grant_type      | client_id           | secret              |
      | password        | the_client_id_2     | the_client_secret_2 |
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a "POST" request to "/oauth/v2/token" with body:
    """
    {
      "grant_type": "password",
      "username": "test-user@maesto.net",
      "password": "secret-wrong-password",
      "client_id": "the_client_id_2",
      "client_secret": "the_client_secret_2"
    }
    """
    Then the response status code should be 400
    And the response should be in JSON
    And the JSON node "error" should not be null
