<?php
/**
 * TENgine class unit tests.
 *
 * @copyright <a href="http://donbidon.rf.gd/" target="_blank">donbidon</a>
 * @license   https://opensource.org/licenses/mit-license.php
 */

declare(strict_types=1);

namespace donbidon\Lib;

use \InvalidArgumentException;
use \RuntimeException;

/**
 * TENgine class unit tests.
 */
class TENgine_Test extends \PHPUnit\Framework\TestCase
{
    /**
     * Contains backup value of error reporting level
     *
     * @var int
     *
     * @see self::setUpBeforeClass()
     * @see self::tearDownAfterClass()
     */
    protected static $errorLevel;

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        static::$errorLevel = error_reporting(E_ALL);
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        error_reporting(static::$errorLevel);

        parent::tearDownAfterClass();
    }

    /**
     * Tests exception when passed wrong locales path.
     *
     * @return void
     *
     * @cover donbidon\Lib\TENgine::__construct()
     */
    public function testInvalidLocalesPath(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            "Invalid locales path \"\""
        );
        new TENgine("", "");
    }

    /**
     * Tests exception when locales folder doesn't contain locale files.
     *
     * @return void
     *
     * @cover donbidon\Lib\TENgine::__construct()
     */
    public function testEmptyLocalesFolder(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            "Empty locales folder \"tests/data/i18n.empty\""
        );
        new TENgine("tests/data/i18n.empty", "");
    }

    /**
     * Tests exception if unsupported locale passed.
     *
     * @return void
     *
     * @cover donbidon\Lib\TENgine::__construct()
     */
    public function testUnsupportedLocale(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            "Unsupported locale \"unknown\" (supported: en, invalid)"
        );
        new TENgine("tests/data/i18n", "", "unknown");
    }

    /**
     * Tests exception if locale file returns invalid data.
     *
     * @return void
     *
     * @cover donbidon\Lib\TENgine::__construct()
     */
    public function testInvalidLocale(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            "Invalid locales file \"tests/data/i18n/invalid.php\""
        );
        new TENgine("tests/data/i18n", "", "invalid");
    }

    /**
     * Tests exception when passed wrong templates path.
     *
     * @return void
     *
     * @cover donbidon\Lib\TENgine::__construct()
     */
    public function testInvalidTemplatesPath(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            "Invalid templates path \"\""
        );
        new TENgine("tests/data/i18n", "");
    }

    /**
     * Tests exception when templates folder doesn't contain output modes folders.
     *
     * @return void
     *
     * @cover donbidon\Lib\TENgine::__construct()
     */
    public function testEmptyTemplatesFolder(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            "Empty templates folder \"tests/data/templates.empty\""
        );
        new TENgine("tests/data/i18n", "tests/data/templates.empty");
    }

    /**
     * Tests exception if unsupported output mode passed.
     *
     * @return void
     *
     * @cover donbidon\Lib\TENgine::__construct()
     */
    public function testUnsupportedMode(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            "Unsupported mode \"unknown\" (supported: cli, www)"
        );
        new TENgine("tests/data/i18n", "tests/data/templates", "", "unknown");
    }

    /**
     * Tests supported locales and output modes.
     *
     * @return void
     *
     * @cover donbidon\Lib\TENgine::__construct()
     * @cover donbidon\Lib\TENgine::getLocales()
     * @cover donbidon\Lib\TENgine::getModes()
     */
    public function testLocalesAndModes(): void
    {
        $ten = new TENgine("tests/data/i18n", "tests/data/templates");

        $expected = ["en", "invalid"];
        self::assertEquals($expected, $ten->getLocales());

        $expected = ["cli", "www"];
        self::assertEquals($expected, $ten->getModes());
    }

    /**
     * Tests exception if invalid string id passed.
     *
     * @return void
     *
     * @cover donbidon\Lib\TENgine::__construct()
     * @cover donbidon\Lib\TENgine::localize()
     * @cover donbidon\Lib\TENgine::l()
     */
    public function testInvalidStringId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            "Unknown string id \"invalid\""
        );
        $ten = new TENgine("tests/data/i18n", "tests/data/templates");
        $ten->l("invalid");
    }

    /**
     * Tests exception if invalid string id passed.
     *
     * @return void
     *
     * @cover donbidon\Lib\TENgine::__construct()
     * @cover donbidon\Lib\TENgine::localize()
     * @cover donbidon\Lib\TENgine::l()
     */
    public function testLocalize(): void
    {
        $ten = new TENgine("tests/data/i18n", "tests/data/templates", "", "cli");
        $expected = "Some error!!!";
        self::assertEquals($expected, $ten->localize("error"));
    }

    /**
     * Tests exception if invalid template passed.
     *
     * @return void
     *
     * @cover donbidon\Lib\TENgine::__construct()
     * @cover donbidon\Lib\TENgine::render()
     * @cover donbidon\Lib\TENgine::r()
     */
    public function testInvalidTemplate(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            "Template \"cli/invalid.php\" not found"
        );
        $ten = new TENgine("tests/data/i18n", "tests/data/templates", "", "cli");
        $ten->r("invalid");
    }

    /**
     * Tests rendering.
     *
     * @return void
     *
     * @cover donbidon\Lib\TENgine::__construct()
     * @cover donbidon\Lib\TENgine::localize()
     * @cover donbidon\Lib\TENgine::l()
     * @cover donbidon\Lib\TENgine::render()
     * @cover donbidon\Lib\TENgine::r()
     */
    public function testRendering(): void
    {
        foreach (["cli", "www"] as $mode) {
            $ten = new TENgine("tests/data/i18n", "tests/data/templates", "", $mode);

            $scope = ["messages" => []];
            foreach (["warning", "error"] as $id) {
                $scope["messages"][] = $ten->r(
                    sprintf("messages/%s", $id),
                    ["message" => $ten->l($id)]
                );
            }
            $scope = ["body" => $ten->r("body", $scope)];

            $actual = $ten->r("page", $scope);
            $expected = file_get_contents(sprintf("tests/data/expected/%s.txt", $mode));
            self::assertEquals($expected, $actual);
        }
    }
}
