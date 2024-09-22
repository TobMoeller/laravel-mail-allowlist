<?php

namespace TobMoeller\LaravelMailAllowlist\Listeners;

use Illuminate\Mail\Events\MessageSending;
use TobMoeller\LaravelMailAllowlist\RecipientFilter;

class FilterMailRecipientsListener
{
    public function handle(MessageSending $messageSendingEvent): bool
    {
        $message = $messageSendingEvent->message;

        $toFilter = app(RecipientFilter::class)->filter($message->getTo());
        $ccFilter = app(RecipientFilter::class)->filter($message->getCc());
        $bccFilter = app(RecipientFilter::class)->filter($message->getBcc());

        $headers = $message->getHeaders();
        $headers->remove('to');
        $headers->remove('cc');
        $headers->remove('bcc');

        if (! empty($toFilter->allowedRecipients)) {
            $message->addTo(...$toFilter->allowedRecipients);
        }

        if (! empty($ccFilter->allowedRecipients)) {
            $message->addCc(...$ccFilter->allowedRecipients);
        }

        if (! empty($bccFilter->allowedRecipients)) {
            $message->addBcc(...$bccFilter->allowedRecipients);
        }

        if (empty($message->getTo())) {
            return false;
        }

        return true;
    }
}
