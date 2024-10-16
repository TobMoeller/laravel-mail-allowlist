<?php

namespace TobMoeller\LaravelMailAllowlist\Actions\Logs;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\RawMessage;
use TobMoeller\LaravelMailAllowlist\Facades\LaravelMailAllowlist;
use TobMoeller\LaravelMailAllowlist\MailSentMiddleware\SentMessageContext;

class GenerateSentLogMessage implements GenerateSentLogMessageContract
{
    public function generate(SentMessageContext $messageContext): string
    {
        $isEmail = ($message = $messageContext->getMessage()) instanceof Email;
        $logMessage = 'LaravelMailAllowlist.MessageSent:';

        if ($className = $messageContext->getOriginatingClassName()) {
            $logMessage .= PHP_EOL.'ClassName: '.$className;
        }

        if (LaravelMailAllowlist::sentLogMiddleware()) {
            $logMessage .= $this->generateMiddlewareMessage($messageContext);
        }

        if ($isEmail && LaravelMailAllowlist::sentLogHeaders()) {
            $logMessage .= $this->generateHeadersMessage($message);
        }

        if (LaravelMailAllowlist::sentLogMessageData()) {
            $logMessage .= $this->generateMessageDataMessage($messageContext);
        }

        if ($isEmail && LaravelMailAllowlist::sentLogBody()) {
            $logMessage .= $this->generateBodyMessage($message);
        }

        // Handle RawMessage
        if (! $isEmail &&
            LaravelMailAllowlist::sentLogHeaders() &&
            LaravelMailAllowlist::sentLogBody()
        ) {
            $logMessage .= $this->generateRawMessageMessage($message);
        } elseif (! $isEmail &&
            (
                LaravelMailAllowlist::sentLogHeaders() ||
                LaravelMailAllowlist::sentLogBody()
            )
        ) {
            $logMessage .= PHP_EOL.__('RawMessages can only be logged including headers and body');
        }

        return $logMessage;
    }

    protected function generateMiddlewareMessage(SentMessageContext $messageContext): string
    {
        $logMessage = <<<'LOG_MIDDLEWARE'

        ----------
        MIDDLEWARE
        ----------
        LOG_MIDDLEWARE;

        foreach ($messageContext->getLog() as $logEntry) {
            $logMessage .= PHP_EOL.$logEntry;
        }

        return $logMessage;
    }

    protected function generateHeadersMessage(Email $message): string
    {
        return <<<LOG_HEADERS

        ----------
        HEADERS
        ----------
        {$message->getHeaders()->toString()}
        LOG_HEADERS;
    }

    protected function generateMessageDataMessage(SentMessageContext $messageContext): string
    {
        $data = json_encode($messageContext->getMessageData()) ?: '';

        return <<<LOG_MESSAGE_DATA

        ----------
        MESSAGE DATA
        ----------
        {$data}
        LOG_MESSAGE_DATA;
    }

    protected function generateBodyMessage(Email $message): string
    {
        return <<<LOG_BODY

        ----------
        BODY
        ----------
        {$message->getBody()->toString()}
        LOG_BODY;
    }

    protected function generateRawMessageMessage(RawMessage $message): string
    {
        return <<<LOG_RAW_MESSAGE

        ----------
        RAW MESSAGE
        ----------
        {$message->toString()}
        LOG_RAW_MESSAGE;
    }
}
