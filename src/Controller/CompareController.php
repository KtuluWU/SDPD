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
     * @Route("/correction/{filename}&{siren}", name="correctionpage")
     */
    public function correction(Request $request, $filename, $siren)
    {        
        # $api_orig = '{ "1":{"a":"xxxxxxxxxx", "b":"xxxxxxxxxx", "indirecte":"0"}, "2":{"a":"yyyyyyyyyyyyyyy", "b":"yyyyyyyyyyyyyyy", "indirecte":"1"}, "3":{"joint":"jointjointjoint", "b":"yyyyyyyyyyyyyyy"}}';
        # $apis = json_decode($api_orig, true);


        /**
         * API
         */
        $api_url_GET = "https://apidata.datainfogreffe.fr:8069/associes/rbe?siren=$siren";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url_GET);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_USERPWD, 'infogreffe:3fn4rg2ff2');

        $res_ch = curl_exec($ch);
        curl_close($ch);
        $apis = json_decode($res_ch, true);
        
        /**
         * DB TEST2
         */
        $em_TEST2 = $this->getDoctrine()->getManager('IFG_TEST2')->getConnection();
        $sql = "
            SELECT * FROM public.ta_suividem_ass p 
            WHERE p.siren='".$siren."' and p.codetypeacte='BENh' and p.dtsaisie is not null ORDER BY dtdepot DESC "; 
        $info_saisie = $em_TEST2->prepare($sql);
        $info_saisie->execute();
        $infos_db = ($info_saisie->fetchAll())[0];


        return $this->render('compare/compare.html.twig', [
            'filename' => $filename,
            'apis' => $apis,
            'siren' => $siren,
            'infos_db' => $infos_db
        ]); 
    }

    /**
     * @Route("/upload/{codegreffe}&{codestatut}&{chrono}&{millesime}&{numacte}&{numdepot}&{siren}", name="uploadpage")
     */
    public function upload(Request $request, $codegreffe, $codestatut, $chrono, $millesime, $numacte, $numdepot, $siren)
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
            
            return $this->redirectToRoute('correctionpage', array('filename' => $filename, 'siren' => $siren));
        }

        return $this->render('compare/upload.html.twig', [
            'form' => $form->createView(),
            'res_ch' => $res_ch
        ]);
    }

    /**
     * @Route("/preXML", name="preXMLpage")
     */
    public function preXML()
    {
        $denomination_sociale = array();
        $siren = array();
        $immatriculation = array();


        foreach($_POST as $key => $value) {
            if (strpos($key, 'denomination_sociale_') === 0) {
                array_push($denomination_sociale, $value);
            }
            if (strpos($key, 'siren_') === 0) {
                array_push($siren, $value);
            }
            if (strpos($key, 'immatriculation_') === 0) {
                array_push($immatriculation, $value);
            }
        } 

        return new Response(var_dump($denomination_sociale));
    }

    /**
     * @Route("/addDBE_S_2/{filename}/{api_length}", name="addDBE_S_2page")
     */
    public function addDBE_S_2($filename, $api_length)
    {
        $api_orig = '{ "1":{"a":"xxxxxxxxxx", "b":"xxxxxxxxxx", "indirecte":"0"}, "2":{"a":"yyyyyyyyyyyyyyy", "b":"yyyyyyyyyyyyyyy", "indirecte":"1"}, "3":{"joint":"jointjointjoint", "b":"yyyyyyyyyyyyyyy"}}';
        $apis = json_decode($api_orig, true);


        for ($i = (count($apis)-1); $i < $api_length; $i++) {
            $api_merge = ["$i"=>["a"=>" ", "b"=>" ", "indirecte"=>"0"]];
            $apis = array_merge($apis, $api_merge);
        }

        return $this->render('compare/compare.html.twig', [
            'filename' => $filename,
            'apis' => $apis
        ]); 
    }

    /**
     * @Route("/addDBE_S_bis/{filename}/{api_length}", name="addDBE_S_bispage")
     */
    public function addDBE_S_bis($filename, $api_length)
    {
       
        $api_orig = '{ "1":{"a":"xxxxxxxxxx", "b":"xxxxxxxxxx", "indirecte":"0"}, "2":{"a":"yyyyyyyyyyyyyyy", "b":"yyyyyyyyyyyyyyy", "indirecte":"1"}, "3":{"joint":"jointjointjoint", "b":"yyyyyyyyyyyyyyy"}}';
        $apis = json_decode($api_orig, true);


        for ($i = (count($apis)-1); $i < $api_length; $i++) {
            $api_merge = ["$i"=>["joint"=>" ", "b"=>" "]];
            $apis = array_merge($apis, $api_merge);
        }
        
        return $this->render('compare/compare.html.twig', [
            'filename' => $filename,
            'apis' => $apis
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

