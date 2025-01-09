<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Panel;
use App\Entity\Product;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use App\Repository\PanelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// #[Route('/admin/orders')]
final class OrderController extends AbstractController
{
    #[Route('/admin/orders',name: 'app_order_index', methods: ['GET'])]
    public function index(OrderRepository $orderRepository, EntityManagerInterface $entityManager): Response
    {
        $orders = $orderRepository->findAll();
        foreach ($orders as $order){
            $panels = $entityManager->getRepository(Panel::class)->findBy(['order_id'=>$order->getId()]);
            if (!empty($panels))
                $order->status = ($panels[0]->getStatus()==='confirm')? "In the Road.": $panels[0]->getStatus();
        }
        
        return $this->render('order/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('order/new', name: 'app_order_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $order = new Order();
        
        $panels = $entityManager->getRepository(Panel::class)->findby([
            'user' => $user,
            'status' => 'pending'
        ]);
        $total = 0;
        
        foreach ($panels as $panel) {
            $total += $panel->getProduct()->getPrice();
        }

        $order->setTotal($total);
        $order->setUser($user);

        $form = $this->createForm(OrderType::class, $order, [
            'user' => $user,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $userSub = $data->getUser();
            if(!$userSub){
                $this->addFlash('danger','Please authenticate !');
                return $this->redirectToRoute('app_home');
            }
            $form->getData()->setCreatedAt(new \DateTimeImmutable());
            $form->getData()->setUpdatedAt(new \DateTimeImmutable());

            $entityManager->persist($order);
            $entityManager->flush();
            
            $orderId = $order->getId();
            $panels = $entityManager->getRepository(Panel::class)->findBy(['user'=> $userSub]);
            foreach ($panels as $panel){
                if ($panel->getUser()->getId() === $userSub->getId() && $panel->getStatus() === 'pending'){
                    $panel->setStatus("confirm");
                    $panel->setOrderId($orderId);
                    $entityManager->persist($panel);
                }
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_order_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('order/new.html.twig', [
            'order' => $order,
            'form' => $form,
        ]);
    }

    #[Route('/admin/orders/{id}', name: 'app_order_show', methods: ['GET'])]
    public function show(Order $order, EntityManagerInterface $entityManager): Response
    {   
        $total = 0;
        $panels = $entityManager->getRepository(Panel::class)->findby(['order_id' => $order->getId(), 'status' => 'confirm']);
        
        foreach ($panels as $panel){
            $total += $panel->getProduct()->getPrice();
        }
        if (empty($panels)){
            $this->addFlash('danger', 'The products have been delivered so far.');
            return $this->redirectToRoute('app_order_index');
        }
        return $this->render('order/show.html.twig', [
            'order' => $order,
            'panels' => $panels,
            'total' => $total,
        ]);
    }

    #[Route('/admin/orders/{id}/edit', name: 'app_order_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Order $order, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OrderType::class, $order);
        $panels = $entityManager->getRepository(Panel::class)->findby(['order_id' => $order->getId(), 'status' => 'confirm']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_order_index', [], Response::HTTP_SEE_OTHER);
        }
        if (empty($panels)){
            $this->addFlash('danger', 'The products have been delivered so far.');
            return $this->redirectToRoute('app_order_index');
        }

        return $this->render('order/edit.html.twig', [
            'order' => $order,
            'form' => $form,
        ]);
    }
    
    #[Route('/admin/orders/{id}', name: 'app_order_delete', methods: ['POST'])]
    public function delete(Request $request, Order $order, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$order->getId(), $request->getPayload()->getString('_token'))) {
            $panels = $entityManager->getRepository(Panel::class)->findby([
                'user' => $this->getUser(),
                'status' => 'confirm',
                'order_id' => $order->getId()
            ]);
            foreach ($panels as $panel){
                $panel->setStatus('canceled');
                $entityManager->persist($panel);
            }
            $entityManager->remove($order);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_order_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/orders/{id}/delivered', name: 'app_order_delivered', methods: ['POST'])]
    public function delivered(Request $request, Order $order, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delivered'.$order->getId(), $request->request->get('_token'))) {
            $panels = $entityManager->getRepository(Panel::class)->findBy([
                'user' => $this->getUser(),
                'status' => 'confirm',
                'order_id' => $order->getId()
            ]);
            // dd($panels);
            foreach ($panels as $panel) {
                $panel->setStatus('delivered');
                $entityManager->persist($panel); // Persist each panel within the loop
            }

            $entityManager->flush(); // Flush all changes to the database
        }

        return $this->redirectToRoute('app_order_index', [], Response::HTTP_SEE_OTHER);
    }

}
