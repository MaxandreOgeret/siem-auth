<?php

namespace AppBundle\Controller;

use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request, LoggerInterface $logger, \Swift_Mailer $mailer)
    {
        //test mail
//        $message = (new \Swift_Message('Hello Email'))
//        ->setFrom('send@example.com')
//        ->setTo('maxandreogeret@gmail.com')
//        ->setBody($this->renderView('default/mailalex.html.twig'), 'text/html');
//        $mailer->send($message);

        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @return JsonResponse
     * @param $mail
     * @Route("/handlemail")
     */
    public function handleEmailAction(Request $request, ValidatorInterface $validator, \Swift_Mailer $mailer)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $mail = $request->get('mail');
        $to = $mail['to'];
        $objet = $mail['objet'];
        $body = $mail['body'];

        $emailConstraint = new Assert\Email();
        $emailConstraint->message = 'Invalid email address';

        $errors = $validator->validate(
            $to,
            $emailConstraint
        );

        if (count($errors) === 0 && mb_strlen($objet)>0) {
            $message = (new \Swift_Message($objet))
            ->setFrom('siem@example.dom')
            ->setTo($to)
            ->setBody($body, 'text/html');
            $mailer->send($message);

            return new JsonResponse('ok');
        } else {
            return new JsonResponse('ko');
        }
    }
}

