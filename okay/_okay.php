<?php

/*
  # OKAY -  Keeping It Simple Specifications for PHP!

  Totally the simplest BDD/TDD framework,... in the world!

  Design based on the original SUnit by Kent Beck

  A result of another Cunningham-Beck innovation:
  http://wiki.c2.com/?DoTheSimplestThingThatCouldPossiblyWork

  ## Documentation:

  1. Adding `_ok.php` turns a directory of `*.inc` scripts/directories into a spec/test suite.
  edit it manually in order to directly require the `_okay.php` file.

  2. _okay.php is both a command line, and a web test runner (wip)

  3. BDD style "english" output.
  ```
  EXPECT("it to be good");
  ```
  4. Uses PHP built in `assert`
  ```
  assert( $it == "good" , "'$it' wasn't good" );
  ```

  5. Use throughout your codebase, deployment optional

  Great for adding specs/tests to a file-based "legacy" PHP system.
  (adjust your deployment to ignore/delete `_*` files, and it's gone.)

  6. Zero dependencies

  Does not need a functioning composer/autoload. Will not clutter your lean cool code.
  Will not frighten your package users by loading lots of stuff, just for testing/require-dev.

  7. Excellent basis for "Platform Tests" and White Screen of Death debugging

  Platform-tests should run to verify that the deployment platform, and php itself is
  is configured and working as expected.

  When faced with the PHP - W.S.O.D. and no clues, a platform test/spec suite can check for common
  misconfiguration scenarios, can tell you what is working.

  #### TODO HTML Web Runner - not ready
 */

namespace {
    $OKAY_VERSION = '0.9.5';
    // Take your pick
    ini_set('log_errors', 1);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    assert_options(ASSERT_WARNING, 0);
    ini_set('assert.exception', 1);

    error_reporting(E_ALL);

    // if (extension_loaded('xdebug')) xdebug_disable(); // orange not to your taste
    // define our own magic constants to point to the project and site roots.
    if (!defined('__PROJECT__')) define("__PROJECT__", dirname(dirname(__DIR__)));
    if (!defined('__SITE__')) define("__SITE__", __PROJECT__ . "/public");

    /* Secure for a specific IP address/range configured in Apache <site>.conf
     *  and signified via the environment variable
     * You may have to adapt this for your security environment.
     */

    if (ok\isCLI()) { // cli runner
        if (!defined('BR')) define('BR', PHP_EOL);
        if (!defined('OKAY_OUTPUT')) define('OKAY_OUTPUT', 'text/plain');
    } else { // web runner
        if (strpos($_SERVER['DEV_ALLOWED'], ".kisting.") == false) {
            echo "Testing not authorised";
            exit;
        }

        // respond in plaintext for now.

        if (!defined('OKAY_OUTPUT')) define('OKAY_OUTPUT', 'text/plain');
        if (!defined('BR')) define('BR', PHP_EOL);
        // if (!defined('BR')) define('BR', '<BR>');

        header("Content-Type: " . OKAY_OUTPUT);
        ini_set('html_errors', 0);

        if (isset($_GET['ok'])) $OKAY_SUITE = __PROJECT__ . '/' . $_GET['ok'];
    }

    function ok($format)
    { // php<5.6
        $args = func_get_args(); // php<5.6
        $args[0] = ok\okay()->indent() . "     " . $format . BR;
        call_user_func_array('ok\printf', $args);
    }
}

namespace ok {

    // ok\DEBUG() && ok\printf("Debug only Output".BR);
    function DEBUG()
    {
        return in_array('-D', $_SERVER['argv']) || filter_input(INPUT_GET, 'debug', FILTER_VALIDATE_BOOLEAN);
    }

    function include_if_present($file)
    {
        if (file_exists($file)) {
            include($file);
            return true;
        }
        return false;
    }

    // useful for wiping out file fixtures in a directory
    function delete_all_matching($in, $match = '*')
    {
        assert($in !== '');
        assert($in !== '/');
        array_map('unlink', glob("{$in}/{$match}"));
    }

