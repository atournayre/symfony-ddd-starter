<?php

namespace App\Infrastructure\Bundle\UtilisateurBundle\Controller;

use App\Domain\Entity\Utilisateur\Utilisateur;
use Exception;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            '@Utilisateur/security/login.html.twig',
            [
                'last_username' => $lastUsername,
                'error' => $error,
            ]
        );
    }

    /**
     * @param ParameterBagInterface $params
     * @return RedirectResponse
     */
    public function logout(ParameterBagInterface $params): RedirectResponse
    {
        return $this->redirectToRoute($params->get('redirect.login'));
    }

    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param ParameterBagInterface $params
     * @return RedirectResponse|Response
     */
    public function enregistrer(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        ParameterBagInterface $params
    ): Response {
        if ($request->isMethod('POST')) {
            $user = new Utilisateur();
            $user->setEmail($request->request->get('email'));
            $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute($params->get('redirect.enregistrer'));
        }

        return $this->render('@Utilisateur/security/enregistrer.html.twig');
    }

    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param Swift_Mailer $mailer
     * @param TokenGeneratorInterface $tokenGenerator
     * @param ParameterBagInterface $params
     * @return Response
     */
    public function mdpOublie(
        Request $request,
        UserPasswordEncoderInterface $encoder,
        Swift_Mailer $mailer,
        TokenGeneratorInterface $tokenGenerator,
        ParameterBagInterface $params
    ): Response {

        if ($request->isMethod('POST')) {

            $email = $request->request->get('email');

            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(Utilisateur::class)->findOneByEmail($email);
            /* @var $user Utilisateur */

            if (is_null($user)) {
                $this->addFlash('danger', 'Email Inconnu');
                return $this->redirectToRoute($params->get('redirect.mdp.oublie.email.inconnu'));
            }
            $token = $tokenGenerator->generateToken();

            try {
                $user->setResetToken($token);
                $entityManager->flush();
            } catch (Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                return $this->redirectToRoute($params->get('redirect.mdp.oublie.erreur.token'));
            }

            $url = $this->generateUrl('app_reset_mdp', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);

            $message = (new Swift_Message('[Fondatools] Mot de passe oublié'))
                ->setFrom('fondatools@fondatools.fr')
                ->setTo($user->getEmail())
                ->setBody(
                    "Veuilliez suivre ce lien pour changer votre mot de passe : " . $url,
                    'text/html'
                );

            $mailer->send($message);
            $this->addFlash('notice', 'Mail envoyé');

            return $this->redirectToRoute($params->get('redirect.mdp.oublie'));
        }
        return $this->render('@Utilisateur/security/mot_de_passe_oublie.html.twig');
    }

    /**
     * @param Request $request
     * @param string $token
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param ParameterBagInterface $params
     * @return RedirectResponse|Response
     */
    public function resetMdp(
        Request $request,
        string $token,
        UserPasswordEncoderInterface $passwordEncoder,
        ParameterBagInterface $params
    ): Response {

        if ($request->isMethod('POST')) {
            $entityManager = $this->getDoctrine()->getManager();

            $user = $entityManager->getRepository(Utilisateur::class)->findOneByResetToken($token);
            /* @var $user Utilisateur */

            if (is_null($user)) {
                $this->addFlash('danger', 'Token Inconnu');
                return $this->redirectToRoute('default');
            }

            $user->setResetToken(null);
            $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));
            $entityManager->flush();

            $this->addFlash('notice', 'Mot de passe mis à jour');

            return $this->redirectToRoute($params->get('redirect.reset.mdp'));
        } else {
            return $this->render('@Utilisateur/security/reset_password.html.twig', ['token' => $token]);
        }
    }
}