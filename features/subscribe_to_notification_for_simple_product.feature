@product_inventory
Feature: Ability to get notified for a specific product when it will be back in stock
  In order to get notified for a back in stock of a specific product
  As a Visitor
  I want the possibility to subscribe me to an alert list

  Background:
    Given the store operates on a single channel in "United States"
    And the store has a product "Knitted cap banana"
    And the product "Knitted cap banana" is out of stock

  @ui
  Scenario: Being able to subscribe to the alert list for the out of stock product as logged customer
    Given there is a customer "Francis Underwood" identified by an email "francis@underwood.com" and a password "whitehouse"
    And I am logged in as "francis@underwood.com"
    When I view product "Knitted cap banana"
    And I subscribe to the alert list for the product "Knitted cap banana"
    Then I should be notified that the email is subscribed correctly
    And an email with a success message should be sent to "francis@underwood.com"

  @ui
  Scenario: Being able to subscribe to the alert list for the out of stock product
    When I view product "Knitted cap banana"
    And I subscribe to the alert list for the product "Knitted cap banana" with the email "ted@example.com"
    Then I should be notified that the email is subscribed correctly
    And an email with a success message should be sent to "ted@example.com"