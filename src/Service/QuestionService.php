<?php

namespace App\Service;

use App\Entity\Choice;
use App\Entity\Question;
use App\Entity\QuestionList;
use App\Exception\EntityException;
use App\Interfaces\ProviderInterface;
use Stichoza\GoogleTranslate\GoogleTranslate;

class QuestionService
{
    /** @var ProviderInterface */
    protected $provider;

    /** @var GoogleTranslate */
    protected $translator;

    public function __construct(ProviderInterface $provider, GoogleTranslate $translator)
    {
        $this->provider   = $provider;
        $this->translator = $translator;
    }

    /**
     * @param string $lang
     *
     * @return QuestionList
     */
    public function listQuestionsInLanguage(string $lang): QuestionList
    {
        $this->translator->setTarget($lang);

        return (new QuestionList)->setData(
            array_map(
                function (Question $question) {
                    return $this->translateQuestion($question);
                },
                $this->provider->findAll(Question::class)
            )
        );
    }

    /**
     * @param Question $question
     *
     * @return bool
     */
    public function insertNewQuestion(Question $question): bool
    {
        $questionList = (new QuestionList())
            ->setData($this->provider->findAll(Question::class));

        $questionList->addData($question);
        $this->provider->insertAll(Question::class, $questionList->getData());

        return true;
    }

    /**
     * @param Question $question
     * @param string   $lang
     *
     * @return Question
     * @throws EntityException
     * @throws \ErrorException
     */
    public function translateQuestion(Question $question, string $lang = null): Question
    {
        if ($lang !== null) {
            $this->translator->setTarget($lang);
        }

        return (new Question())
            ->setText($this->translator->translate($question->getText()))
            ->setChoices(
                array_map(
                    function (Choice $choice) {
                        return new Choice($this->translator->translate($choice->getText()));
                    },
                    $question->getChoices()
                )
            );
    }
}
