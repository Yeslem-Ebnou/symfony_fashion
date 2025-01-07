<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/products')]
final class ProductController extends AbstractController
{
    #[Route(name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, #[Autowire('%photo_dir%')] string $photoDir): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->getData();
            if ($photo=$form['photo']->getData()) {
                $fileName = uniqid().'.'.$photo->guessExtension();
                $photo->move($photoDir, $fileName);
            }
            $product->setImage($fileName);
            
            if($product instanceof Product){
                $product->setCreatedAt(new \DateTimeImmutable());
                $product->setUpdatedAt(new \DateTimeImmutable());
            }
            
            $entityManager->persist($product);
            $entityManager->flush();
            $this->addFlash('success', 'Product added successfully');

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager, #[Autowire('%photo_dir%')] string $photoDir): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->getData();

            $fileName = null;

            if ($photo=$form['photo']->getData()) {
                $fileName = uniqid().'.'.$photo->guessExtension();
                $photo->move($photoDir, $fileName);

                $previousImage = $product->getImage();
                if ($previousImage && $previousImage !== $fileName){
                    $previousImagePath = $photoDir . '/' . $previousImage;
                    if (file_exists($previousImagePath)){
                        unlink($previousImagePath);
                    }
                }
            }
            if ($fileName)
                $product->setImage($fileName);

            if($product instanceof Product){
                $product->setUpdatedAt(new \DateTimeImmutable());
            }
            $entityManager->flush();
            
            $this->addFlash('success', 'Product edited successfully');

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager, #[Autowire('%photo_dir%')] string $photoDir): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->getPayload()->getString('_token'))) {
            $fileName = $product->getImage();
            // full path to the photo
            $filePath = $photoDir. '/' . $fileName;

            // check if the file exists and delete it
            if ($fileName && file_exists($filePath)){
                if (unlink($filePath))
                    $this->addFlash('success', 'Product photo deleted successfully');
                else
                    $this->addFlash('error', 'There was an error deleting the product photo');
            }
            // Remove the product from the database
            $entityManager->remove($product);
            $entityManager->flush();
            $this->addFlash('success', 'Product deleted successfully');
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
