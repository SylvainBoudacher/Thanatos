<?php

namespace App\Controller\Front;

use App\Entity\Corpse;
use App\Form\CorpseType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route('/declarer-un-corps', name: 'declare_corpse')]
    public function declareCorpse(): Response
    {

        $form = new Corpse();
        $form = $this->createForm(CorpseType::class, $form);
        return $this->renderForm('front/declareCorpse.html.twig',  [
                'form' => $form
        ]);
    }

}