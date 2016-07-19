Feature: Easy access from the pimple container
  In order use the cache classes easily from the container
  As a library user
  I need to be able to register the provider

  Scenario: Register an array cache
    Given The container is empty
    When I pass the options to build the array access to the provider
    Then I should retrieve the array cache object

  Scenario: Register an redis cache from the parameters
    Given The container is empty
    When I pass the options to build the redis cache from parameters to the provider
    Then I should retrieve the redis cache object

  Scenario: Register an redis cache from an existing connection
    Given The container is empty
    When I pass the options to build the redis cache from an open connection to the provider
    Then I should retrieve the redis cache object

  Scenario: If no options are give, it defaults to an array cache
    Given The container is empty
    When I register the provider without any option
    Then I should retrieve the array cache object

  Scenario: It is able to decorate the cache for debugging
    Given The container is empty
    When I register the provider with the debug flag set to true
    Then I should retrieve the test decorated cache

  Scenario: The factory throws an exception if the backend is not supported
    Given The container is empty
    When I request an unknown backend cache instance to the factory
    Then And exception should be thrown