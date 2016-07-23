Feature: Registering a cache into a pimple container
  In order to easily use the cache
  As a library user
  I need to be able to register a cache service provider

  Background: The pimple container is empty
  
  Scenario: Registering the provider without options builds a default cache
    Given I register the service provider without options 
    When I retrieve the cache
    Then I should get an array cache

  Scenario: When multiple backends are given a chain should be built
    Given I register the service provider with multiple backends
    When I retrieve the cache
    Then I should get an chain cache

  Scenario: The cache can be tweaked with logging and silencing exceptions
    Given I register the service provider with logging and silent mode
    When I retrieve the cache
    Then I should get an logging decorated cache
