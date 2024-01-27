<?php

namespace App\Controller;

use App\Service\Utils\ProblemCarUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class ProblemCarGeneratorController extends AbstractController
{
    #[Route('/problem-cars/generator', name: 'app_problem_car_generator_index', methods: ['GET'])]
    public function index(ProblemCarUtils $problemCarUtils): JsonResponse
    {
        $problemCarUtils->problemCarGenerator();

        $data = [
            'status' => 200,
            'message' => 'Problem generate with success.',
        ];

        return new JsonResponse($data);
    }
}
