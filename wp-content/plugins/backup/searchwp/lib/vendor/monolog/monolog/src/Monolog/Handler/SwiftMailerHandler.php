<?php

declare (strict_types=1);
/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SearchWP\Dependencies\Monolog\Handler;

use SearchWP\Dependencies\Monolog\Logger;
use SearchWP\Dependencies\Monolog\Formatter\FormatterInterface;
use SearchWP\Dependencies\Monolog\Formatter\LineFormatter;
use SearchWP\Dependencies\Swift_Message;
use SearchWP\Dependencies\Swift;
/**
 * SwiftMailerHandler uses Swift_Mailer to send the emails
 *
 * @author Gyula Sallai
 */
class SwiftMailerHandler extends \SearchWP\Dependencies\Monolog\Handler\MailHandler
{
    protected $mailer;
    private $messageTemplate;
    /**
     * @param \Swift_Mailer          $mailer  The mailer to use
     * @param callable|Swift_Message $message An example message for real messages, only the body will be replaced
     * @param string|int             $level   The minimum logging level at which this handler will be triggered
     * @param bool                   $bubble  Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct(\SearchWP\Dependencies\Swift_Mailer $mailer, $message, $level = \SearchWP\Dependencies\Monolog\Logger::ERROR, bool $bubble = \true)
    {
        parent::__construct($level, $bubble);
        $this->mailer = $mailer;
        $this->messageTemplate = $message;
    }
    /**
     * {@inheritdoc}
     */
    protected function send(string $content, array $records) : void
    {
        $this->mailer->send($this->buildMessage($content, $records));
    }
    /**
     * Gets the formatter for the Swift_Message subject.
     *
     * @param string $format The format of the subject
     */
    protected function getSubjectFormatter(string $format) : \SearchWP\Dependencies\Monolog\Formatter\FormatterInterface
    {
        return new \SearchWP\Dependencies\Monolog\Formatter\LineFormatter($format);
    }
    /**
     * Creates instance of Swift_Message to be sent
     *
     * @param  string        $content formatted email body to be sent
     * @param  array         $records Log records that formed the content
     * @return Swift_Message
     */
    protected function buildMessage(string $content, array $records) : \SearchWP\Dependencies\Swift_Message
    {
        $message = null;
        if ($this->messageTemplate instanceof \SearchWP\Dependencies\Swift_Message) {
            $message = clone $this->messageTemplate;
            $message->generateId();
        } elseif (\is_callable($this->messageTemplate)) {
            $message = \call_user_func($this->messageTemplate, $content, $records);
        }
        if (!$message instanceof \SearchWP\Dependencies\Swift_Message) {
            throw new \InvalidArgumentException('Could not resolve message as instance of Swift_Message or a callable returning it');
        }
        if ($records) {
            $subjectFormatter = $this->getSubjectFormatter($message->getSubject());
            $message->setSubject($subjectFormatter->format($this->getHighestRecord($records)));
        }
        $mime = 'text/plain';
        if ($this->isHtmlBody($content)) {
            $mime = 'text/html';
        }
        $message->setBody($content, $mime);
        if (\version_compare(\SearchWP\Dependencies\Swift::VERSION, '6.0.0', '>=')) {
            $message->setDate(new \DateTimeImmutable());
        } else {
            $message->setDate(\time());
        }
        return $message;
    }
}
