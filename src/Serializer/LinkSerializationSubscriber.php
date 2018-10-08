<?php
namespace App\Serializer;


use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;

class LinkSerializationSubscriber implements EventSubscriberInterface {
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

	}
}