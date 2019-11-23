<?php

namespace App\Entity;

use JMS\Serializer\Annotation as JMS;

class QuestionList
{
    /** @var Question[]
     * @JMS\Expose()
     * @JMS\Groups({"public"})
     */
    protected $data = [];

    /**
     * @return Question[]
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param Question[] $data
     *
     * @return QuestionList
     */
    public function setData(array $data): QuestionList
    {
        $this->data = $data;

        return $this;
    }
}
