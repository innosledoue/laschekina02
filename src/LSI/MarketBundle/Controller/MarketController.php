<?php
/**
 * Created by PhpStorm.
 * User: Sylvanus KONAN
 * Date: 16/07/2018
 * Time: 09:56
 */

namespace LSI\MarketBundle\Controller;


use LSI\MarketBundle\Form\Annonce2Type;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use LSI\MarketBundle\Entity\Annonce;
use LSI\MarketBundle\Entity\Categorie;
use LSI\MarketBundle\Entity\Reserver;
use LSI\MarketBundle\Entity\Image;
use LSI\MarketBundle\Form\AnnonceType;
use LSI\MarketBundle\Repository\CategorieRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use LSI\MarketBundle\Form\ReserverType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Validator\Constraints\Regex;


class MarketController extends Controller {

    public function indexAction(Request $request)
    {

        // Methode de traitement pour la recherche par critere
        $formaff = $this->createFormBuilder()
            ->add('Categorie', EntityType::class, array(
                'class' => Categorie::class,
                'query_builder' => function(CategorieRepository $cr){
                    return $cr->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },
                'choice_label' => 'name',
                'required' => false,
                'placeholder' => 'Choisir une categorie',
            ))
            ->add('Ville', TextType::class,
                array(
                    'required' => false,
                    'constraints' => array(
                        new Regex( array(
                            'pattern' => '/^[a-z]*$/',
                            'match' => true,
                            'message' => 'Informations invalides'
                        )))))
            ->add('Budget', TextType::class,
                array(
                    'required' => false,
                    'constraints' => array(
                        new Regex( array(
                            'pattern' => '/\d+(\.\d+)?/',
                            'message' => 'Informations invalides'
                        )))))
            ->add('Periode', DateType::class, array('required' => false))
            ->add('Prix', RangeType::class, array(
                'attr' => array(
                    'Min' => 1000,
                    'Max' => 10000,
                    'required' => false,
                )
            ))
            ->add('recherche', SubmitType::class)
            ->getForm();

        $repository = $this->getDoctrine()->getRepository(Annonce::class);

        $formaff->handleRequest($request);
        $data = $formaff->getData();
        if ($formaff->isSubmitted() && $formaff->isValid()){
            if(!is_null($data['Categorie'])){
                $listeannonce = $repository->findCategorie($data['Categorie']);
                return $this->render('LSIMarketBundle:market:formbarre.html.twig', [
                    'formaff' => $formaff->createView(),
                    'listeannonce' =>$listeannonce]);
            }elseif (!is_null($data['Budget'])){
                $listeannonce = $repository->findBudget($data['Budget']);
                return $this->render('LSIMarketBundle:market:formbarre.html.twig', [
                    'formaff' => $formaff->createView(),
                    'listeannonce' =>$listeannonce]);
            }elseif (!is_null($data['Periode'])) {
                $listeannonce = $repository->findPeriode($data['Periode']);
                return $this->render('LSIMarketBundle:market:formbarre.html.twig', [
                    'formaff' => $formaff->createView(),
                    'listeannonce' =>$listeannonce]);
            }elseif (!is_null($data['Ville'])) {
                $listeannonce = $repository->findPeriode($data['Ville']);
                return $this->render('LSIMarketBundle:market:formbarre.html.twig', [
                    'formaff' => $formaff->createView(),
                    'listeannonce' =>$listeannonce]);
            }elseif(!is_null($data['Ville'] && !is_null($data['Categorie'] && !is_null($data['Budget']) && $data['Periode']))){
                $listeannonce = $repository->findVilleAndPeriodAndCategAndDate($data['Ville'],$data['Categorie'], $data['Budget'],$data['Periode']);
                return $this->render('LSIMarketBundle:market:formbarre.html.twig', [
                    'formaff' => $formaff->createView(),
                    'listeannonce' =>$listeannonce]);
            }else
//                Message en cours
                ;

        }

        return $this->render('LSIMarketBundle:market:index.html.twig', ['formaff' => $formaff->createView()]);

        // return $this->render('LSIMarketBundle:market:index.html.twig');

    }

