<?php
namespace App\Serializer;


use App\Annotation\Link;
use App\Entity\Programmer;
use Doctrine\Common\Annotations\Reader;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\JsonSerializationVisitor;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class LinkSerializationSubscriber implements EventSubscriberInterface {
	/**
	 * @var RouterInterface
	 */
	private $router;
	/**
	 * @var Reader
	 */
	private $annotationReader;

	/** @var ExpressionLanguage */
	private $expressionLanguage;

	public function __construct(RouterInterface $router, Reader $annotationReader){
		$this->router = $router;
		$this->annotationReader = $annotationReader;
		$this->expressionLanguage = new ExpressionLanguage();
	}

	public static function getSubscribedEvents(){
		return [
			[
				'event' => 'serializer.post_serialize',
				'method' => 'onPostSerialize',
				'format' => 'json'
			]
		];
	}

	public function onPostSerialize(ObjectEvent $event){
		/** @var JsonSerializationVisitor $visitor */
		$visitor = $event->getVisitor();

		$object = $event->getObject();
		$annotations = $this->annotationReader
			->getClassAnnotations(new \ReflectionObject($object));

		$links = [];

		foreach ($annotations as $annotation){
			if ($annotation instanceof Link){
				$uri = $this->router->generate(
					$annotation->route,
					$this->resolveParams($annotation->params, $object)
				);

				$links[$annotation->name] = $uri;
			}
		}

		if($links){
			$visitor->setData('_links', $links);
		}
	}

	private function resolveParams($params, $object){
		foreach ($params as $key => $param){
			$params[$key] = $this->expressionLanguage
				->evaluate($param, [
					'object' => $object
				])
			;
		}
		return $params;
	}
}