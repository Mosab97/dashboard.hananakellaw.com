<?php

namespace App\Exceptions;

use Exception;

class CustomBusinessException extends Exception
{
    private array $data;

    /**
     * Create a new custom business exception instance.
     *
     * @param  array  $data  Additional data to be included in the response
     */
    public function __construct(
        string $message = '',
        int $code = 422,
        array $data = [],
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->data = $data;
    }

    /**
     * Get the additional data associated with the exception.
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Get the exception as an array for API response.
     */
    public function toArray(): array
    {
        return [
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
            'data' => $this->getData(),
        ];
    }

    /**
     * Alias for getData() for compatibility
     */
    public function getContext(): array
    {
        return $this->getData();
    }

    public static function schoolSubscriptionExpired($member)
    {
        return new self(
            message: api('Your subscription has expired. Please renew your subscription to continue using the service.'),
            code: 403,
            data: [
                'is_subscribed' => false,
                'is_verified' => $member->is_verified,
                'is_active' => $member->active,
            ]
        );
    }
}
