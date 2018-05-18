<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LuckyController extends Controller
{
    /**
     * @Route("/lucky", name="lucky")
     */
    public function number($max = 100)
    {
        $number = mt_rand(0, $max);
        return $this->render('lucky/number.html.twig', [
            'controller_name' => 'LuckyController',
            'number' => $number,
        ]);
    }
}
