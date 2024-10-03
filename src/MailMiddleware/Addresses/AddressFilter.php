<?php

namespace TobMoeller\LaravelMailAllowlist\MailMiddleware\Addresses;

use Closure;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Symfony\Component\Mime\Address;
use TobMoeller\LaravelMailAllowlist\Actions\Addresses\CheckAddressContract;
use TobMoeller\LaravelMailAllowlist\Enums\Header;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\MailMiddlewareContract;
use TobMoeller\LaravelMailAllowlist\MailMiddleware\MessageContext;

class AddressFilter implements MailMiddlewareContract
{
    public function __construct(
        public Header $header,
        public CheckAddressContract $addressChecker,
    ) {
        throw_unless(
            $header->isAddressHeader() || $header->isAddressListHeader(),
            InvalidArgumentException::class,
            'Must be an address header'
        );
    }

    public function handle(MessageContext $messageContext, Closure $next): mixed
    {
        /** @var array<int, Address> */
        $allowedRecipients = [];
        /** @var array<int, Address> */
        $deniedRecipients = [];

        $headers = $messageContext->getMessage()->getHeaders();
        $recipients = $headers->getHeaderBody($this->header->value);

        // Remove old header entirely
        $headers->remove($this->header->value);

        foreach (Arr::wrap($recipients) as $recipient) {
            if ($this->addressChecker->check($recipient)) {
                $allowedRecipients[] = $recipient;
            } else {
                $deniedRecipients[] = $recipient;
            }
        }

        if (! empty($allowedRecipients)) {
            // Recreate the header with allowed recipients
            if ($this->header->isAddressHeader()) {
                $headers->addMailboxHeader($this->header->value, $allowedRecipients[0]);
            } else {
                $headers->addMailboxListHeader($this->header->value, $allowedRecipients);
            }
        }

        $log = static::class;
        if (! empty($allowedRecipients)) {
            $log .= PHP_EOL.'Allowed Recipients: '.$this->emailList($allowedRecipients);
        }
        if (! empty($deniedRecipients)) {
            $log .= PHP_EOL.'Denied Recipients: '.$this->emailList($deniedRecipients);
        }
        $messageContext->addLog($log);

        return $next($messageContext);
    }

    /**
     * @param  array<int, Address>  $recipients
     */
    protected function emailList(array $recipients): string
    {
        $recipients = Arr::map($recipients, fn (Address $recipient) => $recipient->getAddress());

        return Arr::join($recipients, ';');
    }
}
