<?php

namespace App\Controller;

use App\Entity\IFG_SDPD\InfoToSaisie;
use App\Form\InfoToSaisieType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response; 
use Symfony\Component\HttpFoundation\Session\Session;

class IndexController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(Request $request)
    {
        $info = new InfoToSaisie();
        $form = $this->createForm(InfoToSaisieType::class, $info);
        $form->handleRequest($request);

        return $this->render('index/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
