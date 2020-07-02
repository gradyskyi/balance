<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\BalanceException;
use App\Exception\DuplicateTransactionException;
use Symfony\Component\HttpFoundation\Response;

class XmlResponse extends Response
{
    private const STATUS_OK = 'OK';
    private const STATUS_ERROR = 'ERROR';
    private const ERROR_MESSAGE = 'internal server error';

    private string $status = self::STATUS_OK;

    /**
     * @var string
     */
    private $message;

    /**
     * @var \Throwable
     */
    private $exception;

    public function __construct($content = '', int $status = 200, array $headers = [])
    {
        parent::__construct($content, $status, array_merge($headers, [
            'Content-Type' => 'text/xml',
        ]));
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function setException(\Throwable $exception): self
    {
        $this->exception = $exception;

        return $this;
    }

    public function build(): self
    {
        if ($this->exception instanceof \Throwable && !($this->exception instanceof DuplicateTransactionException)) {
            $this->status = self::STATUS_ERROR;
            $this->setStatusCode(self::HTTP_INTERNAL_SERVER_ERROR);
            if ($this->exception instanceof BalanceException) {
                $this->message = $this->exception->getMessage();
            } else {
                $this->message = self::ERROR_MESSAGE;
            }
        }

        $messageBlock = $this->message ? 'msg="' . $this->message . '"' : '';

        return $this->setContent('<?xml version="1.0"?><result status="' . $this->status . '" '
        . $messageBlock . '></result>');
    }
}
