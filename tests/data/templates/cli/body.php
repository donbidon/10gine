<?php
/**
 * Template ENgine.
 *
 * Page body template (CLI).
 *
 * @copyright <a href="http://donbidon.rf.gd/" target="_blank">donbidon</a>
 * @license   https://opensource.org/licenses/mit-license.php
 */

foreach ($scope["messages"] as $message) {
    echo $message, PHP_EOL;
}

?>

<?php echo $this->l("body"); ?>

