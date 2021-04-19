<?php
namespace Drupal\Tests\mink_civicrm_helpers\Traits;

/**
 * Trait containing some useful helper functions for running Mink tests with CiviCRM
 */
trait Utils {

  protected function setUpExtension(string $key): void {
    /**
     * Not much point to these tests if our extension isn't installed!
     * But you need to have set the path to the extensions dir where you're
     * developing this extension, since it's expecting everything under
     * the simpletest directory, but that doesn't exist yet until the tests
     * start.
     * Set it either in phpunit.mink.xml with <env name="DEV_EXTENSION_DIR" value="path_to_ext_folder"/>
     * or as an environment variable if not using phpunit.mink.xml
     */
    if ($extdir = getenv('DEV_EXTENSION_DIR')) {
      \Civi::settings()->set('extensionsDir', $extdir);
      // Is there a better way to reset the extension system?
      \CRM_Core_Config::singleton(TRUE, TRUE);
      \CRM_Extension_System::setSingleton(new \CRM_Extension_System());
    }

    require_once 'api/api.php';
    civicrm_api3('Extension', 'install', ['keys' => $key]);
    // Drupal 8 is super cache-y.
    drupal_flush_all_caches();

    // Need this otherwise any new permissions aren't available yet.
    unset(\Civi::$statics['CRM_Core_Permission']['basicPermissions']);

    $this->configureCiviSettings();
  }

  /**
   * Miscellaneous civi settings that make it harder for errors to go unseen.
   *
   * You can either override this function in your test if you don't want
   * anything it does, or extend it using trait-renaming. For the latter, e.g.
   * in your `use` statement you would do:
   * use \Drupal\Tests\mink_civicrm_helpers\Traits\Utils {
   *   Utils::configureCiviSettings as utilsConfigureCiviSettings;
   * }
   * then in your override:
   * protected function configureCiviSettings(): void {
   *   $this->utilsConfigureCiviSettings();
   *   // do more stuff
   * }
   */
  protected function configureCiviSettings(): void {
    \Civi::settings()->add([
      // turn off the popup forms because ajax hides errors
      'ajaxPopupsEnabled' => 0,
      // display a backtrace on screen for exceptions
      'backtrace' => 1,
    ]);
  }

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
   * Confusingly this is not BROWSER_OUTPUT_DIRECTORY but seems to be hardcoded.
   * You can use this if you want your test to create a file that gets uploaded
   * as a viewable artifact at the end of the tests. Put it in this folder.
   * @return string
   */
  protected function getBrowserOutputDirectory(): string {
    return DRUPAL_ROOT . '/sites/simpletest/browser_output/';
  }

}
