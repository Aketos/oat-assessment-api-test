<?php

namespace App\Entity;

use App\Entity\Abstraction\StructuredEntity;
use App\Exception\EntityException;
use JMS\Serializer\Annotation as JMS;

class Question extends StructuredEntity
{
    protected const STRUCTURE = [
        self::DATA_FORMAT_CSV => [
            'text',
            'createdAt',
            'choice1',
            'choice2',
            'choice3'
        ],
        self::DATA_FORMAT_JSON => [
            'text',
            'createdAt',
            'choices'
        ]
    ];

    protected const CALLBACKS = [
        'createdAt' => 'createDateTime',
        'choice1' => 'addChoice',
        'choice2' => 'addChoice',
        'choice3' => 'addChoice',
        'choices' => 'generateChoices'
    ];

    /** @var string
     * @JMS\Expose()
     * @JMS\Groups({"public"})
     */
    protected $text;

    /** @var \DateTime
     * @JMS\Expose()
     * @JMS\Groups({"public"})
     * @JMS\SerializedName("createdAt")
     */
    protected $createdAt;

    /** @var Choice[]
     * @JMS\Expose()
     * @JMS\Groups({"public"})
     */
    protected $choices = [];

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     *
     * @return Question
     */
    public function setText(string $text): Question
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return Question
     */
    public function setCreatedAt(\DateTime $createdAt): Question
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Choice[]
     */
    public function getChoices(): array
    {
        return $this->choices;
    }

    /**
     * @param Choice[] $choices
     *
     * @return Question
     */
    public function setChoices(array $choices): Question
    {
        $this->choices = $choices;

        return $this;
    }

    /**
     * @param string $date
     *
     * @return \DateTime
     * @throws EntityException
     */
    protected function createDateTime(string $date): \DateTime
    {
        try {
            $dateTime = new \DateTime($date);
        } catch (\Exception $e) {
            throw new EntityException('Unable to create DateTime from ' . $date . ' date:' . $e->getMessage());
        }

        return $dateTime;
    }

    /**
     * @param string $textChoice
     *
     * @return Question
     */
    protected function addChoice(string $textChoice): Question
    {
        $choices = $this->getChoices();
        $choices[] = new Choice($textChoice);
        $this->setChoices($choices);

        return $this;
    }

    /**
     * @param array $choices
     *
     * @return Question
     */
    protected function generateChoices(array $choices): Question
    {
        foreach ($choices as $choiceArray) {
            $this->addChoice($choiceArray['text']);
        }

        return $this;
    }
}
