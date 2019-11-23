<?php

namespace App\Controller;

use App\Entity\Question;
use App\Exception\EntityException;
use App\Service\QuestionService;
use App\Traits\SerializerTrait;
use App\Validator\CreateQuestionRequestValidator;
use App\Validator\ListQuestionsRequestValidator;
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
        $error = (new ListQuestionsRequestValidator())
            ->checkIfRequestIsValid($request);

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
                $questionEntitiesList->getData(),
                'json',
                SerializationContext::create()->setGroups(['public']))
        );
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @throws EntityException
     */
    public function createQuestion(Request $request): JsonResponse
    {
        $error = (new CreateQuestionRequestValidator())
            ->checkIfRequestIsValid($request);

        if ($error !== []) {
            return new JsonResponse($error['message'], $error['status']);
        }

        $requestValues = array_filter(
            array_map(
                static function (string $fieldName) use ($request) {
                    if ($request->get($fieldName) !== null) {
                        return $request->get($fieldName);
                    }
                    return null;
                },
                (new CreateQuestionRequestValidator())->getFieldsList()
            )
        );

        if ($request->get('createdAt') === null) {
            $requestValues = array_merge($requestValues, [new \DateTime()]);
        }

        $this->questionService->insertNewQuestion(
            new Question(
                null,
                array_combine(
                    (new CreateQuestionRequestValidator())->getFieldsList(),
                    $requestValues
                )
            )
        );

        return new JsonResponse(['Question successfully created!', 200]);
    }
}