    // useful for repopulating file fixtures into a directory
    function copy_all($from, $to, $match = '*')
    {
        is_dir($to) ?: mkdir($to); // ensure existence
        foreach (glob("{$from}/{$match}") as $path) {
            copy($path, $to . '/' . basename($path));
        }
    }

    // A templating framework in a single function!
    function lookup_and_include($name, $dir, $includes = '_includes')
    {
        //**/ echo("lookup_and_include($name, $dir, $includes = '_includes')\n");
        $target = "{$dir}/{$includes}/{$name}.inc";
        if (file_exists($target)) {
            return include $target;
        } else {
            if ($dir != __DIR__ && $dir != "/" && !empty($dir)) {
                return lookup_and_include($name, dirname($dir), $includes);
            }
        }
        return false;
    }

    function okay($runner = null)
    {
        static $okay;
        if (null !== $runner) $okay = $runner;
        return $okay;
    }

// vocabulary

    function _()
    {
        $msg = implode(' ', func_get_args());
        printf(okay()->indent() . "      {$msg}" . BR);
        return okay();
    }

    function __()
    {
        $msg = implode(' ', func_get_args());
        printf(okay()->indent() . "%2d) " . $msg . BR, ++okay()->count_expectations);
        return okay();
    }

    function given($path)
    {
        $given = substr($path, strlen(__OKAY__));
        $given = preg_replace(array('|/\d+\.|', '|\.inc|', '|\.php|', '|/|', '|/_|',), array(' ', '', ' ', ' '), $given);
        printf(BR . "<div class='test'><em>%sGiven{$given}</em><br><div class = 'output'>" . BR, okay()->indent());
    }

    // $okay = ok\expect("expectation...")
    function EXPECT($message)
    {
        return __("Expect", $message);
    }

    function Should($message)
    {
        return _("should", $message);
    }
    /*
     * If code under test may have an endless loop, this utility comes in handy
     * ok\die_after(5);
     */

    function die_after($over = 99)
    {//calls
        static $the_edge = 0;
        if ($over < $the_edge++) die;
    }

    function isCLI()
    {
        return (php_sapi_name() == 'cli' || (defined('OKAY_OUTPUT') && OKAY_OUTPUT == 'text/plain'));
    }

    // function printf($format, ...$args) { // php>=5.6
    // \printf(isCLI() ? strip_tags($format) : $format, ... $args); }

    function printf($format)
    { // php<5.6
        $args = func_get_args();
        $args[0] = isCLI() ? strip_tags($format) : $format;
        call_user_func_array('\printf', $args);
    }

    function asserts($on)
    {

        if (version_compare(PHP_VERSION, '5.4.0') < 0) {
            assert_options(ASSERT_WARNING, $on);
        } else {
            if ($on) {
                ini_set('assert.exception', 0);
                assert_options(ASSERT_CALLBACK, array(okay(), 'on_assertion_failure'));
            } else {
                ini_set('assert.exception', 1);
                assert_options(ASSERT_CALLBACK, "");
            }
        }
    }

    class Okay
    {
        public $dir;
        public $count_files;
        public $count_expectations;
        public $count_failed_assertions = 0;
        public $previous_error_handler;
        public $previous_exception_handler;
        public $indent = 0;

        static function initializeRequested()
        {
            return in_array('-I', $_SERVER['argv']) || (isset($_GET['INIT']));
        }

        function indent($n = 0)
        {
            static $i;
            return str_repeat(' ', ($i = $i + $n));
        }

        function perform($dir, $method)
        {
            $file = $dir . "/{$method}";
            include_if_present($file) && DEBUG() && printf("<div class='{$method}'>performed: $file</div>" . BR);
        }

        function test($path)
        {
            $this->assertion_fail_count = 0;

            $result = null; // if error occurred

            given($path);

            $start = microtime(true);

            $result = $this->protect(array($this, "performTest"), $path);

            if ($this->count_failed_assertions > 0 && $result == true) {
                $result = false;
            }

            $ms = 1000 * (microtime(true) - $start);

            return $ms;
        }

