<?php

namespace Artyum\RequestDtoMapperBundle\EventListener;

use Artyum\RequestDtoMapperBundle\Attribute\Dto;
use Artyum\RequestDtoMapperBundle\Mapper\Mapper;
use Artyum\RequestDtoMapperBundle\Source\SourceInterface;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class ControllerArgumentsEventListener implements EventSubscriberInterface
{
    public function __construct(private Mapper $mapper)
    {
    }

    public function getSubjectFromControllerArguments(string $subject, array $arguments): ?object
    {
        foreach ($arguments as $argument) {
            if ($argument instanceof $subject) {
                return $argument;
            }
        }

        return null;
    }

    /**
     * @throws ReflectionException
     * @throws ExceptionInterface
     */
    public function onKernelControllerArguments(ControllerArgumentsEvent $event)
    {
        $controller = $event->getController();

        if (is_array($controller)) {
            $class = new ReflectionClass($controller[0]);
            $attributes = $class->getMethod($controller[1])->getAttributes(Dto::class);
        } else {
            $class = new ReflectionClass($controller);
            $attributes = $class->getMethod('__invoke')->getAttributes(Dto::class);
        }

        if (!$attributes) {
            return;
        }

        foreach ($attributes as $attribute) {
            /** @var Dto $attribute */
            $attribute = $attribute->newInstance();
            $subject = $this->getSubjectFromControllerArguments($attribute->getSubject(), $event->getArguments());

            if (!$subject) {
                throw new \LogicException(sprintf(
                    'The subject (%s) was not found in the controller arguments.',
                    $attribute->getSubject()
                ));
            }

            $this->mapper->map($event->getRequest(), $attribute, $subject);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER_ARGUMENTS => 'onKernelControllerArguments'
        ];
    }
}