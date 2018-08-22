<?php

namespace App\Controller;

use App\Entity\IFG_TEST2\InfoToSaisie;
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
        
        if ($form->isSubmitted() && $form->isValid()) {
            $siren_saisie = $info->getSiren();
            $siren = str_replace(' ', '', $siren_saisie);

            $em_TEST2 = $this->getDoctrine()->getManager('IFG_TEST2')->getConnection();
            $sql = "
                SELECT * FROM public.ta_suividem_ass p 
                WHERE p.siren='".$siren."' and p.codetypeacte='BENh' and p.dtsaisie is not null ORDER BY dtdepot DESC "; // date de saisie non null
            $info_saisie = $em_TEST2->prepare($sql);
            $info_saisie->execute();
            $infos_db = $info_saisie->fetchAll();

            if (empty($infos_db)) {
                return $this->render('error/siren.html.twig', [
                    'siren' => $siren
                ]);
            } else {
                $res = $infos_db[0];
            }

            $codegreffe = $res['codegreffe'];
            $numgestion = $res['numerogestion'];
            $numacte = $res['noacte'];
            $numdepot = $res['numdepot'];

            $codestatut = substr($numgestion, 4, 1);
            $chrono = substr($numgestion, 5);
            $millesime = substr($numgestion, 2, 2);

            return $this->redirectToRoute('correctionpage', array('codegreffe'=>$codegreffe, 'codestatut'=>$codestatut, 'chrono'=>$chrono, 'millesime'=>$millesime, 'numacte'=>$numacte, 'numdepot'=>$numdepot, 'siren'=>$siren));

            // return new Response ("Code greffe: ".$codegreffe."<br>Numéro gestion: ".$numgestion."<br>Code statut: ".$codestatut."<br>Chrono: ".$chrono."<br>Millesime: ".$millesime."<br>Numéro d'acte: ".$numacte."<br>Numéro de dépôt: ".$numdepot."<br>".$url_GED);
        }

        return $this->render('index/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
