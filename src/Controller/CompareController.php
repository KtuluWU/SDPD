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
        $civilite = array();
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
            if (strpos($key, 'commune_sociale_') === 0) {
                array_push($commune_sociale, $value);
            }
            if (strpos($key, 'pays_sociale_') === 0) {
                array_push($pays_sociale, $value);
            }
            if (strpos($key, 'civilite_') === 0) {
                array_push($civilite, $value);
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

        $len_array = count($siren);
        $infos_xml = array();

        $denomination_sociale = $this->infos_check($siren, $denomination_sociale);
        $immatriculation = $this->infos_check($siren, $immatriculation);
        $forme_juridique = $this->infos_check($siren, $forme_juridique);
        $adresse_sociale = $this->infos_check($siren, $adresse_sociale);
        $code_postal_sociale = $this->infos_check($siren, $code_postal_sociale);
        $commune_sociale = $this->infos_check($siren, $commune_sociale);
        $pays_sociale = $this->infos_check($siren, $pays_sociale);
        $civilite = $this->infos_check($siren, $civilite);
        $nom_naissance = $this->infos_check($siren, $nom_naissance);
        $nom_usage = $this->infos_check($siren, $nom_usage);
        $pseudonyme = $this->infos_check($siren, $pseudonyme);
        $prenom_principal = $this->infos_check($siren, $prenom_principal);
        $prenom_autres = $this->infos_check($siren, $prenom_autres);
        $naissance_date = $this->infos_check($siren, $naissance_date);
        $naissance_lieu = $this->infos_check($siren, $naissance_lieu);
        $departement_pays = $this->infos_check($siren, $departement_pays);
        $nationalite = $this->infos_check($siren, $nationalite);
        $adresse_domicile = $this->infos_check($siren, $adresse_domicile);
        $code_postal_domicile = $this->infos_check($siren, $code_postal_domicile);
        $commune_domicile = $this->infos_check($siren, $commune_domicile);
        $pays_domicile = $this->infos_check($siren, $pays_domicile);
        $detention_capital = $this->infos_check($siren, $detention_capital);
        $pourcentage_capital = $this->infos_check($siren, $pourcentage_capital);
        $detention_droits = $this->infos_check($siren, $detention_droits);
        $pourcentage_droits = $this->infos_check($siren, $pourcentage_droits);
        $exercice = $this->infos_check($siren, $exercice);
        $representant = $this->infos_check($siren, $representant);
        $date_effect = $this->infos_check($siren, $date_effect);

        for($i = 0; $i < $len_array; $i++) {
            array_push($infos_xml, array(
                $i => array(
                    'denomination_sociale' => $denomination_sociale[$i],
                    'siren' => $siren[$i],
                    'immatriculation' => $immatriculation[$i],
                    'forme_juridique' => $forme_juridique[$i],
                    'adresse_sociale' => $adresse_sociale[$i],
                    'code_postal_sociale' => $code_postal_sociale[$i],
                    'commune_sociale' => $commune_sociale[$i],
                    'pays_sociale' => $pays_sociale[$i],
                    'civilite' => $civilite[$i],
                    'nom_naissance' => $nom_naissance[$i],
                    'nom_usage' => $nom_usage[$i],
                    'pseudonyme' => $pseudonyme[$i],
                    'prenom_principal' => $prenom_principal[$i],
                    'prenom_autres' => $prenom_autres[$i],
                    'naissance_date' => $naissance_date[$i],
                    'naissance_lieu' => $naissance_lieu[$i],
                    'departement_pays' => $departement_pays[$i],
                    'nationalite' => $nationalite[$i],
                    'adresse_domicile' => $adresse_domicile[$i],
                    'code_postal_domicile' => $code_postal_domicile[$i],
                    'commune_domicile' => $commune_domicile[$i],
                    'pays_domicile' => $pays_domicile[$i],
                    'detention_capital' => $detention_capital[$i],
                    'pourcentage_capital' => $pourcentage_capital[$i],
                    'detention_droits' => $detention_droits[$i],
                    'pourcentage_droits' => $pourcentage_droits[$i],
                    'exercice' => $exercice[$i],
                    'representant' => $representant[$i],
                    'date_effect' => $date_effect[$i]
                )
            ));
        }

        /**
         * XML
         */
        $xw = xmlwriter_open_memory();
        xmlwriter_set_indent($xw, 1);
        $res = xmlwriter_set_indent_string($xw, ' ');

        xmlwriter_start_document($xw, '1.0', 'UTF-8');
            xmlwriter_start_element($xw, 'listeSaisiesBE');
                xmlwriter_start_element($xw, 'VersionSchema');
                xmlwriter_text($xw, '1');
                xmlwriter_end_element($xw);

                xmlwriter_start_element($xw, 'SaisieBE');
                    xmlwriter_start_element($xw, 'dateDemande');
                    xmlwriter_text($xw, '2018-06-01');
                    xmlwriter_end_element($xw);

                    xmlwriter_start_element($xw, 'numeroDemande');
                    xmlwriter_text($xw, 'AMITEL_20180601_2100_555');
                    xmlwriter_end_element($xw);

                    xmlwriter_start_element($xw, 'typeDemande');
                    xmlwriter_text($xw, '1');
                    xmlwriter_end_element($xw);

                    xmlwriter_start_element($xw, 'typeDeclaration');
                    xmlwriter_text($xw, '0');
                    xmlwriter_end_element($xw);

                    xmlwriter_start_element($xw, 'identificationEntreprise');
                        xmlwriter_start_element($xw, 'denomination');
                        xmlwriter_text($xw, $infos_xml[0][0]['denomination_sociale']);
                        xmlwriter_end_element($xw);

                        xmlwriter_start_element($xw, 'siren');
                        xmlwriter_text($xw, $infos_xml[0][0]['siren']);
                        xmlwriter_end_element($xw);

                        xmlwriter_start_element($xw, 'codeGreffe');
                        xmlwriter_text($xw, '0401');
                        xmlwriter_end_element($xw);

                        xmlwriter_start_element($xw, 'numeroGestion');
                            xmlwriter_start_element($xw, 'Millesime');
                            xmlwriter_text($xw, '2016');
                            xmlwriter_end_element($xw);

                            xmlwriter_start_element($xw, 'Statut');
                            xmlwriter_text($xw, 'D');
                            xmlwriter_end_element($xw);

                            xmlwriter_start_element($xw, 'Chrono');
                            xmlwriter_text($xw, '00224');
                            xmlwriter_end_element($xw);
                        xmlwriter_end_element($xw);

                        xmlwriter_start_element($xw, 'formeJuridique');
                            xmlwriter_start_element($xw, 'code');
                            xmlwriter_text($xw, 'GFAh');
                            xmlwriter_end_element($xw);
                            
                            xmlwriter_start_element($xw, 'libelle');
                            xmlwriter_text($xw, 'Groupement foncier agricole');
                            xmlwriter_end_element($xw);
                        xmlwriter_end_element($xw);

                        xmlwriter_start_element($xw, 'nbBE');
                        xmlwriter_text($xw, '1');
                        xmlwriter_end_element($xw);

                        xmlwriter_start_element($xw, 'adresse');
                            xmlwriter_start_element($xw, 'ligne1');
                            xmlwriter_text($xw, $infos_xml[0][0]['adresse_sociale']);
                            xmlwriter_end_element($xw);

                            xmlwriter_start_element($xw, 'ligne2');
                            xmlwriter_text($xw, ' ');
                            xmlwriter_end_element($xw);

                            xmlwriter_start_element($xw, 'ligne3');
                            xmlwriter_text($xw, ' ');
                            xmlwriter_end_element($xw);

                            xmlwriter_start_element($xw, 'localite');
                            xmlwriter_text($xw, $infos_xml[0][0]['commune_sociale']);
                            xmlwriter_end_element($xw);

                            xmlwriter_start_element($xw, 'codePostal');
                            xmlwriter_text($xw, $infos_xml[0][0]['code_postal_sociale']);
                            xmlwriter_end_element($xw);

                            xmlwriter_start_element($xw, 'codeCommuneINSEE');
                            xmlwriter_text($xw, '222');
                            xmlwriter_end_element($xw);

                            xmlwriter_start_element($xw, 'pays');
                            xmlwriter_text($xw, $infos_xml[0][0]['pays_sociale']);
                            xmlwriter_end_element($xw);
                        xmlwriter_end_element($xw);
                    xmlwriter_end_element($xw); // 'identificationEntreprise'

                    xmlwriter_start_element($xw, 'infoSaisie');
                        xmlwriter_start_element($xw, 'dateSaisie');
                        xmlwriter_text($xw, '2018-06-18');
                        xmlwriter_end_element($xw);

                        xmlwriter_start_element($xw, 'codeRejet');
                        xmlwriter_text($xw, '0');
                        xmlwriter_end_element($xw);

                        xmlwriter_start_element($xw, 'identificationDepot');
                            xmlwriter_start_element($xw, 'dateDepot');
                            xmlwriter_text($xw, '2018-03-26');
                            xmlwriter_end_element($xw);

                            xmlwriter_start_element($xw, 'numeroDepot');
                            xmlwriter_text($xw, '1683');
                            xmlwriter_end_element($xw);

                            xmlwriter_start_element($xw, 'acte');
                                xmlwriter_start_element($xw, 'dateActe');
                                xmlwriter_text($xw, '2018-03-12');
                                xmlwriter_end_element($xw);

                                xmlwriter_start_element($xw, 'numeroActe');
                                xmlwriter_text($xw, '1');
                                xmlwriter_end_element($xw);

                                xmlwriter_start_element($xw, 'typeActe');
                                xmlwriter_text($xw, 'BENh');
                                xmlwriter_end_element($xw);

                                xmlwriter_start_element($xw, 'libelleActe');
                                xmlwriter_text($xw, 'Document relatif au bénéficiaire effectif');
                                xmlwriter_end_element($xw);
                            xmlwriter_end_element($xw);
                        xmlwriter_end_element($xw); // 'identificationDepot'
                    xmlwriter_end_element($xw); // 'infoSaisie'

                    for ($i = 0; $i < $len_array; $i++) {
                        xmlwriter_start_element($xw, 'BEffectif');
                            xmlwriter_start_element($xw, 'Civilite');
                            xmlwriter_text($xw, $infos_xml[$i][$i]['civilite']);
                            xmlwriter_end_element($xw);

                            xmlwriter_start_element($xw, 'nomPatronymique');
                            xmlwriter_text($xw, $infos_xml[$i][$i]['nom_naissance']);
                            xmlwriter_end_element($xw);

                            xmlwriter_start_element($xw, 'nomMarital');
                            xmlwriter_text($xw, $infos_xml[$i][$i]['nom_usage']);
                            xmlwriter_end_element($xw);

                            xmlwriter_start_element($xw, 'Pseudonyme');
                            xmlwriter_text($xw, $infos_xml[$i][$i]['pseudonyme']);
                            xmlwriter_end_element($xw);

                            xmlwriter_start_element($xw, 'Prenoms');
                            xmlwriter_text($xw, $infos_xml[$i][$i]['prenom_principal']);
                            xmlwriter_end_element($xw);

                            xmlwriter_start_element($xw, 'infoNaissance');
                                xmlwriter_start_element($xw, 'dateNaissance');
                                xmlwriter_text($xw, $infos_xml[$i][$i]['naissance_date']);
                                xmlwriter_end_element($xw);

                                xmlwriter_start_element($xw, 'villeNaissance');
                                xmlwriter_text($xw, $infos_xml[$i][$i]['naissance_lieu']);
                                xmlwriter_end_element($xw);

                                xmlwriter_start_element($xw, 'codeCommuneINSEE');
                                xmlwriter_text($xw, '59350');
                                xmlwriter_end_element($xw);

                                xmlwriter_start_element($xw, 'DepartementNaissance');
                                xmlwriter_text($xw, '59');
                                xmlwriter_end_element($xw);

                                xmlwriter_start_element($xw, 'paysNaissance');
                                xmlwriter_text($xw, $infos_xml[$i][$i]['departement_pays']);
                                xmlwriter_end_element($xw);

                                xmlwriter_start_element($xw, 'Nationalite');
                                xmlwriter_text($xw, $infos_xml[$i][$i]['nationalite']);
                                xmlwriter_end_element($xw);
                            xmlwriter_end_element($xw);

                            xmlwriter_start_element($xw, 'adresse');
                                xmlwriter_start_element($xw, 'ligne1');
                                xmlwriter_text($xw, $infos_xml[$i][$i]['adresse_domicile']);
                                xmlwriter_end_element($xw);

                                xmlwriter_start_element($xw, 'localite');
                                xmlwriter_text($xw, $infos_xml[$i][$i]['commune_domicile']);
                                xmlwriter_end_element($xw);

                                xmlwriter_start_element($xw, 'codePostal');
                                xmlwriter_text($xw, $infos_xml[$i][$i]['code_postal_domicile']);
                                xmlwriter_end_element($xw);

                                xmlwriter_start_element($xw, 'codeCommuneINSEE');
                                xmlwriter_text($xw, '111');
                                xmlwriter_end_element($xw);

                                xmlwriter_start_element($xw, 'pays');
                                xmlwriter_text($xw, $infos_xml[$i][$i]['pays_domicile']);
                                xmlwriter_end_element($xw);
                            xmlwriter_end_element($xw); 

                            xmlwriter_start_element($xw, 'modaliteControle');
                                xmlwriter_start_element($xw, 'modaliteSociete');
                                    xmlwriter_start_element($xw, 'detentionCapital');
                                        xmlwriter_start_element($xw, 'type');
                                        xmlwriter_text($xw, $infos_xml[$i][$i]['detention_capital']);
                                        xmlwriter_end_element($xw);

                                        xmlwriter_start_element($xw, 'pourcentage');
                                        xmlwriter_text($xw, $infos_xml[$i][$i]['pourcentage_capital']);
                                        xmlwriter_end_element($xw);
                                    xmlwriter_end_element($xw);

                                    xmlwriter_start_element($xw, 'detentionDroitsVote');
                                        xmlwriter_start_element($xw, 'type');
                                        xmlwriter_text($xw, $infos_xml[$i][$i]['detention_droits']);
                                        xmlwriter_end_element($xw);

                                        xmlwriter_start_element($xw, 'pourcentage');
                                        xmlwriter_text($xw, $infos_xml[$i][$i]['pourcentage_droits']);
                                        xmlwriter_end_element($xw);
                                    xmlwriter_end_element($xw);
                                xmlwriter_end_element($xw);
                            xmlwriter_end_element($xw);

                            xmlwriter_start_element($xw, 'dateEffet');
                            xmlwriter_text($xw, $infos_xml[$i][$i]['date_effect']);
                            xmlwriter_end_element($xw);

                        xmlwriter_end_element($xw); // 'BEffectif'
                    }
                xmlwriter_end_element($xw);
            xmlwriter_end_element($xw);
        xmlwriter_end_document($xw);

        $res_xml = xmlwriter_output_memory($xw);

        $files_route = ($this->getParameter('files'))."/test.xml";
        $file_xml = fopen($files_route, "w");
        fwrite($file_xml, $res_xml);
        fclose($file_xml);
        return new Response($res_xml);
        // return new Response(var_dump($infos_xml));
        
    }


    /**
     * @return array
     */
    private function infos_check($siren=array(), $infos=array())
    {
        $len_array = count($siren);
        $len_info = count($infos);
        if ( $len_info < $len_array ) {
            for ($i = $len_info; $i < $len_array; $i++) {
                array_push($infos, " ");
            }
        }
        return $infos;
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

