<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\CustomerNotes;
use App\Entity\Inquiry;
use App\Entity\Invoice;
use App\Entity\Offer;
use App\Entity\OfferCategory;
use App\Entity\OfferOption;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use App\Form\CustomerPassType;
use App\Form\InquiryCustomerType;
use App\Form\InquiryType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
/**
 * Class IndexController.
 */
#[Route(['de' => '/gato', 'en' => '/customer-portal'])]
class CatController extends BaseController
{
    #[Route('/lui', name: 'get_cat_start', methods: ['GET'])]
    public function getCat(): Response
    {
        // never called (security.yaml function)
        // and render a template with the button
        return $this->render('cat/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[Route(['de' => '/kontakt', 'en' => '/contact'], name: 'public_contact', methods: ['GET'])]
    public function contact(): Response
    {
        return $this->json('ok');
    }

    #[Route(path: ['de' => '/cat/pdf', 'en' => '/invoice/{id}/pdf'], name: 'public_pdf_invoice', methods: ['GET'])]
    public function catPdf(Invoice $invoice): Response
    {
        return new Response(
            $this->getInvoicePdf($invoice->getInvoiceOrder(), $invoice),
            \Symfony\Component\HttpFoundation\Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                // 'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
            ]
        );
    }

    #[Route('/addNotes', name: 'ajax_public_add_notes', methods: ['GET', 'POST'])]
    public function addCustomerNote(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $post = json_decode($request->getContent());
        if ($this->isGranted('ROLE_MONTAGE') and !empty($post->customer)) {
            $user = $entityManager->getRepository(Customer::class)->find($post->customer);
        }
        $note = new CustomerNotes();
        $date = new \DateTimeImmutable();
        $note->setCreatedAt($date);
        $note->setCustomer($user);
        $user->addCustomerNote($note);
        $note->setType('sos');
        $note->setNote(nl2br($post->note));
        if ($this->isGranted('ROLE_EMPLOYEE_SERVICE') and !empty($post->customer)) {
            $note->setUser($this->getUser());
            foreach ($user->getCustomerNotes() as $notes) {
                $notes->setAnsweredAt($date);
                $entityManager->persist($notes);
            }
            $note->setAnsweredAt($date);
        }
        $entityManager->persist($user);
        $entityManager->persist($note);
        $entityManager->flush();
        $entityManager->refresh($user);

        return $this->render('public/components/_notes.html.twig', [
            'user' => $user,
        ]);
    }

    private function getInvoicePdf(Order $order, Invoice $invoice): string
    {
        $binary = $_ENV['WKHTMLTOPDF_PATH'];
        $knp = new Pdf($binary);

        $html = $this->renderView('app/invoice/pdf/invoice.html.twig', [
            'offer' => $order->getOffer(),
            'invoice' => $invoice,
            'order' => $order,
            'text' => $invoice->getText(),
            'type' => $invoice->getType(),
        ]);

        return $knp->getOutputFromHtml($html, [
                'disable-smart-shrinking' => true,
                // 'orientation'=>'Landscape',
                'default-header' => false,
                'margin-top' => '0',
                'margin-left' => '0',
                'margin-bottom' => '0',
                'margin-right' => '0',
            ]
        );
    }
}
