<?php

namespace TobMoeller\LaravelMailAllowlist;

use Symfony\Component\Mime\Address;
use TobMoeller\LaravelMailAllowlist\Actions\IsAllowedRecipient;

class RecipientFilter
{
    /** @var array<int, Address> */
    public array $deniedRecipients = [];

    /** @var array<int, Address> */
    public array $allowedRecipients = [];

    /**
     * @param  array<int, Address>  $recipients
     */
    public function filter(array $recipients): static
    {
        $IsRecipientAllowed = app(IsAllowedRecipient::class);

        foreach ($recipients as $recipient) {
            if ($IsRecipientAllowed->check($recipient)) {
                $this->allowedRecipients[] = $recipient;
            } else {
                $this->deniedRecipients[] = $recipient;
            }
        }

        return $this;
    }

    public function hasDeniedRecipients(): bool
    {
        return ! empty($this->deniedRecipients);
    }

    public function hasAllowedRecipients(): bool
    {
        return ! empty($this->allowedRecipients);
    }
}
