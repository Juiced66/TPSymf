<?php
namespace App\Controller;

use App\Entity\Livre;
use App\Entity\Article;
use App\Entity\Journee;
use App\Entity\Commande;
use App\Form\CommandeType;
use App\Form\DateStartType;
use App\Form\CommandeJourneeType;
use App\Repository\LocationRepository;
use App\Repository\PrestationsRepository;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\IsNull;

class HomeController extends AbstractController
{
    private $locationrepo;
    private $prestationsrepo;
  
    function __construct(LocationRepository $locationRepository, PrestationsRepository $prestationsRepository)
    {
        $this->locationrepo = $locationRepository;
        $this->prestationsrepo = $prestationsRepository;
    }

    public function creerPrestationListe(array $prestations,  $type = '')
    {
        $caravanes = [];
        $m_h = [];
        $emplacements = [];
        $prestationsListes = [];
        
        foreach($prestations as $value) //parse des éléments de la table prestations
        {
            if(preg_match_all('/^Caravane/', $value->getLabel()))
            array_push($caravanes,$value->getLabel());
            if(preg_match_all('/^M-H/', $value->getLabel()))
            array_push($m_h,$value->getLabel());
            if(preg_match_all('/^Emplacement/', $value->getLabel()))
            array_push($emplacements,$value->getLabel());
        }
        // array_push($prestationsListes, $caravanes, $m_h, $emplacements);
        
        if($type != '')
        {
            if(preg_match('/^Caravane/', $type) != false)
                return $caravanes;
            else if(preg_match('/^M-H/', $type) != false)
                return $m_h;
            
            return $emplacements;               
        }

        $prestationsListes['caravanes'] = $caravanes;
        $prestationsListes['m-h'] = $m_h;
        $prestationsListes['emplacements'] = $emplacements;
        
        return $prestationsListes;
    }
    /**
     * @Route("/home", name="home")
     */
    public function index()
    {
        $prestations = $this->prestationsrepo->findAll();
     
         $prestationsListes = $this->creerPrestationListe($prestations);
        
        return $this->render('home/home.html.twig', [
            'caravanes' =>  $prestationsListes['caravanes'],
            'MHS' =>  $prestationsListes['m-h'],
            'terrains' =>  $prestationsListes['emplacements']
        ]);
    }


    /**
     * @Route("/confirm", name="confirm-commande")
     */
    public function confirmCommande(): Response
    {
        function definirSaison(string $date):int //gestion de la date : ajout de l'année
        {
            $bob =  date('Y', strtotime($_POST['startAt'])).'-'.$date; //strtotime => timestamp
            return strftime('%j',strtotime($bob)); //strftime && %j  => jours de l'année (1 a 366)
        }
        define('HAUTE_SAISON_DEBUT', definirSaison('06-19'));// Definir la date de debut avec -2 jours
        define('HAUTE_SAISON_FIN', definirSaison('08-31'));// 08-31
    
        $prestations = $this->prestationsrepo->findAll();
        $type = $_POST['type'];

        $prices = [];
        $idType = '';
        foreach($prestations as $prestation) //traitement des informations a donner a la vue 
                                            //pour construire la facturation par journées (en fonction du type de logement)
        { 
            if($prestation->getLabel() == $type) 
            {
                $prices['type'] = $prestation->getPrice();
            }
                
            if($prestation->getLabel() == 'taxe de séjour enfant') 
                $prices['taxEnfant'] = $prestation->getPrice();

            if($prestation->getLabel() == 'taxe de séjour Adulte') 
                $prices['taxAdulte'] = $prestation->getPrice();

            if($prestation->getLabel() == 'Piscine adulte') 
                $prices['taxPiscineAdulte'] = $prestation->getPrice();

            if($prestation->getLabel() == 'Piscine enfant') 
                $prices['taxPiscineEnfant'] = $prestation->getPrice();
        }

        $journees = [];
        $dayStart = strtotime($_POST['startAt']);
        $manager = $this->getDoctrine()->getManager();
        // $commande = new Commande();
        // $commande->setDateStart($_POST['startAt'])
        //          ->setLocation();

        for($i = 0; $i < $_POST['days']; $i++) //génération des journées a confirmer
        {
            $journee = new Journee();
            $journee->setAdultes($_POST["commande_journee"]['nombres-adultes'])
                    ->setEnfants($_POST["commande_journee"]['nombres-enfants']);
            
            if( (strftime('%j',$dayStart ) + $i) > HAUTE_SAISON_DEBUT 
            && (strftime('%j', $dayStart)+ $i) < HAUTE_SAISON_FIN) 
            {
                $journee->setMajoration(true);
            }
            else
                $journee->setMajoration(false);  
            array_push($journees, $journee);
            // $manager->persist($journee);
        }
        // $manager->flush();

      
        $prices['majoration'] = 0.15;
        
        return $this->render('home/confirmation.html.twig', [
            'journees' => $journees,
            'startAt' => $dayStart,
            'type' => $type,
            'prestations' => $prestations,
            'prices' => $prices
        ]);
    }


    /**
     * @Route("/location/{name}", name="location")
     */
    public function locations(Request $request, string $name) //Etape intermédiare (après le filtre des dates et avant la facturation)
    {
        // $days = $request->request->get('end-at'); 
        $days = '';
        $prestations = $this->prestationsrepo->findAll();
        $idType = '';
        $form = $this->createForm(CommandeJourneeType::class);
        foreach($prestations as $prestation) 
        { 
            if($prestation->getLabel() == $_POST['type']) 
                $idType = $prestation->getId();
        }
     
        // if(isset($_POST['endAt']) ) 
        $days = (strftime('%j', strtotime($_POST['endAt'])) -  (strftime('%j', strtotime($_POST['startAt'])))); //calcul du nombre de jours
        
        $locations = $this->locationrepo->findByType($idType);
        foreach ($locations as $value) //Pour toutes les locations :
        {
            $reserved = false; //flag de continuation
            $commandes = $value->getCommandes(); // On récupère les commandes et pour toutes ces commandes :
            foreach ($commandes as $commande) 
            {
                // dump($commande->getDateStart());
                $nombreJournees = count($commande->getJournees());// On récupère leur durée 
                $dateDebutCommandeExistantes = strftime('%j', $commande->getDateStart()->getTimestamp() ); //On recupère la date de début (getTimestamp() est une methode de Datetime)
             
                for ($i=0; $i < $nombreJournees; $i++) //Pour chaque journée de la commande 
                { 
                    if ($dateDebutCommandeExistantes + $i >= strftime('%j', strtotime($_POST['startAt'] ))&& 
                        $dateDebutCommandeExistantes + $i <= strftime('%j', strtotime($_POST['endAt'])))
                    { 
                        //On teste si notre jour est pris
                        $reserved = true; //on le signale
                    }
                }
                
                
                if(!$reserved) //Si c'est pas reservé
                {
                    return $this->render('home/part/_location.html.twig', [
                        'form' => $form->createView(),
                        'name' => $name,
                        'days' => $days,
                        'startAt' => $_POST['startAt'],
                        'type' => $_POST['type'],
                        'error' => null
                    ]);
                } 
            }//Sinon location suivante
        } //On a rien trouvé désolé
        // $form = $this->createForm(CommandeJourneeType::class);

        $prestationsListe = $this->creerPrestationListe($prestations, $_POST['type']);

        return $this->render('home/part/_location.html.twig', [
            'form' => $form->createView(),
            'name' => $name,
            'days' => $days,
            'startAt' => $_POST['startAt'],
            'type' => $_POST['type'],
            'prestations' => $prestationsListe,
            'error' => 'error'
        ]);

    }
}