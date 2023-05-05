<?php
/**
 *
 */
namespace FishPig\WordPress\Model;

use Monolog\Level;
use Safe\DateTimeImmutable;

class Logger extends \Monolog\Logger
{
    /**
     * Extended to add in calling object data to context array
     * @param int|Level $level
     * @param string $message
     * @param array $context
     * @param DateTimeImmutable|\Monolog\DateTimeImmutable|null $datetime
     * @return bool
     */
    public function addRecord($level, $message, array $context = array(), DateTimeImmutable|\Monolog\DateTimeImmutable $datetime = null): bool
    {
        if ($backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)) {
            $context['backtrace'] = array_pop($backtrace);
        }

        parent::addRecord($level, $message, $context);
        return true ;
    }
}
