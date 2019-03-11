<?php
/**
 * Template ENgine.
 *
 * Page body template (WWW).
 *
 * @copyright <a href="http://donbidon.rf.gd/" target="_blank">donbidon</a>
 * @license   https://opensource.org/licenses/mit-license.php
 */

foreach ($scope["messages"] as $message) {
    echo $message;
}

?>
<div>
<?php echo $this->l("body"); ?>

</div>
