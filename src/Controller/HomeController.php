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
        $tab = [];
        define('HAUTE_SAISON_DEBUT', 170);// 21/06
        define('HAUTE_SAISON_FIN', 243);// 31/08
        dump($_POST);

        $dayStart = strtotime($_POST['startAt']);
        for($i = 0; $i < $_POST['days']; $i++)
        {
            $journee = new Journee();
            $journee->setAdultes($_POST["commande_journee"]['nombres-adultes'])
                    ->setEnfants($_POST["commande_journee"]['nombres-enfants']);
            
            if( (strftime('%j',$dayStart ) + $i) > HAUTE_SAISON_DEBUT 
                && (strftime('%j', $dayStart)+ $i) < HAUTE_SAISON_FIN) 
                $journee->setMajoration(true);
            else
                $journee->setMajoration(false);
            array_push($tab, $journee);
        }
        return $this->render('home/confirmation.html.twig', [
            'journees' => $tab,
            'startAt' => $dayStart,
            'type' => $_POST['type']
        ]);
    }


    /**
     * @Route("/location/{name}", name="location")
     */
    public function locations(Request $request, string $name)
    {
        $days = $request->request->get('end-at'); 
        $days = '';
     
        if(isset($_POST['endAt']) )
            $days = (strftime('%j', strtotime($_POST['endAt'])) -  (strftime('%j', strtotime($_POST['startAt']))));

        $form = $this->createForm(CommandeJourneeType::class);

        return $this->render('home/part/_location.html.twig', [
            'form' => $form->createView(),
            'name' => $name,
            'days' => $days,
            'startAt' => $_POST['startAt'],
            'type' => $_POST['type']
        ]);
    }

  
}