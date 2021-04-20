# Mink CiviCRM Helpers

This provides a Drupal 8/9+ module that can be shared among Mink tests that run against CiviCRM. It provides a trait with some helper functions, like assertNoPageErrors() to check if any errors appear on the page or CiviCRM popped up any javascript error boxes on the page.

It also provides a basic starting point for your setUp function which is likely to be common across all tests.

# Usage
1. composer require semperit/minkcivicrmhelpers
1. Include the following in your test class (inside the class definition not at the top of the file):
   ```php
   use \Drupal\Tests\mink_civicrm_helpers\Traits\Utils;
   ```
1. Include the following var declaration in the class (this tells the drupal test system what it needs during bootstrap):
   ```php
   /**
    * @var array
    */
   protected static $modules = [
     'mink_civicrm_helpers',
   ];
   ```
1. Then typically your setUp() functions will look like this, which installs the extension and does some initial basic config:
   ```php
   public function setUp(): void {
     parent::setUp();
     $this->setUpExtension('_put_your_extension_key_here_');
   }
   ```
1. For further usage see the code comments inside the trait.
