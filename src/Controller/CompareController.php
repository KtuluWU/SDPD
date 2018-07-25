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
        $forme_juridique = array();
        $adresse_sociale = array();
        $code_postal_sociale = array();
        $commune_sociale = array();
        $pays_sociale = array();
        $cilivile = array();
        $nom_naissance = array();
        $nom_usage = array();
        $pseudonyme = array();
        $prenom_principal = array();
        $prenom_autres = array();
        $naissance_date = array();
        $naissance_lieu = array();
        $departement_pays = array();
        $nationalite = array();
        $adresse_domicile = array();
        $code_postal_domicile = array();
        $commune_domicile = array();
        $pays_domicile = array();
        $detention_capital = array();
        $pourcentage_capital = array();
        $detention_droits = array();
        $pourcentage_droits = array();
        $exercice = array();
        $representant = array();
        $date_effect = array();


        foreach($_POST as $key => $value) {
            if (strpos($key, 'denomination_sociale_') === 0) {
                array_push($denomination_sociale, $value);
            }
            if (strpos($key, 'siren_') === 0) {
                array_push($siren, $value);
            }
            if (strpos($key, 'forme_juridique_') === 0) {
                array_push($forme_juridique, $value);
            }
            if (strpos($key, 'adresse_sociale_') === 0) {
                array_push($adresse_sociale, $value);
            }
            if (strpos($key, 'code_postal_sociale_') === 0) {
                array_push($code_postal_sociale, $value);
            }
            if (strpos($key, 'pays_sociale_') === 0) {
                array_push($pays_sociale, $value);
            }
            if (strpos($key, 'cilivile_') === 0) {
                array_push($cilivile, $value);
            }
            if (strpos($key, 'nom_naissance_') === 0) {
                array_push($nom_naissance, $value);
            }
            if (strpos($key, 'nom_usage_') === 0) {
                array_push($nom_usage, $value);
            }
            if (strpos($key, 'pseudonyme_') === 0) {
                array_push($pseudonyme, $value);
            }
            if (strpos($key, 'prenom_principal_') === 0) {
                array_push($prenom_principal, $value);
            }
            if (strpos($key, 'prenom_autres_') === 0) {
                array_push($prenom_autres, $value);
            }
            if (strpos($key, 'naissance_date_') === 0) {
                array_push($naissance_date, $value);
            }
            if (strpos($key, 'naissance_lieu_') === 0) {
                array_push($naissance_lieu, $value);
            }
            if (strpos($key, 'departement_pays_') === 0) {
                array_push($departement_pays, $value);
            }
            if (strpos($key, 'nationalite_') === 0) {
                array_push($nationalite, $value);
            }
            if (strpos($key, 'adresse_domicile_') === 0) {
                array_push($adresse_domicile, $value);
            }
            if (strpos($key, 'code_postal_domicile_') === 0) {
                array_push($code_postal_domicile, $value);
            }
            if (strpos($key, 'commune_domicile_') === 0) {
                array_push($commune_domicile, $value);
            }
            if (strpos($key, 'pays_domicile_') === 0) {
                array_push($pays_domicile, $value);
            }
            if (strpos($key, 'detention_capital_') === 0) {
                array_push($detention_capital, $value);
            }
            if (strpos($key, 'pourcentage_capital_') === 0) {
                array_push($pourcentage_capital, $value);
            }
            if (strpos($key, 'detention_droits_') === 0) {
                array_push($detention_droits, $value);
            }
            if (strpos($key, 'pourcentage_droits_') === 0) {
                array_push($pourcentage_droits, $value);
            }
            if (strpos($key, 'exercice_') === 0) {
                array_push($exercice, $value);
            }
            if (strpos($key, 'representant_') === 0) {
                array_push($representant, $value);
            }
            if (strpos($key, 'date_effect_') === 0) {
                array_push($date_effect, $value);
            }
        }

        $infos_xml = array(
            'denomination_sociale' => $denomination_sociale,
            'siren' => $siren,
            'immatriculation' => $immatriculation,
            'forme_juridique' => $forme_juridique,
            'adresse_sociale' => $adresse_sociale,
            'code_postal_sociale' => $code_postal_sociale,
            'commune_sociale' => $commune_sociale,
            'pays_sociale' => $pays_sociale,
            'cilivile' => $cilivile,
            'nom_naissance' => $nom_naissance,
            'nom_usage' => $nom_usage,
            'pseudonyme' => $pseudonyme,
            'prenom_principal' => $prenom_principal,
            'prenom_autres' => $prenom_autres,
            'naissance_date' => $naissance_date,
            'naissance_lieu' => $naissance_lieu,
            'departement_pays' => $departement_pays,
            'nationalite' => $nationalite,
            'adresse_domicile' => $adresse_domicile,
            'code_postal_domicile' => $code_postal_domicile,
            'commune_domicile' => $commune_domicile,
            'pays_domicile' => $pays_domicile,
            'detention_capital' => $detention_capital,
            'pourcentage_capital' => $pourcentage_capital,
            'detention_droits' => $detention_droits,
            'pourcentage_droits' => $pourcentage_droits,
            'exercice' => $exercice,
            'representant' => $representant,
            'date_effect' => $date_effect,

        );

        return new Response(var_dump($date_effect));
    }

    /**
     * @Route("/addDBE_S_2/{filename}/{beneficiaires}/{siren}", name="addDBE_S_2page")
     */
    public function addDBE_S_2($filename, $beneficiaires, $siren)
    {
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


        for ($i = (count($apis['beneficiaires'])-1); $i < $beneficiaires; $i++) {
            $api_merge = ["$i"=>[" "=>" "]];
            $apis['beneficiaires'] = array_merge($apis['beneficiaires'], $api_merge);
        }

        return $this->render('compare/compare.html.twig', [
            'filename' => $filename,
            'apis' => $apis,
            'siren' => $siren,
            'infos_db' => $infos_db
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