        function performTest($path)
        {
            global $okaying;

            $this->count_files++;

            //$this->indent(+2);
            $okaying = true;
            $result = include($path);
            $okaying = false;
            //$this->indent(-2);

            return $result;
        }

        // function protect($callable, ...$args) { // php>=5.6
        function protect($callable)
        { // php<5.6
            if (version_compare(PHP_VERSION, '5.4.0') < 0) {
                $this->previous_error_handler = set_error_handler(array($this, "error_handler"), E_WARNING);
            } else assert_options(ASSERT_WARNING, 0);

            // $this->previous_exception_handler = set_exception_handler(array($this, "exception_handler"));
            asserts(true);

            // $result = $callable(...$args); // php>=5.6
            $result = call_user_func_array($callable, array_slice(func_get_args(), 1)); // php<5.6

            asserts(false);
            // restore_exception_handler();
            restore_error_handler();

            return $result;
        }

        function run($dir)
        {
            okay($this);
            if (static::initializeRequested()) $this->perform($dir, '_initialize.php');

            printf("<div class = 'suite'>");

            $this->perform($dir, '_ok.php');
            foreach (glob("$dir/{*.inc,*/_ok.php}", GLOB_BRACE) as $path) {
                $this->perform($dir, '_setup.php');

                if (substr($path, -3) == 'php') $this->run(dirname($path));
                else if (is_dir($path)) {
                    //$this->indent(+2);
                    $this->run($path);
                    //$this->indent(-2);
                } else $this->test($path);

                $this->perform($dir, '_teardown.php');
            }

            $this->perform($dir, '_ok_teardown.php');

            printf("</div>");

            return $this;
        }

        //// We catch warnings - compatible with php5.3+
        function error_handler($level, $msg, $file, $line)
        {
            if ($level == 2 && (substr($msg, 0, 8) == 'assert()')) { // Handling Warning - php<5.4
                if (version_compare(PHP_VERSION, '5.4.0') < 0) {
                    ++$this->count_failed_assertions;
                    $msg = substr($msg, 10, strlen($msg) - 10);
                    printf("<em style = 'assertion-failed'>%s   FAILED(%s):</em> %s" . BR, $this->indent(), $line, $msg);
                }
            }
            if ($this->previous_error_handler == null) return null;
            $handler = $this->previous_error_handler;
            return $handler($level, $msg, $file, $line);
        }

        function exception_handler($ex)
        {

            if ($this->previous_exception_handler == null) {
                print_r($ex->getTraceAsString());
                return null;
            }
            return $this->previous_exception_handler($ex);
        }

        function on_assertion_failure($file, $line, $code, $msg)
        {
            if (version_compare(PHP_VERSION, '5.4.0') >= 0) { // Handling Callback php>=5.4
                ++$this->count_failed_assertions;
                printf("<em style = 'assertion-failed'>%2d} %sFAILED(%s):</em> %s" . BR, $this->count_expectations, $this->indent(), $line, $msg);
            }
        }
    }

    if (!isset($OKAY_SUITE)) $OKAY_SUITE = __DIR__;
    if (!defined('__OKAY__')) define('__OKAY__', __FILE__);
 
     
    $title = "OKAY($OKAY_VERSION):" . $OKAY_SUITE;

    if (isCLI()) printf("$title" . BR);
    else \ok\lookup_and_include('header_okay', $OKAY_SUITE);

    $okay = new Okay();
    $okay->run($OKAY_SUITE);

    $count_files = $okay->count_files;
    $count_expectations = $okay->count_expectations;
    $count_failed_assertions = $okay->count_failed_assertions;

    $failedMsg = ($count_failed_assertions > 0) ? "failed {$count_failed_assertions} assertions" : "OK";
    if (isCLI())
            printf("Ran %d files (%d expectations) %s" . BR, $count_files, $count_expectations, $failedMsg);
    else \ok\lookup_and_include('footer_okay', $OKAY_SUITE);
}    
