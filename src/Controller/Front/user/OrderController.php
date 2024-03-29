<?php

namespace App\Controller\Front\user;

use App\Entity\CompanyPainting;
use App\Entity\CompanyTheme;
use App\Entity\Painting;
use App\Entity\Preparation;
use App\Entity\Theme;
use App\Repository\AddressOrderRepository;
use App\Entity\Address;
use App\Entity\AddressOrder;
use App\Entity\Burial;
use App\Entity\Company;
use App\Entity\Corpse;
use App\Entity\Model;
use App\Entity\ModelExtra;
use App\Entity\ModelMaterial;
use App\Entity\Order;
use App\Form\AddressType;
use App\Form\CorpseType;
use App\Repository\CorpseRepository;
use App\Repository\ModelRepository;
use App\Repository\OrderRepository;
use App\Repository\PreparationRepository;
use App\Repository\ThemeRepository;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use App\Security\Voter\CorpseVoter;
use App\Security\Voter\OrderVoter;
use App\Security\Voter\PreparationVoter;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Stripe\StripeClient;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("ROLE_USER")]
#[Route("/client")] // TODO : Peut-être faire /company/{nomDeLaCompagnie} dans le futur
class OrderController extends AbstractController
{

    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/commande', name: 'user_order', methods: ['GET'])]
    public function dashboard(OrderRepository $orderRepository): Response
    {

        $ordersInProgress = $orderRepository->findAllOwnedOrderInProgress();
        $ordersClose = $orderRepository->findAllOwnedOrderClosed();
        $orderDraft = $orderRepository->findOneOwnedOrderByStatus(Order::DRAFT);
        $ordersRefused = $orderRepository->findAllOwnedOrderByStatus(Order::DRIVER_PROCESSING_REFUSED);

        // Get address form order draft
        if ($orderDraft !== null) {
            $addressOrders = array_filter($orderDraft->getAddressOrders()->toArray(), function ($i) {
                return $i->getStatus() === AddressOrder::DECLARATION_CORPSES;
            });
            $orderDraft->address = null;
            if (!empty($addressOrders)) $orderDraft->address = $addressOrders[0]->getAddress();
        }

        foreach ($ordersInProgress as $order) {

            $addressOrders = array_filter($order->getAddressOrders()->toArray(), function ($i) {
                return $i->getStatus() === AddressOrder::DECLARATION_CORPSES;
            });
            $order->address = null;
            if (!empty($addressOrders)) $order->address = $addressOrders[0]->getAddress();
        }

        foreach ($ordersClose as $order) {

            $addressOrders = array_filter($order->getAddressOrders()->toArray(), function ($i) {
                return $i->getStatus() === AddressOrder::DECLARATION_CORPSES;
            });
            $order->address = null;
            if (!empty($addressOrders)) $order->address = $addressOrders[0]->getAddress();
        }

        foreach ($ordersRefused as $order) {

            $addressOrders = array_filter($order->getAddressOrders()->toArray(), function ($i) {
                return $i->getStatus() === AddressOrder::DECLARATION_CORPSES;
            });
            $order->address = null;
            if (!empty($addressOrders)) $order->address = $addressOrders[0]->getAddress();
        }

        return $this->render('front/user/myCommand/index.html.twig', [
            'orderDraft' => $orderDraft,
            'orderInProgress' => $ordersInProgress,
            'orderClose' => $ordersClose,
            'ordersRefused' => $ordersRefused
        ]);
    }

    /*   #[Route('/commande/{id}', name: 'user_order_id', methods: ['GET'])]
       public function show(Order $order, int $id, PreparationRepository $preparationRepository): Response
       {

           if ($order->getPossessor() != $this->getUser()) {
               throw $this->createNotFoundException(
                   'Aucune commande pour l\'id: ' . $id . ' vous appartient'
               );
           }

           $corpses = $order->getCorpses();
           foreach ($corpses as $corpse) {
               $corpse->setPreparation($preparationRepository->findOneBy(['corpse' => $corpse]));
           }

           return $this->render(
               'front/user/order.html.twig', [
                   'order' => $order,
                   'corpses' => $corpses
               ]
           );

       }*/

