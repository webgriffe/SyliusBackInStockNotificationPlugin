@product_inventory
Feature: Ability to get notified for a specific product when it will be back in stock
  In order to get notified for a back in stock of a specific product
  As a Visitor
  I want the possibility to subscribe me to an alert list

  Background:
    Given the store operates on a single channel in "United States"
    And the store has a "T-shirt banana" configurable product
    And this product has option "Size" with values "Small" and "Large"
    And this product has all possible variants
    But the product "T-shirt banana" is out of stock

  @ui @javascript
  Scenario: Being able to subscribe to the alert list for the out of stock product as logged customer
    Given there is a customer "Francis Underwood" identified by an email "francis@underwood.com" and a password "whitehouse"
    And I am logged in as "francis@underwood.com"
    When I view product "T-shirt banana"
    And I select its Size as "Large"
    And I select its Size as "Small"
    And I subscribe to the alert list for the product "T-shirt banana"
    Then I should be notified that the email is subscribed correctly
    And an email with a success message should be sent to "francis@underwood.com"

  @ui @javascript
  Scenario: Being able to subscribe to the alert list for the out of stock product
    When I view product "T-shirt banana"
    And I select its Size as "Large"
    And I select its Size as "Small"
    And I subscribe to the alert list for the product "T-shirt banana" with the email "ted@example.com"
    Then I should be notified that the email is subscribed correctly
    And an email with a success message should be sent to "ted@example.com"