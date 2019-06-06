<?php

namespace Primo\Phinx;

use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of LogsOutput
 *
 * @author keith
 */
class LogsOutput implements OutputInterface
{
    protected $logs;
    protected $buffer = '';
    public $tag;

    function __construct($logs)
    {
        $this->logs = $logs;
    }

    function setTag($tag)
    {
        $this->tag = $tag;
    }

    /**
     * {@inheritdoc}
     */
    public function setFormatter(OutputFormatterInterface $formatter)
    {
        // do nothing
    }

    /**
     * {@inheritdoc}
     */
    public function getFormatter()
    {
        // to comply with the interface we must return a OutputFormatterInterface
        return new OutputFormatter();
    }

    /**
     * {@inheritdoc}
     */
    public function setDecorated($decorated)
    {
        // do nothing
    }

    /**
     * {@inheritdoc}
     */
    public function isDecorated()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function setVerbosity($level)
    {
        // do nothing
    }

    /**
     * {@inheritdoc}
     */
    public function getVerbosity()
    { 
        /*
      const VERBOSITY_QUIET = 16;
      const VERBOSITY_NORMAL = 32;
      const VERBOSITY_VERBOSE = 64;
      const VERBOSITY_VERY_VERBOSE = 128;
      const VERBOSITY_DEBUG = 256;

      const OUTPUT_NORMAL = 1;
      const OUTPUT_RAW = 2;
      const OUTPUT_PLAIN = 4;
     */
        return self::VERBOSITY_DEBUG;
    }

    /**
     * {@inheritdoc}
     */
    public function isQuiet()
    {
        return self::VERBOSITY_QUIET === $this->getVerbosity();
    }

    /**
     * {@inheritdoc}
     */
    public function isVerbose()
    {
        return self::VERBOSITY_VERBOSE <=  $this->getVerbosity();
    }

    /**
     * {@inheritdoc}
     */
    public function isVeryVerbose()
    {
        return self::VERBOSITY_VERY_VERBOSE <=  $this->getVerbosity();
    }

    /**
     * {@inheritdoc}
     */
    public function isDebug()
    {
        return self::VERBOSITY_DEBUG <=  $this->getVerbosity();
    }

    /**
     * {@inheritdoc}
     */
    public function writeln($msg, $options = self::OUTPUT_NORMAL)
    {
        if (isset($this->logs)) {
            $line = "{$this->buffer}{$msg}";
            $this->logs->logThis($line, $this->tag);
            $this->buffer = '';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function write($messages, $newline = false, $options = self::OUTPUT_NORMAL)
    {
        if (isset($this->logs)) {
            if ($newline) return $this->writeln($messages, $options);
            else $this->buffer = "{$this->buffer}{$messages}";
        }
    }
}
