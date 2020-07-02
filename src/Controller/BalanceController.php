<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\BalanceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BalanceController extends AbstractController
{
    private XmlResponse $response;

    private BalanceService $service;

    private const SERVICE_DEBIT_METHOD_TAG = 'debit';
    private const SERVICE_CREDIT_METHOD_TAG = 'credit';

    public function __construct(XmlResponse $response, BalanceService $service) {
        $this->response = $response;
        $this->service = $service;
    }

    /**
     * @Route("/balance", name="balance")
     */
    public function balance(Request $request): Response
    {
        try {
            $xmlRequest = new \SimpleXMLElement($request->getContent());
            $attributes = current((array)$xmlRequest->attributes());

            if (self::SERVICE_DEBIT_METHOD_TAG === $xmlRequest->getName()) {
                $this->service->debit($attributes);
            } else if (self::SERVICE_CREDIT_METHOD_TAG === $xmlRequest->getName()) {
                $this->service->credit($attributes);
            } else {
                throw new \Exception();
            }
        } catch (\Exception $exception) {
            $this->response->setException($exception);
        }

        return $this->response->build();
    }
}