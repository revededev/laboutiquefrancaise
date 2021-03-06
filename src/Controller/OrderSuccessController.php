<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderSuccessController extends AbstractController
{
    private $entityManager;
	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;	
	}
    /**
     * @Route("/commande/merci/{stripeSessionId}", name="order_validate")
     */
    public function index($stripeSessionId, Cart $cart): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        // dd($order);
        if(!$order || $order->getUser() !== $this->getUser()){
            return $this->redirectToRoute('home');
        }

        if(!$order->getIsPaid()){
            // isPaid = true
            $cart->remove();
            $order->setIsPaid(1);
            $this->entityManager->flush();
            // Envoye mail
        }

        // Afficher qq infos de la commande


        return $this->render('order_success/index.html.twig', [
            'order' => $order
        ]);
    }
}
