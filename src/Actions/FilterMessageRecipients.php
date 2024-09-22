<?php

namespace TobMoeller\LaravelMailAllowlist\Actions;

use Symfony\Component\Mime\Email;
use TobMoeller\LaravelMailAllowlist\RecipientFilter;

class FilterMessageRecipients
{
    public function filter(Email $message): void
    {
        $toFilter = app(RecipientFilter::class)->filter($message->getTo());
        $ccFilter = app(RecipientFilter::class)->filter($message->getCc());
        $bccFilter = app(RecipientFilter::class)->filter($message->getBcc());

        $headers = $message->getHeaders();
        $headers->remove('to');
        $headers->remove('cc');
        $headers->remove('bcc');

        if ($toFilter->hasAllowedRecipients()) {
            $message->addTo(...$toFilter->allowedRecipients);
        }

        if ($ccFilter->hasAllowedRecipients()) {
            $message->addCc(...$ccFilter->allowedRecipients);
        }

        if ($bccFilter->hasAllowedRecipients()) {
            $message->addBcc(...$bccFilter->allowedRecipients);
        }
    }
}
