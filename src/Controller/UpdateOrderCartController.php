<?php
/**
 * Created by PhpStorm.
 * User: MUD0
 * Date: 28/11/2019
 * Time: 14:34
 */

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\OrderStatus;
use App\Form\UpdateOrderStatusType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\StateMachine;
use Twig\Environment;

/**
 * Class UpdateOrderCartController
 * @Route(methods={"GET", "POST"}, path="/carts/{id}/update", name="updateOrderStatus")
 */
class UpdateOrderCartController extends AbstractController
{
	private $workflows;
	//private $stateMachine;

	public function __construct(Registry $workflows/*, StateMachine $stateMachine*/)
	{
		$this->workflows = $workflows;
		//$this->stateMachine = $stateMachine;
	}

	public function __invoke(RouterInterface $router, Request $request, Cart $cart, OrderStatus $status, Environment $twig, FormFactoryInterface $formFactory, EntityManagerInterface $entityManager): Response
    {
        $form = $formFactory->create(UpdateOrderStatusType::class);

        $cartStatusOrder = $cart->getOrderStatus()->getId();

        $stateMachine = $this->workflows->get($cart, 'cart');

        $form->handleRequest($request);
        if($form->isSubmitted()) {

        	if($stateMachine->can($cart, 'ready')){
				$stateMachine->apply($cart, 'ready');
				$cart->setUpdatedAt();
			}

        	if($stateMachine->can($cart, 'done')){
        		$stateMachine->apply($cart, 'done');
			}

        	$entityManager->persist($cart);
            $entityManager->flush();

            return $this->redirect($router->generate('carts'));
        }

        return new Response($twig->render('updateOrderStatus.html.twig', [
            'form' => $form->createView(),
			'cart' => $cart
        ]));
    }
}