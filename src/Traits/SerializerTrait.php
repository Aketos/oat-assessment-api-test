<?php

namespace App\Traits;

use JMS\Serializer\SerializerInterface;

trait SerializerTrait
{
    /** @var SerializerInterface */
    protected $serializer;

    /**
     * @return SerializerInterface
     */
    public function getSerializer(): SerializerInterface
    {
        return $this->serializer;
    }

    /**
     * @param SerializerInterface $serializer
     *
     * @return self
     */
    public function setSerializer($serializer): self
    {
        $this->serializer = $serializer;

        return $this;
    }
}
