<?php

namespace App\Controller;

use App\Entity\Panel;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }
    
    #[Route('/about', name: 'app_about')]
    public function about() : Response {
        return $this->render('home/about.html.twig');
    }

    #[Route('/panel/new', name: 'app_panel_add', methods: ['POST'])]
    public function addToPanel(Request $request, EntityManagerInterface $entityManager): Response{
        $user = $this->getUser();
        $productId = $request->request->get('productId');
        $product=$entityManager->getRepository(Product::class)->find($productId);

        if (!$product){
            throw $this->createNotFoundException('No product found for id '. $productId);
        }

        $panel = new Panel();
        $panel->setUser($user);
        $panel->setProduct($product);
        $panel->setStatus('pending');

        $entityManager->persist($panel);
        $entityManager->flush();
        $this->addFlash('success', 'Product added to panel successfully');

        return $this->redirectToRoute('app_home');
    }

    #[Route('/panel', name: 'app_panel_view')] 
    public function viewPanel(EntityManagerInterface $entityManager): Response { 
        $total = 0;
        $sumPrices=0;
        $user = $this->getUser(); 
        $panels = $entityManager->getRepository(Panel::class)->findBy(['user' => $user]); 
        $total = count($panels);
        foreach ($panels as $panel)
            $sumPrices+=$panel->getProduct()->getPrice();

        return $this->render('home/viewPanel.html.twig', [ 
            'panels' => $panels, 
            'countPanels' => $total,
            'sumPrices' => $sumPrices,
        ]); 
    }

    #[Route('/panel/total',name: 'app_panel_total_view')]
    public function getTotal(EntityManagerInterface $entityManager): Response {
        $total = 0;
        if ($user = $this->getUser()){
            $panels = $entityManager->getRepository(Panel::class)->findby(['user'=> $user]);
            $total = count($panels);
        }
        return $this->json(['total'=>$total]);
    }

    #[Route('panel/{id}/delete', name: 'app_panel_delete_home', methods: ['POST'])]
    public function delete(Request $request, Panel $panel, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$panel->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($panel);
            $entityManager->flush();
            $this->addFlash('success', 'Product deleted from panel successfully');
        }

        return $this->redirectToRoute('app_panel_view', [], Response::HTTP_SEE_OTHER);
    }
}