    /* DECLARE CORPSE */

    #[Route('/declarer-corps', name: 'declare_corpse', methods: ['POST', 'GET'])]
    public function declareCorpses(Request $request, ManagerRegistry $doctrine, OrderRepository $orderRep, CorpseRepository $corpseRep, AddressOrderRepository $addressOrderRep,): Response
    {
        $em = $doctrine->getManager();

        $corpseId = $request->request->getInt('corpseId');
        $corpse = $corpseRep->find($corpseId) ?? new Corpse();
        $order = $orderRep->findOneOwnedOrderByStatus(Order::DRAFT);

        $nextCorpse = false;

        if ($corpse->getId()) {

            $this->denyAccessUnlessGranted(CorpseVoter::EDIT, $corpse);

            $corpseIsOwned = $order->getId() === $corpse->getCommand()->getId();
            if (!$corpseIsOwned) throw $this->createAccessDeniedException();
        }

        // create form
        $form = $this->createForm(CorpseType::class, $corpse);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            if ($form->isValid()) {
                // check data
                $corpse = $form->getData();

                if ($corpse->checkDateConsistency() && $corpse->isBirthdateValid()) {

                    if (!$order) {
                        // create order
                        $order = new Order();
                        $order->setIsValid(false);
                        $order->setPossessor($this->getUser());
                        //TODO Check better number random string
                        $order->setNumber(Carbon::now()->getPreciseTimestamp(-2));
                        $order->setStatus(Order::DRAFT);
                        $order->setTypes(Order::DRIVER);

                        $corpse->setPosition(0);
                    } else {
                        // get corpses
                        $corpses = $corpseRep->findBy(
                            ['command' => $order->getId()],
                            ['position' => 'ASC']
                        );

                        if (!$corpse->getId()) $corpse->setPosition($corpses[count($corpses) - 1]->getPosition() + 1);
                    }
                    $corpse->setCommand($order);
                    $order->addCorpse($corpse);

                    // persist
                    $em->persist($corpse);
                    $em->persist($order);
                    $em->flush();

                    $order = $orderRep->find($order->getId());

                    if ($request->request->get('draftDeclaration')) {
                        $this->addFlash('success', 'Déclaration de corps bien enregistrée en tant que brouillon');
                        return $this->redirectToRoute('user_order');

                    } else {

                        $corpses = $corpseRep->findBy(
                            ['command' => $order->getId()],
                            ['position' => 'ASC']
                        );

                        $indexCurrentCorpse = array_filter($corpses, fn($otherCorpse) => $otherCorpse->getPosition() === $corpse->getPosition()) ?? null;
                        $indexNextCorpse = is_array($indexCurrentCorpse) && !empty($indexCurrentCorpse) ? array_key_first($indexCurrentCorpse) + 1 : -1;

                        if ($indexNextCorpse) {
                            $corpse = $corpses[$indexNextCorpse] ?? new Corpse();
                            $nextCorpse = isset($corpses[$indexNextCorpse++]);
                        }
                        $form = $this->createForm(CorpseType::class, $corpse);
                        $this->addFlash('success', 'Corps bien ajouté');
                    }
                } else {
                    $this->addFlash('failed', 'Les dates ne sont pas coherent');
                }
            }

        } else {
            if ($order) {
                $corpses = $corpseRep->findBy(
                    ['command' => $order->getId()],
                    ['position' => 'ASC']
                );
                $corpse = $corpses[0];
                $form = $this->createForm(CorpseType::class, $corpse);
                $nextCorpse = isset($corpses[1]);
            }
        }

        $priceOrder = 0.00;
        $priceOrderTVA = 0.00;
        $TVA = 20 / 100;
        $address = null;

