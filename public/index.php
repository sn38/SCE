<?php
define("PREFIXE","ced598r48ece84r8ece804");
define("SUFFIXE","dk58fe1z85f1ez56f1ez8f541zef8");
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';


$config = [

    'displayErrorDetails' => true,
    'db' => [
        'host' => 'localhost',
        'dbname' => 'SCE', //infotec //infos_por,
        'user' => 'root',
        'pass' => '',
    ]
];

//  session_start(); //demarrer une session

$app = new \Slim\App(["settings" => $config]);

$container = $app->getContainer(); //création du container

$container['db'] = function ($c) {  //déclaration container tableau associatif pour connexion
    $db = $c['settings']['db'];
    $pdo = new PDO('mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'], $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};


$container['view'] = function ($container) { //déclaration container tableau associatif pour la vue
    $view = new \Slim\Views\Twig('../views', [
        'cache' => false  
   ]);

   // Instantiate and add Slim specific extension                                   
   $router = $container->get('router');
   $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
   $view->addExtension(new \Slim\Views\TwigExtension($router, $uri)); 
   return $view;
};



$app->get('/', function (Request $request, Response $response, array $args) {

    
    $req = $this->db->prepare('SELECT watts FROM table_LTP');
    $req->execute();
    while ($posts = $req->fetch()) {
   
                    $LTP = $posts['watts'];     
    }

    $req = $this->db->prepare('SELECT watts FROM table_LTS1');
    $req->execute();
    while ($posts = $req->fetch()) {

                    $LTS1 = $posts['watts'];
        
    }

    $req = $this->db->prepare('SELECT watts FROM table_LTS2');
    $req->execute();
    while ($posts = $req->fetch()) {

                    $LTS2 = $posts['watts'];
               
    }

    $req = $this->db->prepare('SELECT watts FROM table_LTS3');
    $req->execute();
    while ($posts = $req->fetch()) {

                    $LTS3 = $posts['watts'];
           
    }
  
    $req = $this->db->prepare('SELECT watts FROM table_LTS4');
    $req->execute();
    while ($posts = $req->fetch()) {

                    $LTS4 = $posts['watts'];
  
    }

    $req = $this->db->prepare('SELECT watts FROM table_LTS5');
    $req->execute();
    while ($posts = $req->fetch()) {

                    $LTS5 = $posts['watts'];
                    
    }

    $req = $this->db->prepare('SELECT watts FROM table_LTS6');
    $req->execute();
    while ($posts = $req->fetch()) {

                    $LTS6 = $posts['watts'];
                    
    }
    

    return $this->view->render($response, 'index.html', ['LTP' => $LTP, 'LTS1' => $LTS1, 'LTS2' => $LTS2,
    'LTS3' => $LTS3, 'LTS4' => $LTS4, 'LTS5' => $LTS5, 'LTS6' => $LTS6]);   //permet de rendre la vue çàd index.html
       
   
       
 });
 
$app->get('/oui', function (Request $request, Response $response, array $args) {

    
$req = $this->db->prepare('SELECT * FROM table_reception');
$req->execute();

$arr = array();

while ($posts = $req->fetch()) {
    
    //if (preg_match("#\.#", $donnees['Path']))
        array_push (
            $arr, 
            [
                "name" => $posts['id'],
                "path" => $posts['nom'],
                "date" => $posts['watts'],
                "operation" => $posts['heure_mesuree']
            ]
        );
}
    
   

return $this->view->render($response, 'receptionOK.html', ['items' => $arr ]);   
});

$app->post('/reception', function (Request $request, Response $response, array $args) {

    $data = json_decode(file_get_contents('php://input'), true); //Recupération des données JSON
    /*Affectation des valeurs */
    $nom=$data['nom'];  
    $watts=$data['watts'];
    $heure=$data['heure'];
    /*Stockage des données */
    $req = $this->db->prepare("INSERT INTO table_reception (watts, nom, heure_mesuree) VALUES ('$watts','$nom','$heure')");
    $req->execute();

 
    
    return $this->view->render($response, 'reception.html');   
    });

$app->post('/receptionMoyenne', function (Request $request, Response $response, array $args) {

        $data = json_decode(file_get_contents('php://input'), true); //Recupération des données JSON
        /*Affectation des valeurs */
        $nom=$data['nom'];  
        $watts=$data['watts'];
        $heure=$data['heure'];

        /*Stockage des données */
        $req = $this->db->prepare("INSERT INTO table_reception (watts, nom, heure_mesuree) VALUES ('$watts','$nom','$heure')");
        $req->execute();


    
     
        
        return $this->view->render($response, 'reception.html');   
        });

