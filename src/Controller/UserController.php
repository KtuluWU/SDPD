<?php

namespace App\Controller;

use App\Entity\IFG_SDPD\User;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\ArrayLoader;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Doctrine\UserManager;

/**
 * User controller.
 *
 * @Route("/User")
 */

class UserController extends Controller
{

    /**
     * @Route("/create", name="adduserpage")
     */
    public function create()
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->createUser();

        $firstname = "Kun";
        $lastname = "XU";
        $username = "kun";
        $email = "kun@yunkun.org";
        $plainPassword = "wycjdhr1991621";
        $role = array("ROLE_ADMIN");

        date_default_timezone_set("Europe/Paris");
        $register_date = date_create(date('Y-m-d H:i:s'));

        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPlainPassword($plainPassword);
        $user->setRoles($role);
        $user->setRegisterDate($register_date);
        $user->setEnabled(true);

        $userManager->updateUser($user);
        return $this->redirectToRoute('homepage');
    }
}