        if ($order !== null) {
            $priceOrder = count($order->getCorpses()->toArray()) * 15.00;
            $addressOrder = $addressOrderRep->findOneOwnedByStatusAndOrder(AddressOrder::DECLARATION_CORPSES, Order::DRAFT, $order);
            if ($addressOrder instanceof AddressOrder) $address = $addressOrder->getAddress();
            $priceOrderTVA = $priceOrder * (1 + $TVA);
        }


        return $this->renderForm('front/user/declareCorpse/index.html.twig', [
            'form' => $form,
            'order' => $order,
            'corpse' => $corpse,
            'nextCorpse' => $nextCorpse,
            'priceOrderTVA' => $priceOrderTVA,
            'priceOrder' => $priceOrder,
            'address' => $address
        ]);
    }

    #[Route('/declarer-corps-adresse', name: 'declare_corpse_address', methods: ['POST', 'GET'])]
    public function declareCorpsesAddress(Request $request, ManagerRegistry $doctrine, OrderRepository $orderRep, AddressOrderRepository $addressOrderRep): Response
    {
        $order = $orderRep->findOneOwnedOrderByStatus(Order::DRAFT);
        $address = new Address();

        // get address if already exist
        $addressOrder = $addressOrderRep->findOneOwnedByStatusAndOrder(AddressOrder::DECLARATION_CORPSES, Order::DRAFT, $order);
        if ($addressOrder instanceof AddressOrder) $address = $addressOrder->getAddress();
        else $addressOrder = new AddressOrder();

        if (!($order instanceof Order)) return $this->redirectToRoute('declare_corpse');

        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $doctrine->getManager();
            $address = $form->getData();

            // insert address
            $addressOrder->setAddress($address);
            $addressOrder->setCommand($order);
            $addressOrder->setStatus(AddressOrder::DECLARATION_CORPSES);

            $em->persist($addressOrder);
            $em->persist($address);
            $em->flush();

            return $this->redirectToRoute('declare_corpse_confirmation');
        }

        $TVA = 20 / 100;

        $priceOrder = count($order->getCorpses()->toArray()) * 15.00;
        $priceOrderTVA = $priceOrder * (1 + $TVA);

        return $this->renderForm('front/user/declareCorpse/address.html.twig', [
            'form' => $form,
            'address' => $address,
            'order' => $order,
            'priceOrderTVA' => $priceOrderTVA,
            'priceOrder' => $priceOrder,

        ]);
    }

    #[Route('/declarer-corps-confirmation', name: 'declare_corpse_confirmation', methods: ['POST', 'GET'])]
    public function declareCorpsesConfirmation(
        Request                $request,
        ManagerRegistry        $doctrine,
        OrderRepository        $orderRep,
        AddressOrderRepository $addressOrderRep,
    ): Response
    {
        $order = $orderRep->findOneOwnedOrderByStatus(Order::DRAFT);
        $this->denyAccessUnlessGranted(OrderVoter::CONFIRM, $order);

        $addressOrder = $addressOrderRep->findOneOwnedByStatusAndOrder(AddressOrder::DECLARATION_CORPSES, Order::DRAFT, $order);

        $address = $addressOrder->getAddress();
        $em = $doctrine->getManager();
        $priceOrder = 0.00;

        foreach ($order->getCorpses() as $corpse) {
            $priceOrder += 15.00;
        }

        $TVA = 20 / 100;

        $priceOrderTVA = $priceOrder * (1 + $TVA);

        if ($request->query->getBoolean('confirm')
            || $request->query->getBoolean('cancel')) {

            if ($request->query->getBoolean('confirm')) {

                return $this->redirectToRoute('user_order_payment');
            } else if ($request->query->getBoolean('cancel')) {
                $order->setStatus(Order::DRIVER_USER_CANCEL_ORDER);
                $this->addFlash('error', "Votre déclaration de corps s'est bien annulée");
            } else {
                return $this->renderForm('front/user/declareCorpse/confirmation.html.twig', [
                    'order' => $order,
                    'address' => $address
                ]);
            }
            $em->persist($order);
            $em->flush();

            return $this->redirectToRoute('user_order');
        }

        return $this->renderForm('front/user/declareCorpse/confirmation.html.twig', [
            'order' => $order,
            'address' => $address,
            'price' => $priceOrder,
            'priceWithTva' => $priceOrderTVA
        ]);

        return $this->redirectToRoute('user_order');
    }

    #[Route('/supprimer-corps-declaration/{id}', name: 'delete_corpse_declaration')]
    public function delete_corpse_declaration(Corpse $corpse, OrderRepository $orderRep): Response
    {

        $order = $orderRep->findOneOwnedOrderByStatus(Order::DRAFT);

        if ($order != null && !empty($order->getCorpses()->toArray()) && count($order->getCorpses()->toArray()) > 1) {
            if (array_filter($order->getCorpses()->toArray(), fn($c) => $c->getId() == $corpse->getId())) {

                $em = $this->getDoctrine()->getManager();
                $em->remove($corpse);
                $em->flush();
            }
        }

        return $this->redirectToRoute('declare_corpse_confirmation');
    }

    #[Route('/modifier-corps-declaration/{id}', name: 'edit_corpse_declaration')]
    public function edit_corpse_declaration(Request $request, Corpse $corpse, OrderRepository $orderRep, AddressOrderRepository $addressOrderRep): Response
    {
        $order = $orderRep->findOneOwnedOrderByStatus(Order::DRAFT);

        if ($order != null && !empty($order->getCorpses()->toArray())) {
            if (array_filter($order->getCorpses()->toArray(), fn($c) => $c->getId() == $corpse->getId())) {

                $form = $this->createForm(CorpseType::class, $corpse);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($corpse);
                    $em->flush();

                    return $this->redirectToRoute('declare_corpse_confirmation');
                }

                $TVA = 20 / 100;
                $address = null;

                $priceOrder = count($order->getCorpses()->toArray()) * 15.00;
                $addressOrder = $addressOrderRep->findOneOwnedByStatusAndOrder(AddressOrder::DECLARATION_CORPSES, Order::DRAFT, $order);
                if ($addressOrder instanceof AddressOrder) $address = $addressOrder->getAddress();
                $priceOrderTVA = $priceOrder * (1 + $TVA);

                return $this->renderForm('front/user/declareCorpse/index.html.twig', [
                    'form' => $form,
                    'order' => $order,
                    'corpse' => $corpse,
                    'nextCorpse' => false,
                    'editVersion' => true,
                    'priceOrder' => $priceOrder,
                    'priceOrderTVA' => $priceOrderTVA,
                    'TVA' => $TVA,
                    'address' => $address
                ]);

            }
        }
        return $this->redirectToRoute('declare_corpse_confirmation');
    }

    /* ORDER SERVICE */

    #[Route('/commander-un-service/etape-1/{corpse}/{theme}', name: 'user_order_theme', methods: ['POST', 'GET'])]
    public function orderServiceTheme(ThemeRepository $themeRepository, Corpse $corpse, Theme $theme = null): Response
    {
        $this->denyAccessUnlessGranted(PreparationVoter::ORDER, $corpse);

        $preparation = $corpse->getPreparation() !== null ? $corpse->getPreparation() : new Preparation();

        if (!is_null($theme)) {
            if ($theme->getDeletedAt()) throw $this->createAccessDeniedException();

            $preparation->setTheme($theme);
            $preparation->setStatus(Preparation::FUNERAL_DRAFT);
            $preparation->setCorpse($corpse);
            $preparation->setCommand($corpse->getCommand());
            $corpse->setPreparation($preparation);

            $em = $this->getDoctrine()->getManager();
            $em->persist($preparation);
            $em->flush();

            if ($theme->getType() === Theme::TYPE_SPECIAL) {
                $preparation->setPainting(null);
                $preparation->setModelMaterial(null);
                $preparation->setModelExtra(null);
                $preparation->setPrice($theme->getPrice());

                $em->flush();
                return $this->redirectToRoute('user_order_recap', ['corpse' => $corpse->getId()]);
            }

            return $this->redirectToRoute('user_order_company', ['id' => $corpse->getId()]);
        }

        $themesSpecial = $themeRepository->findBy(['type' => Theme::TYPE_SPECIAL], ['name' => 'ASC']);
        $themesClassic = $themeRepository->findBy(['type' => Theme::TYPE_CLASSIC], ['name' => 'ASC']);

        return $this->render('front/user/orderService/index.html.twig', [
            'themesSpecial' => $themesSpecial,
            'themesClassic' => $themesClassic,
            'corpse' => $corpse,
        ]);
    }

    #[Route('/commander-un-service/etape-2/{id}', name: 'user_order_company', methods: ['POST', 'GET'])]
    public function orderServiceCompany(Request $request, ModelRepository $modelRepository, PaginatorInterface $paginator, Corpse $corpse): Response
    {
        $this->denyAccessUnlessGranted(PreparationVoter::ORDER, $corpse);
        $this->denyAccessUnlessGranted(PreparationVoter::ORDER_CLASSIC, $corpse);

        $companies = $modelRepository->getCompaniesThatHaveModelsAndFiltersByTheme($corpse->getPreparation()->getTheme(), true);

        $page = $request->query->getInt('page', 1) ? $request->query->getInt('page', 1) : 1;

        $pagination = $paginator->paginate(
            $companies,
            $page,
            10
        );

        return $this->render('front/user/orderService/companies.html.twig', [
            'companies' => $companies,
            'corpse' => $corpse,
            'pagination' => $pagination
        ]);
    }

    #[Route('/commander-un-service/etape-3/{corpse}/pompe-funebre/{company}', name: 'user_order_product', methods: ['POST', 'GET'])]
    public function orderServiceProduct(Request $request, ModelRepository $modelRepository, Corpse $corpse, Company $company): Response
    {

        $this->denyAccessUnlessGranted(PreparationVoter::ORDER, $corpse);
        $this->denyAccessUnlessGranted(PreparationVoter::ORDER_CLASSIC, $corpse);

        $hasTheme = array_filter($company->getCompanyThemes()->toArray(), fn(CompanyTheme $i) => $i->getTheme()->getId() == $corpse->getPreparation()->getTheme()->getId());

        if (empty($hasTheme)) throw $this->createAccessDeniedException();

        // get ressources funeral
        $data = $modelRepository->getCompleteProductsRelatedToCompany($company);
        $burials = array_filter($data, fn($i) => $i instanceof Burial);
        $models = array_filter($data, fn($i) => $i instanceof Model);
        $modelsExtra = array_filter($data, fn($i) => $i instanceof ModelExtra);
        $modelsMaterial = array_filter($data, fn($i) => $i instanceof ModelMaterial);
        $companiesPainting = array_filter($data, fn($i) => $i instanceof CompanyPainting);

        if ($request->request->getInt('burial') &&
            $request->request->getInt('model') &&
            $request->request->getInt('material') &&
            $request->request->getInt('color') &&
            $request->request->getInt('extra')
        ) {
            $em = $this->getDoctrine()->getManager();

            // get entites for order
            $burial = $em->getRepository(Burial::class)->find($request->request->get('burial'));
            $model = $em->getRepository(Model::class)->find($request->request->get('model'));
            $modelMaterial = $em->getRepository(ModelMaterial::class)->find($request->request->get('material'));
            $modelExtra = $em->getRepository(ModelExtra::class)->find($request->request->get('extra'));
            $color = $em->getRepository(Painting::class)->find($request->request->get('color'));

            // Verification if ressources consistent
            $entities = [$burial, $model, $modelMaterial, $modelExtra, $color];
            if (in_array(null, $entities) || in_array([], $entities)) throw $this->createAccessDeniedException();

            if (!($model->getBurial()->getId() == $burial->getId() &&
                $modelMaterial->getModel()->getId() == $model->getId() &&
                $modelExtra->getModel()->getId() == $model->getId() &&
                $em->getRepository(CompanyPainting::class)->findBY([
                    'company' => $model->getCompany()->getId(),
                    'painting' => $color

                ]))) throw $this->createAccessDeniedException();


            // set preparation
            $preparation = $corpse->getPreparation();
            $preparation->setModelExtra($modelExtra);
            $preparation->setModelMaterial($modelMaterial);
            $preparation->setPainting($color);

            // get total price
            $price = 0;
            $price += $color->getPrice();
            $price += $preparation->getTheme()->getPrice();
            $price += $modelMaterial->getMaterial()->getPrice();
            $price += $modelExtra->getExtra()->getPrice();
            $price += $model->getPrice();

            $preparation->setPrice($price);

            $em->flush();

            return $this->redirectToRoute('user_order_recap', ['corpse' => $corpse->getId()]);
        }

        return $this->render('front/user/orderService/company.html.twig', [
            'company' => $company,
            'burials' => $burials,
            'models' => $models,
            'modelsMaterial' => $modelsMaterial,
            'companiesPainting' => $companiesPainting,
            'modelsExtra' => $modelsExtra,
            'corpse' => $corpse
        ]);
    }

    #[Route('/commander-un-service/recapitulatif/{corpse}', name: 'user_order_recap', methods: ['POST', 'GET'])]
    public function orderServiceRecap(Request $request, Corpse $corpse): Response
    {
        $this->denyAccessUnlessGranted(PreparationVoter::ORDER, $corpse);
        $this->denyAccessUnlessGranted(PreparationVoter::CONFIRM_ORDER, $corpse);

        if ($request->query->getBoolean('confirm')) {

            return $this->redirectToRoute('user_order_corpse_payment', ['id' => $corpse->getId()]);

        }

        if ($request->query->getBoolean('cancel')) {
            $em = $this->getDoctrine()->getManager();
            $preparation = $corpse->getPreparation();

            $corpse->setPreparation(null);
            $em->remove($preparation);
            $em->persist($corpse);
            $em->flush();

            $this->addFlash('success', 'La commande a bien été annulée');
            return $this->redirectToRoute('user_order');
        }

        return $this->render('front/user/orderService/recap.html.twig', [
            'corpse' => $corpse,
            'preparation' => $corpse->getPreparation(),
            'company' => $corpse->getPreparation()?->getModelExtra()?->getModel()?->getCompany()
        ]);
    }

    /**
     * @throws ApiErrorException
     */
    #[Route('/commander-un-service/payement', name: 'user_order_payment', methods: ['POST', 'GET'])]
    public function orderServicePayement(EntityManagerInterface $em): Response
    {

        $order = $em->getRepository(Order::class)->findOneOwnedOrderByStatus(Order::DRAFT);
        $this->denyAccessUnlessGranted(OrderVoter::CONFIRM, $order);

        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $stripe = new StripeClient($_ENV['STRIPE_SECRET_KEY']);

        $stripe->products->create([
            'name' => 'Gold Special',
        ]);

        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'submit_type' => 'pay',
            'line_items' => [[
                # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
                'price' => 'price_1LH328GgCa17kbBH3P8ybu94',
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => 'http://localhost' . $this->generateUrl('user_order_success'),
            'cancel_url' => 'http://localhost' . $this->generateUrl('user_order_cancel'),
        ]);

        header("Location: " . $checkout_session->url);

        return $this->render('front/user/payment/payment.html.twig', [
            'checkout_session' => $checkout_session,
        ]);

    }

    #[Route('/commander-un-service/payement/confirmation', name: 'user_order_success', methods: ['POST', 'GET'])]
    public function orderServiceSuccess(EntityManagerInterface $em, OrderRepository $orderRep, UserRepository $userRep, AddressOrderRepository $addressOrderRep): Response
    {

        $order = $orderRep->findOneOwnedOrderByStatus(Order::DRAFT);
        $this->denyAccessUnlessGranted(OrderVoter::CONFIRM, $order);

        $em = $this->getDoctrine()->getManager();
        $order = $em->getRepository(Order::class)->findOneOwnedOrderByStatus(Order::DRAFT);
        $order->setStatus(Order::DRIVER_NEW);
        $em->persist($order);
        $em->flush();

        $user = $userRep->find($this->getUser());

        $addressOrder = $addressOrderRep->findOneOwnedByStatusAndOrder(AddressOrder::DECLARATION_CORPSES, Order::DRIVER_NEW, $order);

        $address = $addressOrder->getAddress();
        $priceOrder = 0.00;

        foreach ($order->getCorpses() as $corpse) {
            $priceOrder += 15.00;
        }

        $TVA = 20 / 100;

        $priceOrderTVA = $priceOrder * (1 + $TVA);

        // generate a signed url and email it to the user
        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new \Symfony\Component\Mime\Address('thanatos.super.mailer@gmail.com', 'Thanatos'))
                ->to("esedjicoraline@gmail.com")
                //TODO change email
