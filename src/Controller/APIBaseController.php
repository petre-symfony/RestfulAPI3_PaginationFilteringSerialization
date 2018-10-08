<?php
namespace App\Controller;


use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class APIBaseController extends AbstractController {
	private $serializer;

	public function __construct(SerializerInterface $serializer){
		$this->serializer = $serializer;
	}

	protected function serialize($data){
		$context = new SerializationContext();
		$context->setSerializeNull(true);

		$request = $this->get('request_stack')->getCurrentRequest();
		$groups = ['Default'];
		if($request->query->get('deep')){
			$groups[] = 'deep';
		}
		$context->setGroups($groups);

		return $this->serializer->serialize($data, 'json', $context);
	}

	protected function createAPIResponse($data, $statusCode = 200){
		$json = $this->serialize($data);

		return new Response($json, $statusCode, [
			'Content-Type' => 'application/json'
		]);
	}
}