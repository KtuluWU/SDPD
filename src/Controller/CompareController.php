<?php

namespace App\Controller;

use App\Entity\IFG_TEST2\UploadPdf;
use App\Form\UploadPdfType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response; 
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

/**
 * Compare controller.
 *
 * @Route("/compare")
 */

class CompareController extends Controller
{
    /**
     * @Route("/correction/{filename}", name="correctionpage")
     */
    public function correction(Request $request, $filename)
    {
        $api = ['a'=>'xxxxxxxxxx', 'b'=>'xxxxxxxxxx', 'c'=>'xxxxxxxxxx', 'd'=>'xxxxxxxxxx', 'e'=>'xxxxxxxxxx', 
            'f'=>'xxxxxxxxxx', 'g'=>'xxxxxxxxxx', 'h'=>'xxxxxxxxxx', 'i'=>'xxxxxxxxxx', 'j'=>'xxxxxxxxxx',
            '1'=>'xxxxxxxxxx', '2'=>'xxxxxxxxxx', '3'=>'xxxxxxxxxx', '4'=>'xxxxxxxxxx', '5'=>'xxxxxxxxxx',
            '6'=>'xxxxxxxxxx', '7'=>'xxxxxxxxxx', '8'=>'xxxxxxxxxx', '9'=>'xxxxxxxxxx', '10'=>'xxxxxxxxxx',
            '11'=>'xxxxxxxxxx', '12'=>'xxxxxxxxxx', '13'=>'xxxxxxxxxx', '14'=>'xxxxxxxxxx', '15'=>'xxxxxxxxxx',
            '16'=>'xxxxxxxxxx', '17'=>'xxxxxxxxxx', '18'=>'xxxxxxxxxx', '19'=>'xxxxxxxxxx', '20'=>'xxxxxxxxxx',
            '21'=>'xxxxxxxxxx', '22'=>'xxxxxxxxxx', '23'=>'xxxxxxxxxx', '24'=>'xxxxxxxxxx', '25'=>'xxxxxxxxxx'];

        return $this->render('compare/compare.html.twig', [
            'filename' => $filename,
            'api' => $api
        ]);
    }

    /**
     * @Route("/upload/{codegreffe}&{codestatut}&{chrono}&{millesime}&{numacte}&{numdepot}", name="uploadpage")
     */
    public function upload(Request $request, $codegreffe, $codestatut, $chrono, $millesime, $numacte, $numdepot)
    {
        $pdf = new UploadPdf();
        $form = $this->createForm(UploadPdfType::class, $pdf);
        $form->handleRequest($request);

        // $url_GED = "https://services.infogreffe.fr/wwwDemat/getDocument?codegreffe=$codegreffe&codestatut=$codestatut&chrono=$chrono&millesime=$millesime&numeroacte=$numacte&numerodepot=$numdepot&typeproduit=act&telecharge";
        $url_GED = "https://services.infogreffe.fr/wwwDemat/getDocument?codegreffe=2104&codestatut=B&chrono=00324&millesime=07&numeroacte=1&numerodepot=8&typeproduit=act";

        /**
         * Récupérer le pdf par cUrl
         */
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url_GED);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, 'INFOGREFFE:JsE2=BDC');

        $res_ch = curl_exec($ch);
        curl_close($ch);


        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('filename')->getData();
            $filename = $this->generateUniqueFileName($file);
            $file->move($this->getParameter('files'), $filename);            
            
            return $this->redirectToRoute('correctionpage', array('filename' => $filename));
        }

        return $this->render('compare/upload.html.twig', [
            'form' => $form->createView(),
            'res_ch' => $res_ch
        ]);
    }

    /**
     * @return string
     */
    private function generateUniqueFileName($file)
    {
        return md5(uniqid()).'.'.$file->guessExtension();;
    }
}

