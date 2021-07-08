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

class HomeController extends AbstractController
{
    private $locationrepo;
    private $prestationsrepo;
  
    function __construct(LocationRepository $locationRepository, PrestationsRepository $prestationsRepository)
    {
        $this->locationrepo = $locationRepository;
        $this->prestationsrepo = $prestationsRepository;
    }
    /**
     * @Route("/home", name="home")
     */
    public function index()
    {
        $prestations = $this->prestationsrepo->findAll();
     
        $caravanes = [];
        $m_h = [];
        $emplacements = [];
        foreach($prestations as $value)
        {
            if(preg_match_all('/^Caravane/', $value->getLabel()))
                array_push($caravanes,$value->getLabel());
            if(preg_match_all('/^M-H/', $value->getLabel()))
                array_push($m_h,$value->getLabel());
            if(preg_match_all('/^Emplacement/', $value->getLabel()))
                array_push($emplacements,$value->getLabel());
        }
       
        return $this->render('home/home.html.twig', [
            'caravanes' =>  $caravanes,
            'MHS' =>  $m_h,
            'terrains' =>  $emplacements
        ]);
    }


    /**
     * @Route("/confirm", name="confirm-commande")
     */
    public function confirmCommande(): Response
    {
        function definirSaison(string $date):int
        {
            $bob =  date('Y', strtotime($_POST['startAt'])).'-'.$date;
            return strftime('%j',strtotime($bob));
        }
        define('HAUTE_SAISON_DEBUT', definirSaison('06-19'));// Definir la date de debut avec -2 jours
        define('HAUTE_SAISON_FIN', definirSaison('08-31'));// 08-31
    
        $prestations = $this->prestationsrepo->findAll();
        $type = $_POST['type'];

        $prices = [];
        $idType = '';
        foreach($prestations as $prestation) 
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

        // $locations = $this->locationrepo->findByType($idType);
    //     foreach ($locations as $value) 
    //     {
//            $bob = $value->getCommandes();
//              dump($bob);
//              foreach ($bob as $value) 
    //        {
    //            dump($value->getJournees()[0]);
    //        }
           
    //    }
        $tab = [];
        $dayStart = strtotime($_POST['startAt']);
        $manager = $this->getDoctrine()->getManager();
        // $commande = new Commande();
        // $commande->setDateStart($_POST['startAt'])
        //          ->setLocation();

        for($i = 0; $i < $_POST['days']; $i++)
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
            array_push($tab, $journee);
            // $manager->persist($journee);
        }
        // $manager->flush();

      
        $prices['majoration'] = 0.15;
        
        return $this->render('home/confirmation.html.twig', [
            'journees' => $tab,
            'startAt' => $dayStart,
            'type' => $type,
            'prestations' => $prestations,
            'prices' => $prices
        ]);
    }


    /**
     * @Route("/location/{name}", name="location")
     */
    public function locations(Request $request, string $name)
    {
        $days = $request->request->get('end-at'); 
        $days = '';
        $prestations = $this->prestationsrepo->findAll();
        $idType = '';
        foreach($prestations as $prestation) 
        { 
            if($prestation->getLabel() == $_POST['type']) 
                $idType = $prestation->getId();
        }
     
        if(isset($_POST['endAt']) )
            $days = (strftime('%j', strtotime($_POST['endAt'])) -  (strftime('%j', strtotime($_POST['startAt']))));
        
        $locations = $this->locationrepo->findByType($idType);
        $reserved = false;
        foreach ($locations as $value) 
        {
            $commandes = $value->getCommandes();
            foreach ($commandes as $commande) 
            {
                dump($commande->getDateStart());
                $nombreJournees = count($commande->getJournees());
                $dateDebutCommandeExistantes = strftime('%j', $commande->getDateStart()->getTimestamp() );
                
                for ($i=0; $i < $nombreJournees; $i++) 
                { 
                    if ($dateDebutCommandeExistantes + $i == strftime('%j', strtotime($_POST['startAt'] )) + $i){
                        $reserved = true;
                    }
                }
                if(!$reserved)
                {
                    // $error = 'excusez nous il y a pas de places';
                    $form = $this->createForm(CommandeJourneeType::class);
            
                    return $this->render('home/part/_location.html.twig', [
                        'form' => $form->createView(),
                        'name' => $name,
                        'days' => $days,
                        'startAt' => $_POST['startAt'],
                        'type' => $_POST['type'],
                    ]);
                }
                return $this->render('home/error');
            }
        }

    }
}