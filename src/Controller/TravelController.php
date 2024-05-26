<?php

namespace App\Controller;

use App\Request\TravelCostDTO;
use App\Service\Discount\EarlyBookingDiscountService;
use App\Service\Discount\KidDiscountService;
use DateTimeImmutable;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[AsController]
#[Route('/api/v1/travel')]
class TravelController
{
    public function __construct(
        private LoggerInterface $logger
    ) {
    }

    #[Route('/cost', name: 'app_travel_cost', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns cost with discounts',
        content: new OA\JsonContent(
            properties: [new OA\Property(property: 'cost', type: 'int')],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 422,
        description: 'Incorrect request',
        content: new OA\JsonContent(
            properties: [new OA\Property(property: 'error', type: 'string')],
            type: 'object'
        )
    )]
    public function cost(
        #[MapQueryString] TravelCostDTO $travelCostDTO,
        KidDiscountService $kidDiscountService,
        EarlyBookingDiscountService $earlyBookingDiscountService,
    ): JsonResponse {
        try {
            $birthDate = new DateTimeImmutable($travelCostDTO->getBirthDate());
            $cost = $travelCostDTO->getCost() - $kidDiscountService->getDiscount($travelCostDTO->getCost(), $birthDate);

            $startDate = $travelCostDTO->getStartDate() ? new DateTimeImmutable($travelCostDTO->getStartDate()) : null;
            $payDate = $travelCostDTO->getPayDate() ? new DateTimeImmutable($travelCostDTO->getPayDate()) : null;
            $cost = $cost - $earlyBookingDiscountService->getDiscount($cost, $startDate, $payDate);
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());

            return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return new JsonResponse(['cost' => $cost]);
    }
}
