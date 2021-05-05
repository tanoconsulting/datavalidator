<?php

declare(strict_types=1);

namespace TanoConsulting\DataValidatorBundle\Logger;

use DateTime;
use DateTimeInterface;
use function get_class;
use function gettype;
use function is_object;
use function is_scalar;
use function method_exists;
use Psr\Log\AbstractLogger;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use function sprintf;
use function strpos;
use function strtr;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * PSR-3 compliant console logger.
 *
 * Copy of Doctrine\Migrations\Tools\Console\ConsoleLogger, modified to our taste
 */
final class ConsoleLogger extends AbstractLogger
{
    public const INFO = 'info';
    public const ERROR = 'error';
    public const COMMENT = 'comment';

    protected $errors = 0;
    protected $warnings = 0;

    /** @var OutputInterface */
    private $output;

    /** @var LoggerInterface */
    private $fileLogger;

    /** @var array<string, int> */
    private $verbosityLevelMap = [
        LogLevel::EMERGENCY => OutputInterface::VERBOSITY_NORMAL,
        LogLevel::ALERT => OutputInterface::VERBOSITY_NORMAL,
        LogLevel::CRITICAL => OutputInterface::VERBOSITY_NORMAL,
        LogLevel::ERROR => OutputInterface::VERBOSITY_NORMAL,
        LogLevel::WARNING => OutputInterface::VERBOSITY_NORMAL,
        LogLevel::NOTICE => OutputInterface::VERBOSITY_VERBOSE,
        LogLevel::INFO => OutputInterface::VERBOSITY_VERY_VERBOSE,
        LogLevel::DEBUG => OutputInterface::VERBOSITY_DEBUG,
    ];
    // For further coloring options, see: https://symfony.com/doc/5.2/console/coloring.html
    /** @var array<string, string> */
    private $formatLevelMap = [
        LogLevel::EMERGENCY => self::ERROR,
        LogLevel::ALERT => self::ERROR,
        LogLevel::CRITICAL => self::ERROR,
        LogLevel::ERROR => self::ERROR,
        LogLevel::WARNING => self::COMMENT,
        LogLevel::NOTICE => self::INFO,
        LogLevel::INFO => self::INFO,
        LogLevel::DEBUG => self::INFO,
    ];

    /**
     * @param array<string, int>    $verbosityLevelMap
     * @param array<string, string> $formatLevelMap
     */
    public function __construct(OutputInterface $output, LoggerInterface $fileLogger = null, array $verbosityLevelMap = [],
        array $formatLevelMap = [])
    {
        $this->output = $output;
        $this->fileLogger = $fileLogger;
        $this->verbosityLevelMap = $verbosityLevelMap + $this->verbosityLevelMap;
        $this->formatLevelMap = $formatLevelMap + $this->formatLevelMap;
    }

    /**
     * {@inheritdoc}
     */
    public function log($level, $message, array $context = []): void
    {
        if (!isset($this->verbosityLevelMap[$level])) {
            throw new InvalidArgumentException(sprintf('The log level "%s" does not exist.', $level));
        }

        if (in_array($level, [LogLevel::EMERGENCY, LogLevel::ALERT, LogLevel::CRITICAL, LogLevel::ERROR])) {
            ++$this->errors;
        } elseif ($level === LogLevel::WARNING) {
            ++$this->warnings;
        }

        if ($this->fileLogger) {
            $this->fileLogger->log($level, $message, $context);
        }

        $output = $this->output;

        // Write to the error output if necessary and available
        if ($this->formatLevelMap[$level] === self::ERROR) {
            if ($this->output instanceof ConsoleOutputInterface) {
                $output = $output->getErrorOutput();
            }
        }

        // the if condition check isn't necessary -- it's the same one that $output will do internally anyway.
        // We only do it for efficiency here as the message formatting is relatively expensive.
        if ($output->getVerbosity() < $this->verbosityLevelMap[$level]) {
            return;
        }

        $output->writeln(sprintf('<%1$s>%2$s</%1$s>', $this->formatLevelMap[$level], $this->interpolate($message, $context)));
    }

    /**
     * Interpolates context values into the message placeholders.
     *
     * @param mixed[] $context
     */
    private function interpolate(string $message, array $context): string
    {
        if (strpos($message, '{') === false) {
            return $message;
        }

        $replacements = [];
        foreach ($context as $key => $val) {
            if ($val === null || is_scalar($val) || (is_object($val) && method_exists($val, '__toString'))) {
                $replacements["{{$key}}"] = $val;
            } elseif ($val instanceof DateTimeInterface) {
                $replacements["{{$key}}"] = $val->format(DateTime::RFC3339);
            } elseif (is_object($val)) {
                $replacements["{{$key}}"] = '[object ' . get_class($val) . ']';
            } else {
                $replacements["{{$key}}"] = '[' . gettype($val) . ']';
            }

            if (!isset($replacements["{{$key}}"])) {
                continue;
            }

            $replacements["{{$key}}"] = '<comment>' . $replacements["{{$key}}"] . '</comment>';
        }

        return strtr($message, $replacements);
    }

    public function resetErrorsCount()
    {
        $this->warnings = 0;
        $this->errors = 0;
    }

    /**
     * @return int[]
     */
    public function getErrorsCount()
    {
        return [
            'warnings' => $this->warnings,
            'errors' => $this->errors,
        ];
    }

    public function getVerbosity()
    {
        return $this->output->getVerbosity();
    }
}
