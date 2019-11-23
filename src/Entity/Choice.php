<?php

namespace App\Entity;

use JMS\Serializer\Annotation as JMS;

class Choice
{
    /** @var string
     * @JMS\Expose()
     * @JMS\Groups({"public"})
     */
    protected $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

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
     * @return Choice
     */
    public function setText(string $text): Choice
    {
        $this->text = $text;

        return $this;
    }
}
