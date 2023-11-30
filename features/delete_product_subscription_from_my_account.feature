@customer_account
Feature: Ability to delete a particular subscription from my account

  Background:
    Given the store operates on a single channel in "United States"
    And the store has a product "Knitted cap apple"
    And the product "Knitted cap apple" is out of stock
    And there is a customer "Francis Underwood" identified by an email "francis@underwood.com" and a password "whitehouse"
    And I am logged in as "francis@underwood.com"

  @ui @javascript
  Scenario: Being able to delete a subscription from my account
    When I view product "Knitted cap apple"
    And I subscribe to the alert list for the product "Knitted cap apple"
    And I browse to my product subscriptions
    Then I should see only one subscription
    And I delete the first subscription
    Then I should be notified that the subscription has been successfully deleted
    And there should be no subscriptions
