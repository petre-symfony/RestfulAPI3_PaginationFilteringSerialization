<?php
namespace App\Controller\API;


use App\API\ApiProblem;
use App\API\ApiProblemException;
use App\Controller\APIBaseController;
use App\Entity\Programmer;
use App\Form\ProgrammerType;
use App\Form\UpdateProgrammerType;
use App\Pagination\PaginatedCollection;
use App\Pagination\PaginationFactory;
use App\Repository\ProgrammerRepository;
use App\Repository\UserRepository;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProgrammerController extends APIBaseController {

	/**
	 * @Route("/api/programmers", methods="POST")
	 */
	public function newAction(Request $request, UserRepository $userRepository){

		$programmer = new Programmer();
		$form = $this->createForm(ProgrammerType::class, $programmer);
		$this->processForm($request, $form);

		if(!$form->isValid()){
			$this->throwAPIProblemException($form);
		}

		$programmer->setUser($userRepository->findOneBy(['username' => 'weaverryan']));

		$em = $this->getDoctrine()->getManager();
		$em->persist($programmer);
		$em->flush();

		$response = $this->createAPIResponse($programmer, 201);
		$location = $this->generateUrl("api_programmers_show", [
			'nickname' => $programmer->getNickname()]);

		$response->headers->set('Location',  $location);

		return $response;
	}

	/**
	 * @Route("/api/programmers/{nickname}", name="api_programmers_show", methods="GET")
	 */
	public function showAction(ProgrammerRepository $programmerRepository, $nickname){
		$programmer = $programmerRepository->findOneBy(['nickname' => $nickname]);
		if(!$programmer){
			throw $this->createNotFoundException('No programmer found for username ' . $nickname);
		}

		return $this->createAPIResponse($programmer);
	}

	/**
	 * @Route("/api/programmers/{nickname}", name="api_programmers_update", methods="PATCH|PUT")
	 */
	public function updateAction($nickname, Request $request,
		ProgrammerRepository $programmerRepository
	){
		$programmer = $programmerRepository->findOneBy(['nickname' => $nickname]);

		if(!$programmer){
			throw $this->createNotFoundException('No programmer found for username ' . $nickname);
		}

		$form = $this->createForm(UpdateProgrammerType::class, $programmer);
		$this->processForm($request, $form);

		if(!$form->isValid()){
			$this->throwAPIProblemException($form);
		}

		$em = $this->getDoctrine()->getManager();
		$em->persist($programmer);
		$em->flush();

		return $this->createAPIResponse($programmer);
	}

	/**
	 * @Route("/api/programmers/{nickname}", methods="DELETE")
	 */
	public function deleteAction($nickname, ProgrammerRepository $programmerRepository){
		$programmer = $programmerRepository->findOneBy(['nickname' => $nickname]);

		if($programmer){
			$em = $this->getDoctrine()->getManager();
			$em->remove($programmer);
			$em->flush();
		}

		return new Response(null, 204);
	}

	/**
	 * @Route("/api/programmers", name="api_programmers_list", methods="GET")
	 */
	public function listAction(
		Request $request,
		ProgrammerRepository $programmerRepository,
		PaginationFactory $paginationFactory
	){
		$filter = $request->query->get('filter');

		$qb  = $programmerRepository->findAllQueryBuilder($filter);

		$paginatedCollection = $paginationFactory->createCollection(
			$qb,
			$request,
			'api_programmers_list'
		);
		return $this->createAPIResponse($paginatedCollection);
	}

	private function processForm(Request $request, FormInterface $form){
		$body = $request->getContent();
		$data = json_decode($body, true);

		if ($data == null){
			$apiProblem = new ApiProblem(
				400,
				ApiProblem::TYPE_INVALID_REQUEST_BODY_FORMAT
			);

			throw new ApiProblemException($apiProblem);
		}

		$clearMissing = $request->getMethod() !== 'PATCH';
		$form->submit($data, $clearMissing);
	}

	private function getErrorsFromForm(FormInterface $form){
		$errors = array();
		foreach ($form->getErrors() as $error) {
			$errors[] = $error->getMessage();
		}
		foreach ($form->all() as $childForm) {
			if ($childForm instanceof FormInterface) {
				if ($childErrors = $this->getErrorsFromForm($childForm)) {
					$errors[$childForm->getName()] = $childErrors;
				}
			}
		}
		return $errors;
	}

	private function throwAPIProblemException(FormInterface $form){
		$errors = $this->getErrorsFromForm($form);

		$apiProblem = new ApiProblem(
			400,
			ApiProblem::TYPE_VALIDATION_ERROR
		);
		$apiProblem->set('errors', $errors);


		throw new ApiProblemException($apiProblem);
	}
}