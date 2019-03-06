<?php declare(strict_types=1);

/**
 * Template ENgine.
 *
 * @copyright <a href="http://donbidon.rf.gd/" target="_blank">donbidon</a>
 * @license   https://opensource.org/licenses/mit-license.php
 */

namespace donbidon;

use \InvalidArgumentException;
use \RuntimeException;

/**
 * Template ENgine.
 *
 * @todo API documentation.
 */
class TENgine
{
    const DEFAULT_LOCALE = "en";

    /**
     * Supported locales
     *
     * @var array
     *
     * @see self::getSupportedLocales()
     */
    protected $locales;

    /**
     * Loaded locale data
     *
     * @var array
     */
    protected $localeData;

    /**
     * Supported output modes
     *
     * @var array
     *
     * @see self::getSupportedModes()
     */
    protected $modes;

    /**
     * Path to templates
     *
     * @var string
     */
    protected $path;

    /**
     * Output mode
     *
     * @var string
     */
    protected $mode;

    /**
     * DumbTemplate constructor.
     *
     * @param string $localesPath
     * @param string $templatesPath
     * @param string $locale
     * @param string $mode           Output mode (cli, html, etc.)
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function __construct(string $localesPath, string $templatesPath, string $locale = "", string $mode = "")
    {
        if (!file_exists(sprintf("%s/.tag", $localesPath))) {
            throw new InvalidArgumentException(sprintf(
                "Invalid locales path \"%s\"",
                $localesPath
            ));
        }
        $this->locales = array_map(
            function($name) {
                return basename($name, ".php");
            },
            glob(sprintf("%s/*.php", $localesPath))
        );
        if (0 === sizeof($this->locales)) {
            throw new RuntimeException(sprintf(
                "Empty locales folder \"%s\"",
                $localesPath
            ));
        }
        if ("" === $locale) {
            $locale = self::DEFAULT_LOCALE;
        }
        if (!in_array($locale, $this->locales)) {
            throw new InvalidArgumentException(sprintf(
                "Unsupported locale \"%s\" (supported: %s)",
                $locale,
                implode(", ", $this->locales)
            ));
        }
        $localePath = sprintf("%s/%s.php", $localesPath, $locale);
        $this->localeData = require($localePath);
        if (!is_array($this->localeData)) {
            throw new RuntimeException(sprintf(
                "Invalid locales file \"%s\"",
                $localePath
            ));
        }

        if (!file_exists(sprintf("%s/.tag", $templatesPath))) {
            throw new InvalidArgumentException(sprintf(
                "Invalid templates path \"%s\"",
                $templatesPath
            ));
        }
        $this->modes = array_map(
            "basename",
            array_filter(glob(sprintf("%s/*", $templatesPath)), "is_dir")
        );
        if (0 === sizeof($this->modes)) {
            throw new RuntimeException(sprintf(
                "Empty templates folder \"%s\"",
                $templatesPath
            ));
        }
        if ("" === $mode) {
            $mode = "cli" === php_sapi_name() ? "cli" : "html";
        }
        if (!in_array($mode, $this->modes)) {
            throw new InvalidArgumentException(sprintf(
                "Unsupported mode \"%s\" (supported: %s)",
                $mode,
                implode(", ", $this->modes)
            ));
        }
        $this->path = $templatesPath;
        $this->mode = $mode;
    }

    /**
     * Returns supported locales.
     *
     * @return array
     */
    public function getLocales(): array
    {
        return $this->locales;
    }

    /**
     * Returns supported output modes.
     *
     * @return array
     */
    public function getModes(): array
    {
        return $this->modes;
    }

    /**
     * Returns localized string according to id and arguments.
     *
     * @param string $id
     * @param array  $args
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public function localize(string $id, array $args = []): string
    {
        if (!isset($this->localeData[$id])) {
            throw new InvalidArgumentException(sprintf(
                "Unknown string id \"%s\"",
                $id
            ));
        }

        return vsprintf($this->localeData[$id], $args);
    }

    /**
     * Renders template according to locales and passed scope.
     *
     * @param string $template
     * @param array  $scope
     *
     * @return string
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function render(string $template, array $scope = []): string
    {
        $path = sprintf("%s/%s/%s.phtml", $this->path, $this->mode, $template);
        if (!is_file($path)) {
            throw new InvalidArgumentException(sprintf(
                "Template \"%s\" not found",
                sprintf("%s/%s.phtml", $this->mode, $template)
            ));
        }
        if (!is_readable($path)) {
            throw new RuntimeException(sprintf(
                "Template \"%s\" not readable",
                sprintf("%s/%s.phtml", $this->mode, $template)
            ));
        }
        $locales = $this->localeData;
        ob_start();
        require $path;
        $rendered = ob_get_contents();
        ob_end_clean();

        return $rendered;
    }
}
