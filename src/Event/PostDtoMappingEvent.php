<?php

namespace Artyum\RequestDtoMapperBundle\Event;

use Artyum\RequestDtoMapperBundle\Attribute\Dto;
use Artyum\RequestDtoMapperBundle\Mapper\DtoInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * This event is dispatched once the mapping is done (and before the validation if enabled), this allows you to alter the DTO before it's passed to the controller.
 */
class PostDtoMappingEvent extends Event
{
}
