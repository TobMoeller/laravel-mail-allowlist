<?php

namespace TobMoeller\LaravelMailAllowlist\Traits\MailMiddleware;

trait HandlesMessageData
{
    /** @var array<string, mixed> */
    protected array $messageData = [];

    /**
     * @return array<string, mixed>
     */
    public function getMessageData(): array
    {
        return $this->messageData;
    }

    /**
     * Returns the originating class name (notification or mailable).
     * This might not be set despite originating from a notification or mailable.
     */
    public function getOriginatingClassName(): ?string
    {
        $className = $this->messageData['__laravel_notification'] ??
            $this->messageData['__laravel_mailable'] ??
            null;

        return is_string($className) ? $className : null;
    }
}
