<?php

namespace App\Entity;

use App\Entity\Abstraction\StructuredEntity;
use App\Exception\EntityException;
use App\Interfaces\EntityToArrayInterface;
use JMS\Serializer\Annotation as JMS;

class Question extends StructuredEntity implements EntityToArrayInterface
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

    public const HEADER = [
        self::DATA_FORMAT_CSV => ['Question text', 'Created At', 'Choice 1', 'Choice 2', 'Choice 3']
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
     * @throws \Exception
     */
    public function getCreatedAt(): \DateTime
    {
        return !($this->createdAt instanceof \DateTime)
            ? new \DateTime($this->createdAt)
            : $this->createdAt;
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
            $this->setCreatedAt($dateTime);
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

    /**
     * @return array
     * @throws \Exception
     */
    public function toArray(): array
    {
        $choices = array_map(
            static function (Choice $choice) {
                return $choice->getText();
            },
            $this->getChoices()
        );

        return [
            'text' => $this->getText(),
            'createdAt' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'choice1' => $choices[0],
            'choice2' => $choices[1],
            'choice3' => $choices[2]
        ];
    }
}