$app->get('/journalier', function (Request $request, Response $response, array $args) {

    $req = $this->db->prepare('SELECT moyenne FROM table_moyenne_jour WHERE nom="LTP" UNION SELECT moyenne FROM table_moyenne_jour WHERE nom="LTP" LIMIT 12');
    $req->execute();
    while ($posts = $req->fetch()) {
   
                    $LTP = $posts['moyenne'];     
                    $LTPHEURE = $posts['moyenne'];     
    }

    $req = $this->db->prepare('SELECT watts FROM table_LTS1');
    $req->execute();
    while ($posts = $req->fetch()) {

                    $LTS1 = $posts['watts'];
        
    }

    $req = $this->db->prepare('SELECT watts FROM table_LTS2');
    $req->execute();
    while ($posts = $req->fetch()) {

                    $LTS2 = $posts['watts'];
               
    }

    $req = $this->db->prepare('SELECT watts FROM table_LTS3');
    $req->execute();
    while ($posts = $req->fetch()) {

                    $LTS3 = $posts['watts'];
           
    }
  
    $req = $this->db->prepare('SELECT watts FROM table_LTS4');
    $req->execute();
    while ($posts = $req->fetch()) {

                    $LTS4 = $posts['watts'];
  
    }

    $req = $this->db->prepare('SELECT watts FROM table_LTS5');
    $req->execute();
    while ($posts = $req->fetch()) {

                    $LTS5 = $posts['watts'];
                    
    }

    $req = $this->db->prepare('SELECT watts FROM table_LTS6');
    $req->execute();
    while ($posts = $req->fetch()) {

                    $LTS6 = $posts['watts'];
                    
    }
    

    return $this->view->render($response, 'journalier.html', ['LTP' => $LTP, 'LTS1' => $LTS1, 'LTS2' => $LTS2,
    'LTS3' => $LTS3, 'LTS4' => $LTS4, 'LTS5' => $LTS5, 'LTS6' => $LTS6]);   //permet de rendre la vue çàd index.html
       
 });

 $app->get('/total', function (Request $request, Response $response, array $args) {

    $tab = "Consommation de la semaine";
    //$tab = array("valeur 1", "valeur 2", "valeur 3");

    return $this->view->render($response, 'total.html', ['val' => $tab ]);   //permet de rendre la vue çàd index.html
       
 });

 $app->get('/hebdomadaire', function (Request $request, Response $response, array $args) {

    $tab = "Consommation de la semaine";
    //$tab = array("valeur 1", "valeur 2", "valeur 3");

    return $this->view->render($response, 'highcharts.html', ['val' => $tab ]);   //permet de rendre la vue çàd index.html
       
 });
 $app->get('/change', function (Request $request, Response $response, array $args) {

    return $this->view->render($response, 'change.html');
});
$app->get('/login', function (Request $request, Response $response, array $args) {

    return $this->view->render($response, 'login.html');
});
$app->get('/incorrect', function (Request $request, Response $response, array $args) {

    return $this->view->render($response, 'incorrect.html');
});
$app->get('/ok', function (Request $request, Response $response, array $args) {

    return $this->view->render($response, 'ok.html');
});
$app->get('/test', function (Request $request, Response $response, array $args) {

   


    $req = $this->db->prepare('SELECT moyenne FROM table_moyenne_jour WHERE nom="LTP" UNION SELECT moyenne FROM table_moyenne_jour WHERE nom="LTP" LIMIT 12');
    $req->execute();

    $LTP = array();
    
    while ($posts = $req->fetch()) {
        
        //if (preg_match("#\.#", $donnees['Path']))
            array_push (
                $LTP, 
                [
                    
                    "moyenne" => $posts['moyenne'],
                    
                ]
            );
    }

    

    $req = $this->db->prepare('SELECT watts FROM table_LTS1');
    $req->execute();
    while ($posts = $req->fetch()) {

                    $LTS1 = $posts['watts'];
        
    }

    $req = $this->db->prepare('SELECT watts FROM table_LTS2');
    $req->execute();
    while ($posts = $req->fetch()) {

                    $LTS2 = $posts['watts'];
               
    }

    $req = $this->db->prepare('SELECT watts FROM table_LTS3');
    $req->execute();
    while ($posts = $req->fetch()) {

                    $LTS3 = $posts['watts'];
           
    }
  
    $req = $this->db->prepare('SELECT watts FROM table_LTS4');
    $req->execute();
    while ($posts = $req->fetch()) {

                    $LTS4 = $posts['watts'];
  
    }

    $req = $this->db->prepare('SELECT watts FROM table_LTS5');
    $req->execute();
    while ($posts = $req->fetch()) {

                    $LTS5 = $posts['watts'];
                    
    }

    $req = $this->db->prepare('SELECT watts FROM table_LTS6');
    $req->execute();
    while ($posts = $req->fetch()) {

                    $LTS6 = $posts['watts'];
                    
    }
    

    return $this->view->render($response, 'test.html', ['items' => $LTP, 'LTS1' => $LTS1, 'LTS2' => $LTS2,
    'LTS3' => $LTS3, 'LTS4' => $LTS4, 'LTS5' => $LTS5, 'LTS6' => $LTS6]);   //permet de rendre la vue çàd index.html
       
});


$app->post('/changepost', function (Request $request, Response $response, array $args) {

    $req = $this->db->prepare("SELECT password FROM `passw`");
    $req->execute();
    $pwd =  $req->fetch();
    if ($_POST['new'] == $_POST['new2'])
    {
    $mdp = PREFIXE.hash('sha512', $_POST['old']).SUFFIXE;
    $newpwd = PREFIXE.hash('sha512', $_POST['new']).SUFFIXE;
    }
    if (!isset($mdp) OR $mdp != $pwd['password'])

    {
        return $response->withRedirect('incorrect');   
       
    }
   else
   {
  
    $req = $this->db->prepare("UPDATE passw SET password='$newpwd' WHERE password='$mdp'");
    $req->execute();

    return $response->withRedirect('ok');
}
});
 $app->post('/admin', function (Request $request, Response $response, array $args) {
    
    
 

    $req = $this->db->prepare("SELECT password FROM `passw`");
    $req->execute();
    $pwd =  $req->fetch();
    $mdp = PREFIXE.hash('sha512', $_POST['mot_de_passe']).SUFFIXE;



    if (!isset($mdp) OR $mdp != $pwd['password'])

    {
        return $response->withRedirect('incorrect');   
       
    }
   else
   {

    
    return $this->view->render($response, 'admin.html');
    }   
});
$app->run();

?>
