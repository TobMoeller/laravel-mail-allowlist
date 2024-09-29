<?php

namespace TobMoeller\LaravelMailAllowlist\Enums;

/**
 * Corresponds to Symfony\Component\Mime\Header\Headers::HEADER_CLASS_MAP
 */
enum Header: string
{
    case DATE = 'date';
    case FROM = 'from';
    case SENDER = 'sender';
    case REPLY_TO = 'reply-to';
    case TO = 'to';
    case CC = 'cc';
    case BCC = 'bcc';
    case MESSAGE_ID = 'message-id';
    case IN_REPLY_TO = 'in-reply-to';
    case REFERENCES = 'references';
    case SUBJECT = 'subject';

    /**
     * @return array<int, self>
     */
    public static function addressHeaders(): array
    {
        return [
            self::SENDER,
        ];
    }

    public function isAddressHeader(): bool
    {
        return in_array($this, self::addressHeaders());
    }

    /**
     * @return array<int, self>
     */
    public static function addressListHeaders(): array
    {
        return [
            self::FROM,
            self::REPLY_TO,
            self::TO,
            self::CC,
            self::BCC,
        ];
    }

    public function isAddressListHeader(): bool
    {
        return in_array($this, self::addressListHeaders());
    }
}
