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
     * @Route("/", name="userpage")
     */
    public function index(Request $request)
    {
        $userManager = $this->get('fos_user.user_manager');
        $users = $userManager->findUsers();
        $user = array();
        foreach ($users as $value) {
            array_push($user, array(
                'username' => $value->getUsername(),
                'firstname' => $value->getFirstname(),
                'lastname' => $value->getLastname(),
                'email' => $value->getEmail(),
                'enable' => $value->isEnabled(),
                'lastlogin' => $value->getLastLogin(),
                'role' => $value->getRoles(),
                'regsterdate' => $value->getRegisterDate()
            ));
        }
        $users_paginate = $this->get('knp_paginator')->paginate($user, $request->query->get('page',1),10);
        return $this->render('user/index.html.twig', [
            'users_paginate' =>$users_paginate
        ]);
    }

    /**
     * @Route("/preCreate", name="preCreateuserpage")
     */
    public function preCreate()
    {
        return $this->render('user/create.html.twig');
    }

    /**
     * @Route("/create", name="createuserpage")
     */
    public function create()
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->createUser();

        /*$firstname = "Didi";
        $lastname = "TOTO";
        $username = "toto";
        $email = "toto@yunkun.org";
        $plainPassword = "11111111";
        $role = array("ROLE_USER");*/

        $username = $_POST['user_create_username'];
        $firstname = $_POST['user_create_firstname'];
        $lastname = $_POST['user_create_lastname'];
        $email = $_POST['user_create_email'];
        $role = array($_POST['user_create_roles']);
        $enable = $_POST['user_create_enable'];
        $plainPassword = $_POST['user_create_password'];

        date_default_timezone_set("Europe/Paris");
        $register_date = date_create(date('Y-m-d H:i:s'));

        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPlainPassword($plainPassword);
        $user->setRoles($role);
        $user->setRegisterDate($register_date);
        $user->setEnabled($enable);

        $userManager->updateUser($user);
        return $this->redirectToRoute('userpage');
    }

    /**
     * @Route("/preEdit/{username}", name="preEdituserpage")
     */
    public function preEdit($username)
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername($username);

        return $this->render('user/edit.html.twig', [
            'username' => $user->getUsername(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'email' => $user->getEmail(),
            'enable' => $user->isEnabled(),
            'role' => $user->getRoles()
        ]);
    }

    /**
     * @Route("/update/{username}", name="updateuserpage")
     */

    public function update(Request $request, $username=-1)
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername($username);

        $username = $_POST['user_edit_username'];
        $firstname = $_POST['user_edit_firstname'];
        $lastname = $_POST['user_edit_lastname'];
        $email = $_POST['user_edit_email'];
        $roles = array($_POST['user_edit_roles']);
        $enable = $_POST['user_edit_enable'];

        $user->setUsername($username);
        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setEmail($email);
        $user->setRoles($roles);
        $user->setEnabled($enable);

        $userManager->updateUser($user);

        return $this->redirectToRoute('userpage');
    }


    /**
     * @Route("/delete/{username}", name="deleteuserpage")
     */
    public function delete($username)
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername($username);
        $userManager->deleteUser($user);
        return $this->redirectToRoute('userpage');
    }
}
