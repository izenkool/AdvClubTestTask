<?php

namespace App\Controller;

use App\Request\TravelCostDTO;
use App\Service\Discount\KidDiscountService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route('/api/v1/travel')]
class TravelController
{
    public function __construct(
        private LoggerInterface $logger
    ) {
    }

    #[Route('/cost', name: 'app_travel_cost', methods: ['GET'])]
    public function cost(
        #[MapQueryString] TravelCostDTO $travelCostDTO,
        KidDiscountService $kidDiscountService
    ): JsonResponse {
        try {
            $birthDate = new \DateTimeImmutable($travelCostDTO->getBirthDate());
            $cost = $travelCostDTO->getCost() - $kidDiscountService->getDiscount($travelCostDTO->getCost(), $birthDate);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());

            return new JsonResponse(["error" => $exception->getMessage()]);
        }

        return new JsonResponse(["cost" => $cost]);
    }
}