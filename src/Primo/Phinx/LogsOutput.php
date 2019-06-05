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
        return self::VERBOSITY_QUIET;
    }

    /**
     * {@inheritdoc}
     */
    public function isQuiet()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isVerbose()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isVeryVerbose()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isDebug()
    {
        return false;
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
