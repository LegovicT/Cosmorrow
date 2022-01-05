<!--

faire une fonction dans le controlleur pour verifier les actions <== main::CONTROLE DES ACTIONS
idem pour COM et COL

-->
<!-- URL :
http://localhost/Cosmorrow/main.php
http://www.cosmorrow.com/Cosmorrow/main.php
-->

<?php
//INCLUSION DES CLASSES
    include 'Views/display.php';                            //genere l'affichage du code HTML
    include 'Controller/fileReader.php';                    //gere la lecture des fichiers ressources
    include 'Controller/controller.php';                    //gere le controle des variables super globales

//INITIALISATION DES VARIABLES SESSION
    session_start();
    /************** Variables de session : ********************
    *  Action       =>              tableau des actions
    *   - Home          =>  affiche le formulaire des cosmorrows
    *   - Reset         =>  affiche le formulaire des cosmorrows
    *   - FormCOM       =>  affiche le formulaire des cosmorrows
    *   - ValideCOM     =>  verifie le cosmorrow selectionné
    *   - FormCOL       =>  affiche le formulaire des cosmoleds
    *   - ValideCOL     =>  verifie les cosmoleds selectionnées
    *   - Rendering     =>  affiche le rendu (nom des elements choisis, valeur de consomation, valeur de PF, valeur de PE, graphique du spectre d'absorbtion, schema du montage)
    *   - Download      =>  telecharge le PFD du rendu pour le client
    *
    *  COM          =>              cosmorrows selectionné
    *   - ref           =>  reference du cosomorrow
    *   - design        =>  designation du cosomorrow
    *   - pin           =>  nombres de pines du cosomorrow
    *   - pow           =>  puissance de consomation du cosomorrow
    *   - spacing       =>  espacement du cosomorrow
    *   - img3D         =>  chemin de l'image 3D du cosomorrow
    *   - img2D         =>  chemin de l'image 2D du cosomorrow
    *   - preset        =>  noms des presets du cosomorrow
    *       - col           =>  reference des cosmoleds du preset du cosomorrow
    *
    *  COL          =>              tableau des cosmoleds selectionnées
    *   - ref           =>  references des cosmoleds selectionnées
    *   - design        =>  designations des cosmoleds selectionnées
    *   - pow           =>  puissances de consomation des cosmoleds selectionnées
    *   - PF            =>  flux de photons des cosmoleds selectionnées
    *   - PE            =>  efficacités lumineuses des cosmoleds selectionnées
    *   - img           =>  chemins des images des cosmoleds selectionnées
    *   - spectrum      =>  spectres d'absorbtions des cosmoleds selectionnées
    *       - waveLength    =>  longueurs d'ondes des spectres
    *       - irradiance    =>  irradiances des spectres
    *
    *  COMList      =>              tableau des cosmorrows disponibles
    *
    *  COLList      =>              tableau des cosmoleds disponibles
    *
    */

//INSTANCIATIONS
    $_display = new Display;
    $_controller = new Controller;
    $_controller->_display = $_display;

//DECLARATIONS DES VARIABLES SUPER GLOBALES
    readCOMFile();
    readCOLFile();

//CONTROLE DES ACTIONS ET VARIABLES SESSION
    //récupère les données d'action passées dans l'URL
    if(isset($_GET["Action"]) and $_GET["Action"] != null)
    {
        $_SESSION["Action"] = $_GET["Action"];
        $_GET["Action"] = null;
    }
    else
    {
        $_SESSION["Action"] = "FormCOM";
    }
    //récupère des données du cosmorrow passées dans l'URL
    if(isset($_GET["COM"]))     //controle du cosmorrow selectionné
    {
        if($_controller->verifCOM($_GET["COM"]))
        {
            $_SESSION["Action"] = "ValideCOM";
            $_controller->_display->getCOL();
        }
        else
        {
            $_SESSION["Action"] = "FormCOM";
            $_controller->_display->getCOM();
        }
    }
    //récupère des données des cosmoleds passées dans l'URL
    if(isset($_GET["presets"])) //controle des cosmoleds selectionnées
    {
        $tab = array();
        $value = "0";
        $i = 1;
        while($value != null)
        {
            if(!isset($_GET["COL".$i]))
            {
                $value = null;
            }
            else
            {
                $value = $_GET["COL".$i];
            }
            $tab{$i-1} = $value;
            $i++;
            if(!isset($_GET["COL".$i]) or $_GET["COL".$i] == null)
            {
                $value = null;
            }
        }
        
        if($_controller->verifCOL($tab))
        {
            $_SESSION["Action"] = "Rendering";
            $_controller->_display->getRender();
        }
        else
        {
            $_SESSION["Action"] = "FormCOL";
            $_controller->_display->getCOL();
        }
    }
    //recupere les données calculées dans l'url
    if(isset($_GET["Conso"]) and $_GET["Conso"] != null)
    {
        $_SESSION["Rendering"]{"Conso"} = $_GET["Conso"];
    }
    if(isset($_GET["PE"]) and $_GET["PE"] != null)
    {
        $_SESSION["Rendering"]{"PE"} = $_GET["PE"];
    }
    if(isset($_GET["PF"]) and $_GET["PF"] != null)
    {
        $_SESSION["Rendering"]{"PF"} = $_GET["PF"];
    }

//AFFICHAGE

    //Tests
        //var_dump("SESSION[COM]{preset}",$_SESSION["COM"]{"preset"});
        //var_dump("SESSION[COMList]{preset}",$_SESSION["COMList"]{"preset"});
        //var_dump("SESSION[COL]",$_SESSION["COL"]);
        //var_dump("SESSION[Action]",$_SESSION["Action"]);
        //var_dump("SESSION[COM]",$_SESSION["COM"]);
        //var_dump("SESSION[COMList]",$_SESSION["COMList"]);
        //var_dump("SESSION[COLList]",$_SESSION["COLList"]);
        //var_dump("SESSION[COLList]{spectrum}",$_SESSION["COLList"]{"spectrum"});


    //controle de l'action
        $_controller->verifAction();
        //affichage de la page
        echo $_controller->_display->printPage();
?>