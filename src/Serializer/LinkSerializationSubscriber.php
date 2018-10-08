<?php
namespace App\Serializer;


use App\Entity\Programmer;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\JsonSerializationVisitor;
use Symfony\Component\Routing\RouterInterface;

class LinkSerializationSubscriber implements EventSubscriberInterface {
	/**
	 * @var RouterInterface
	 */
	private $router;

	public function __construct(RouterInterface $router){
		$this->router = $router;
	}

	public static function getSubscribedEvents(){
		return [
			[
				'event' => 'serializer.post_serialize',
				'method' => 'onPostSerialize',
				'format' => 'json',
				'class' => 'App\Entity\Programmer'
			]
		];
	}

	public function onPostSerialize(ObjectEvent $event){
		/** @var JsonSerializationVisitor $visitor */
		$visitor = $event->getVisitor();
		/** @var Programmer $programmer */
		$programmer = $event->getObject();
		$visitor->addData(
			'uri',
			$this->router->generate(
				'api_programmers_show',
				[
					'nickname' => $programmer->getNickname()
				]
			)
		);
	}
}