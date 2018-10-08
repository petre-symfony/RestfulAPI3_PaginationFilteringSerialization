<?php
/**
 * Created by PhpStorm.
 * User: petrero
 * Date: 14.09.2018
 * Time: 10:21
 */

namespace App\Controller\Web;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController {
	/**
	 * @Route("/", name="homepage")
	 */
	public function homepage(){
		return $this->render('homepage.html.twig');
	}
}