    public function listeReservationAction(){
        $em = $this->getDoctrine()->getManager();

        // Recuperer toutes les annonces en bd
        $annonces = $em->getRepository('LSIMarketBundle:Annonce')->findAll();


        return $this->render('LSIMarketBundle:market:reservations.html.twig', array('annonces' => $annonces));
    }

    public function ajouterAction(Request $request){
        // Restreindre l'acces uniquement a la mairie
        $this->denyAccessUnlessGranted('ROLE_MAIRIE', $this->redirectToRoute('fos_user_security_login'));
        $annonce = new Annonce();

        $form = $this->createForm(AnnonceType::class, $annonce);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()){
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser()->getMairie();
            $annonce->setMairie($user);

            $em->persist($annonce);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notif', 'Annonce ajoutée avec succès !');

            return $this->redirectToRoute('ls_imarket_ajouter_annonce2', array('id' => $annonce->getId()));
        }
        return $this->render('LSIMarketBundle:market:ajouter.html.twig', array('form' => $form->createView()));
    }

    public function voirAction($id){
        $em = $this->getDoctrine()->getManager();

        // Recuperer l'annonce cliquee
        $annonce = $em->getRepository('LSIMarketBundle:Annonce')->find($id);

        if(null === $annonce){
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }
        // Recuperer l'auteur de l'annonce...
        $auteur = $em->getRepository('LSI\MarketBundle\Entity\User')
                     ->findAuteurAnnonce($annonce->getMairie()->getId());


        return $this->render('LSIMarketBundle:market:voir.html.twig', array('annonce' => $annonce, 'auteur' => $auteur));
    }

    public function reserverAction(Request $request, $id){
        $this->denyAccessUnlessGranted(['ROLE_MAIRIE', 'ROLE_PART'], $this->redirectToRoute('fos_user_security_login'));
        $em = $this->getDoctrine()->getManager();
        $reserver = new Reserver();

        $form = $this->createForm(ReserverType::class, $reserver);

        $annonce = $em->getRepository('LSIMarketBundle:Annonce')->find($id);

        $auth = $em->getRepository('LSIMarketBundle:User')
            ->findAuteurAnnonce($annonce->getMairie()->getId());
        $authMail = '';
        foreach ($auth as $aut ){
            $authMail = $aut->getEmail();
        }

        //dump($authMail);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()){

            $reserver->setUser($this->getUser());
            $reserver->setAnnonce($annonce);

            $em->persist($reserver);
            $em->flush();

            $from = "%mailer_user%";
            $toCostumer = $this->getUser()->getEmail();
            $toSeller = $authMail;
            $subject = 'Nouvelle réservation';
            $bodyCost = 'Vous avez effectué une réservation sur l\'annonces : '. $annonce->getTitre();
            // Envoi de mail au client...
            //sendMail($from, $toCostumer, $subject, $bodyCost);

            $bodySell = 'Vous avez reçu une réservation sur votre annonce : '.$annonce->getTitre();
            // Envoi de mail à l'offreur...
            //sendMail($from, $toSeller, $subject, $bodySell);


            $request->getSession()->getFlashBag()->add('info', 'Reservation effectuee, un message a ete envoyer a l\'offreur');

            return $this->redirectToRoute('ls_imarket_reservation');
        }

        return $this->render('LSIMarketBundle:reservation:reserver.html.twig', array('form' => $form->createView()));

    }

    public function reservationAction(){
        $this->denyAccessUnlessGranted(['ROLE_MAIRIE', 'ROLE_PART'], $this->redirectToRoute('fos_user_security_login'));
        $em = $this->getDoctrine()->getManager();

        // Recuperer l'id du User connecte
        $userId = $this->getUser()->getId();

        // Recuperer les reservations actives en BD dont l'auteur est le User connecte
        $reservations = $em->getRepository('LSIMarketBundle:Reserver')->findByUser($userId);
        $titreAnn = [];

        if (null === $reservations){
            throw new NotFoundHttpException("Vous n'avez aucune reservation en attente !");
        }else{
            //Recuperer le titre de chaque annonce recuperee
            foreach ($reservations as $reservation) {
                $titreAnn = $em->getRepository('LSIMarketBundle:Annonce')
                    ->titreAnnonce($reservation->getAnnonce());

            }

            return $this->render('LSIMarketBundle:reservation:mesreservations.html.twig', array(
                'reservations' => $reservations,
                'titreAnnonce' => $titreAnn));
        }

        return $this->render('LSIMarketBundle:reservation:mesreservations.html.twig');
    }

    public function annonceReserverAction(){
        $this->denyAccessUnlessGranted('ROLE_MAIRIE', $this->redirectToRoute('fos_user_security_login'));
        $em = $this->getDoctrine()->getManager();

        $userId = $this->getUser()->getMairie()->getId();

        $annonceReservees = $em->getRepository('LSIMarketBundle:Reserver')->annonceReserver($userId);

        $titreAnn = [];

        if (null === $annonceReservees){

        }else{
            //Recuperer le titre de chaque annonce recuperee
            foreach ($annonceReservees as $annonceR) {
                $titreAnn = $em->getRepository('LSIMarketBundle:Annonce')
                    ->titreAnnonce($annonceR->getAnnonce());
            }

            return $this->render('LSIMarketBundle:mairie:annonce_reserver.html.twig', array(
                'annonceReservee' => $annonceReservees,
                'titre' => $titreAnn
            ));
        }

    }

    // Renvoie la liste des annonces crees par une mairie.
    /*public function monEspaceAction(){
        $this->denyAccessUnlessGranted(['ROLE_MAIRIE', 'ROLE_PART', 'ROLE_SUPER_ADMIN'], $this->redirectToRoute('fos_user_security_login'));
        $em = $this->getDoctrine()->getManager();

        // Recuperer le User connecte
        $userId = $this->getUser()->getMairie();

        // Recuperer les annonces en fonction du User connecte.
        $annonces = $em->getRepository('LSIMarketBundle:Annonce')->findMesAnnonces($userId);

        if (null === $annonces){
            echo 'Vous n\'avez créer aucune annonce !';
        }else{
            if ($this->getUser()->hasRole('ROLE_MAIRIE')){
                return $this->render('LSIMarketBundle:mairie:mesannonces.html.twig', array('annonce' => $annonces));
            }elseif ($this->getUser()->hasRole('ROLE_PART')){
                return $this->reservationAction();
            }
        }

        if ($this->getUser()->hasRole('ROLE_MAIRIE')){
            return $this->render('LSIMarketBundle:mairie:mesannonces.html.twig');
        }elseif ($this->getUser()->hasRole('ROLE_PART')){
            return $this->reservationAction();
        }elseif($this->getUser()->hasRole('ROLE_SUPER_ADMIN')){

        }
    }*/

    public function monEspaceAction(){
        return $this->render('LSIMarketBundle::monespace.html.twig');
    }

    public function modifierAction($id, Request $request) {
        $this->denyAccessUnlessGranted('ROLE_MAIRIE', $this->redirectToRoute('fos_user_security_login'));
        //connection à la BD
        $em = $this->getDoctrine()->getManager();

        //recupération de l'objet à modifier
        $annonce = $em->getRepository('LSIMarketBundle:Annonce')->find($id);

        if (null === $annonce)
        {
            throw new NotFoundHttpException("L'annonce dont le numéro est ".$id." n'existe pas.");
        }

        //création du formulaire de modification
        // image de l'iamge avant modif pas encore réglé
        $form = $this->createForm(AnnonceType::class, $annonce);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $annonce->setAnnonceUpdateAt(new \DateTime('now'));

            //Mise a jour de la BD
            $em->persist($annonce);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');

            //Redirection vers la page de consultation
            return $this->redirectToRoute('ls_imarket_voir_annonce', array('id' => $annonce->getId(),
                ));
        }

        //création de la vue
        return $this->render('LSIMarketBundle:market:modifier.html.twig', array('form' => $form->createView(),
            'images'=> $annonce->getImages()->getWebPath()));
    }

    public function dupliquerAction(Request $request,$id){

        $this->denyAccessUnlessGranted('ROLE_MAIRIE', $this->redirectToRoute('fos_user_security_login'));
        //création d'un objet pour le dupliquer l'annonce avec une new image
        $newannonce = new Annonce();
        $image = new Image();

        //connection à la BD et récupération de l'annonce à créer par duplicata
        $em = $this->getDoctrine()->getManager();


        $annonce = $em->getRepository('LSIMarketBundle:Annonce')->findById($id);
        //$id = $em->getRepository('LSIMarketBundle:Annonce')->findIdMax();

        if (null === $annonce)
        {
            throw new NotFoundHttpException("L'annonce dont le numéro est ".$id." n'existe pas.");
        }
        $img = '';
        //copie du contenu de l'ancienne annonce dans la nouvelle
        foreach ($annonce as $item){
            $newannonce->setTitre($item->getTitre());
            $newannonce->setDescription($item->getDescription());
            $newannonce->setRegleCond($item->getRegleCond());
            $newannonce->setPrixDefaut($item->getPrixDefaut());
            $newannonce->setStatut($item->getStatut());
            $img = $item->getImages();
            $newannonce->setImages(new Image());
            $newannonce->setCategorie($item->getCategorie());
        }
        //dump($newannonce); $newannonce = clone $annonce; dump($newannonce);
        // //création du formulaire
        $form = $this->createForm(AnnonceType::class, $newannonce);
        //dump($newannonce);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser()->getMairie();
            $newannonce->setMairie($user);
            $em->persist($newannonce);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Annonce crée avec succes.');

            return $this->redirectToRoute('ls_imarket_voir_annonce', array('id' => $newannonce->getId(),
                ));
        }

        return $this->render('LSIMarketBundle:Market:dupliquer.html.twig', array('form' => $form->createView(),
            'img' => $img->getWebPath()));
    }


    // traitement barre de recherche pour toutes les pages et affiner les resultats de la recherche
    public function recherchebarreAction(Request $request){
        // Formulaire de traitement pour la recheche affiner
        $formaff = $this->createFormBuilder()
            ->add('Categorie', EntityType::class, array(
                'class' => Categorie::class,
                'query_builder' => function(CategorieRepository $cr){
                    return $cr->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },
                'choice_label' => 'name',
                'required' => false,
                'placeholder' => 'Choisir une categorie',
            ))
            ->add('Ville', TextType::class, array('required' => false,
                'constraints' => array(
                    new Regex( array(
                        'pattern' => '/^[a-z]*$/',
                        'match' => true,
                        'message' => 'Informations invalides'
                    )))))
            ->add('Budget', TextType::class, array('required' => false,
                'constraints' => array(
                    new Regex( array(
                        'pattern' => '/\d+(\.\d+)?/',
                        'message' => 'Informations invalides'
                    )))))
            ->add('Periode', DateType::class, array('required' => false))
            ->add('Prix', RangeType::class, array(
                'attr' => array(
                    'Min' => 1000,
                    'Max' => 10000,
                    'required' => false,
                )
            ))
            ->add('recherche', SubmitType::class)
            ->getForm();

        $formaff->handleRequest($request);
        // Traitement pour la recherche simple
        $motcle = $request->get('recherche');
        $repository = $this->getDoctrine()->getRepository('LSIMarketBundle:Annonce');
        $listeannonce = $repository->findTitreDescp($motcle);

        return $this->render('LSIMarketBundle:market:formbarre.html.twig', [ 'formaff' => $formaff->createView(),
            'listeannonce' => $listeannonce]);
    }

    // Methode de traitement de recherche pour la page principale index.html.twig
    public function rechprinciplaAction(Request $request) {
    // Formulaire de traitement pour la recheche affiner
    $formaff = $this->createFormBuilder()
        ->add('Categorie', EntityType::class, array(
            'class' => Categorie::class,
            'query_builder' => function(CategorieRepository $cr){
                return $cr->createQueryBuilder('c')
                    ->orderBy('c.name', 'ASC');
            },
            'choice_label' => 'name',
            'required' => false,
            'placeholder' => 'Choisir une categorie',
        ))
        ->add('Ville', TextType::class, array('required' => false,
            'constraints' => array(
                new Regex( array(
                    'pattern' => '/^[a-z]$/',
                    'match' => true,
                    'message' => 'Informations invalides'
                )))))
        ->add('Budget', TextType::class, array('required' => false,
            'constraints' => array(
                new Regex( array(
                    'pattern' => '/\d+(\.\d+)?/',
                    'message' => 'Informations invalides'
                )))))
        ->add('Periode', DateType::class, array(
            'required' => false,
            'input' => 'single_widget',
            ))
        ->add('Prix', RangeType::class, array(
            'attr' => array(
                'Min' => 1000,
                'Max' => 10000,
                'required' => false,
            )
        ))
        ->add('recherche', SubmitType::class)
        ->getForm();

    $repository = $this->getDoctrine()->getRepository('LSIMarketBundle:Annonce');
    // Traitement pour la recherche simple
    $motcle = $request->get('recherche');
    // $listeannonce = $repository->findBy(array('titre' => $motcle));
    $listeannonce = $repository->findTitreDescp($motcle);
    if (is_null($listeannonce)) {
        return $this->redirectToRoute('ls_imarket_recherche_index');
    }
    return $this->render('LSIMarketBundle:market:formbarre.html.twig', ['listeannonce' => $listeannonce]);
}

	public function annonceCommandeeAction(){
        return $this->render('LSIMarketBundle:mairie:annonce_commandee.html.twig');
    }

    public function mesCommandesAction(){

        return $this->render('LSIMarketBundle:commande:mes_commandes.html.twig');
    }

    public function monEpciAction($cp){
        $em = $this->getDoctrine()->getManager();

        $codePostal = $em->getRepository('LSIMarketBundle:Epci')->findEpciCodePostal($cp);

        if (null !== $codePostal){
            $reponse = new Response(json_encode($codePostal));
            return $reponse;
        }
    }

    function sendMail($from, $to, $subject, $body){
            $mesg = \Swift_Message::newInstance()
                ->setFrom($from)
                ->setTo($to)
                ->setSubject($subject)
                ->setBody($body)
                ;
            $this->get('mailer')->send($mesg);
       }

    public function ajouter2Action($id, Request $request) {
        $this->denyAccessUnlessGranted('ROLE_MAIRIE', $this->redirectToRoute('fos_user_security_login'));
        //connection à la BD
        $em = $this->getDoctrine()->getManager();

        //recupération de l'objet
        $annonce = $em->getRepository('LSIMarketBundle:Annonce')->find($id);

        if (null === $annonce)
        {
            throw new NotFoundHttpException("L'annonce dont le numéro est ".$id." n'existe pas.");
        }

        //création du formulaire
        $form = $this->createForm(Annonce2Type::class, $annonce);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            //Insertion dans la BD
            $em->persist($annonce);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Votre Annonce a été crée et publiée.');

            //Redirection vers la page de consultation
            return $this->redirectToRoute('ls_imarket_voir_annonce', array('id' => $annonce->getId(),
            ));
        }
        //création de la vue
        return $this->render('LSIMarketBundle:market:ajouter2.html.twig', array('form' => $form->createView()));
    }

}