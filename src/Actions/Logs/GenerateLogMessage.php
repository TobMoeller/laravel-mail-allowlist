<?php

namespace TobMoeller\LaravelMailAllowlist\Actions\Logs;

use TobMoeller\LaravelMailAllowlist\Facades\LaravelMailAllowlist;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\MessageContext;

class GenerateLogMessage implements GenerateLogMessageContract
{
    public function generate(MessageContext $messageContext): string
    {
        $logMessage = 'LaravelMailAllowlist.MessageSending:';

        if ($className = $messageContext->getOriginatingClassName()) {
            $logMessage .= PHP_EOL.'ClassName: '.$className;
        }

        if (! $messageContext->shouldSendMessage()) {
            $logMessage .= PHP_EOL.'Message was canceled by Middleware!';
        }

        if (LaravelMailAllowlist::logMiddleware()) {
            $logMessage .= $this->generateMiddlewareMessage($messageContext);
        }

        if (LaravelMailAllowlist::logHeaders()) {
            $logMessage .= $this->generateHeadersMessage($messageContext);
        }

        if (LaravelMailAllowlist::logMessageData()) {
            $logMessage .= $this->generateMessageDataMessage($messageContext);
        }

        if (LaravelMailAllowlist::logBody()) {
            $logMessage .= $this->generateBodyMessage($messageContext);
        }

        return $logMessage;
    }

    protected function generateMiddlewareMessage(MessageContext $messageContext): string
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

    protected function generateHeadersMessage(MessageContext $messageContext): string
    {
        return <<<LOG_HEADERS

        ----------
        HEADERS
        ----------
        {$messageContext->getMessage()->getHeaders()->toString()}
        LOG_HEADERS;
    }

    protected function generateMessageDataMessage(MessageContext $messageContext): string
    {
        $data = json_encode($messageContext->getMessageData()) ?: '';

        return <<<LOG_MESSAGE_DATA

        ----------
        MESSAGE DATA
        ----------
        {$data}
        LOG_MESSAGE_DATA;
    }

    protected function generateBodyMessage(MessageContext $messageContext): string
    {
        return <<<LOG_BODY

        ----------
        BODY
        ----------
        {$messageContext->getMessage()->getBody()->toString()}
        LOG_BODY;
    }
}
