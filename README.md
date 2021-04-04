# Mink CiviCRM Helpers

This provides a Drupal 8/9+ module that can be shared among Mink tests that run against CiviCRM. It provides a trait with some helper functions, like assertNoPageErrors() to check if any errors appear on the page or CiviCRM popped up any javascript error boxes on the page.

# Usage
1. composer require semperit/minkcivicrmhelpers
1. Include the following in your test class (inside the class definition not at the top of the file): `use \Drupal\Tests\mink_civicrm_helpers\Traits\Utils;`
