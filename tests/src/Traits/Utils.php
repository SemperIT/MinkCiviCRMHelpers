<?php
namespace Drupal\Tests\mink_civicrm_helpers\Traits;

/**
 * Trait containing some useful helper functions for running Mink tests with CiviCRM
 */
trait Utils {

  /**
   * Asserts the page has no error messages.
   */
  protected function assertPageHasNoErrorMessages(): void {
    $error_messages = $this->getSession()->getPage()->findAll('css', '.messages.messages--error');
    $this->assertCount(0, $error_messages, implode(', ', array_map(static function(\Behat\Mink\Element\NodeElement $el) {
      return $el->getValue();
    }, $error_messages)));

    // Check civi status messages
    // This would be a more robust way but it doesn't work here because once the
    // message is displayed it's removed from the array, so we're too late.
    /*
    $session_messages = array_filter(\CRM_Core_Session::singleton()->getStatus(), function($x) {
      return ($x['type'] === 'error');
    });
    $this->assertEmpty($session_messages);
     */

    // This does almost the same thing but using the UI. This might be a little
    // more comprehensive because some of these are generated purely from
    // javascript, but is tied to the css currently used by the popup.
    $civi_popups = $this->getSession()->getPage()->find('css', '.error.ui-notify-message');
    $this->assertNull($civi_popups);
  }

  /**
   * Confusingly this is not BROWSER_OUTPUT_DIRECTORY but seems to be hardcoded
   * @return string
   */
  protected function getBrowserOutputDirectory(): string {
    return DRUPAL_ROOT . '/sites/simpletest/browser_output/';
  }

}
