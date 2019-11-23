<?php

namespace App\Controller;

use App\Service\QuestionService;
use App\Traits\SerializerTrait;
use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class QuestionsController extends AbstractController
{
    use SerializerTrait;

    /** @var QuestionService */
    protected $questionService;

    public function __construct(QuestionService $questionService)
    {
        $this->questionService = $questionService;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function listQuestions(Request $request): JsonResponse
    {
        $error = $this->checkIfErrorInRequest($request);

        if ($error !== []) {
            return new JsonResponse($error['message'], $error['status']);
        }

        try {
            $questionEntitiesList = $this->questionService->listQuestionsInLanguage($request->get('lang'));
        } catch (\Exception $e) {
            if ($this->getParameter('kernel.environment') !== 'prod') {
                throw $e;
            }

            return new JsonResponse('A technical error occurred', 500);
        }

        return new JsonResponse(
            $this->getSerializer()->serialize(
                $questionEntitiesList,
                'json',
                SerializationContext::create()->setGroups(['public']))
        );
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    protected function checkIfErrorInRequest(Request $request): array
    {
        if ($request->get('lang') === null) {
            return [
                'status' => 400,
                'message' => 'Parameter lang is missing from request'
            ];
        }

        if (!preg_match('/^([a-z]{2})(-[A-Z]{2})?$/', $request->get('lang'))) {
            return [
                'status' => 400,
                'message' => 'Incorrect lang parameter value'
            ];
        }

        return [];
    }
}