//                ->to($user->getEmail())
                ->subject('Confirmation de votre déclaration de corps')
                ->context([
                    'order' => $order,
                    'address' => $address,
                    'priceWithTva' => $priceOrderTVA,
                    'price' => $priceOrder,
                ])
                ->htmlTemplate('front/confirmation_declare_corpse.html.twig')
        );


        return $this->render('front/user/payment/success.html.twig');
    }

    #[Route('/commander-un-service/payement/annulation', name: 'user_order_cancel', methods: ['POST', 'GET'])]
    public function orderServiceCancel(): Response
    {
        //TODO warning access page easily
        return $this->render('front/user/payment/cancel.html.twig');
    }

    /**
     * @throws ApiErrorException
     */
    #[Route('/commander-un-service-corps/payement/{id}', name: 'user_order_corpse_payment', methods: ['POST', 'GET'])]
    public function orderServicePayementCorps(EntityManagerInterface $em, int $id): Response
    {

        $corpse = $em->getRepository(Corpse::class)->find($id);
        if ($corpse == null) throw $this->createAccessDeniedException();
        $this->denyAccessUnlessGranted(PreparationVoter::ORDER, $corpse);
        $this->denyAccessUnlessGranted(PreparationVoter::CONFIRM_ORDER, $corpse);

        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $stripe = new StripeClient($_ENV['STRIPE_SECRET_KEY']);

        $stripe->products->create([
            'name' => 'Gold Special',
        ]);

        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'submit_type' => 'pay',
            'line_items' => [[
                # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
                'price' => 'price_1LH328GgCa17kbBH3P8ybu94',
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => 'http://localhost' . $this->generateUrl('user_order_corpse_success', ['id' => $id]),
            'cancel_url' => 'http://localhost' . $this->generateUrl('user_order_cancel'),
        ]);


        header("Location: " . $checkout_session->url);

        return $this->render('front/user/payment/payment.html.twig', [
            'checkout_session' => $checkout_session,
        ]);

    }

    #[Route('/commander-un-service-corps/payement/confirmation/{id}', name: 'user_order_corpse_success', methods: ['POST', 'GET'])]
    public function orderServiceCorpseSuccess(int $id, CorpseRepository $corpseRepository): Response
    {
        $corpse = $corpseRepository->find($id);
        if ($corpse == null) throw $this->createAccessDeniedException();
        $this->denyAccessUnlessGranted(PreparationVoter::ORDER, $corpse);
        $this->denyAccessUnlessGranted(PreparationVoter::CONFIRM_ORDER, $corpse);

        $em = $this->getDoctrine()->getManager();
        $preparation = $corpse->getPreparation();
        $preparation->setStatus(Preparation::FUNERAL_NEW);
        $em->flush();

        $this->addFlash('success', 'La commande a été envoyée à la pompe funèbre');

        return $this->render('front/user/payment/success.html.twig');
    }


}
