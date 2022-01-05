<?php
/*
* la classe controller permet d'effectuer les controles sur les données passées dans l'URL
*                      effectue le controle des variables de session
*/

class Controller
{
//VARIABLES
    private $_actions;                      //tableau des actions possibles
    public $_display;

//CONSTRUCTEUR
    public function __construct()
    {
        $this->_actions = array(
            "1" => "FormCOM",
            "2" => "ValideCOM",
            "3" => "FormCOL",
            "4" => "ValideCOL",
            "5" => "Rendering",
            "6" => "Download");
    }

//GETTER
    //verifie la validité de l'action passée dans l'URL
    public function verifAction()
    {
        //verification de l'action passée
        if(!isset($_SESSION["Action"]) or !in_array($_SESSION["Action"], $this->_actions))    //si la variable de session action n'existe pas ou n'est pas dans le tableau des actions prévues
        {
            $_SESSION["Action"] = "FormCOM";                                            //l'action sera d'afficher le formulaire des cosmorrows
        }

        //traitement de l'action
        switch($_SESSION["Action"])
        {
            case "FormCOM":                                                                            //formulaire de selection du cosmorrow
                $this->_display->getCOM();
            break;
            case "ValideCOM":                                                                          //validation du formulaire du cosmorrow
                if($this->verifCOM($_SESSION["COM"]{"ref"}))
                {
                    //echo "<script type='text/javascript'>document.location.replace('http://localhost/Cosmorrow/main.php?Action=FormCOL');</script>";
                    $this->_display->getCOL();
                }
                else
                {
                    $this->_html .= "ERROR : The cosmorrow was not filled in!";
                    unset($_SESSION["COM"]);
                    echo "<script type='text/javascript'>document.location.replace('http://localhost/Cosmorrow/main.php?Action=FormCOM');</script>";
                    $this->_display->getCOM();
                }
            break;
            case "FormCOL":                                                                           //formulaire de selection des cosmoleds
                $this->_display->getCOL();
            break;
            case "ValideCOL":                                                                          //validation du formulaire des cosmoleds
                if($this->verifCOL($_SESSION["COL"]{"ref"}) and $this->verifCOM($_SESSION["COM"]{"ref"}))
                {
                    $this->_display->getRender();
                }
                else
                {
                    $this->_html .= "ERROR : The cosmorrow was not filled in! or not the whole of cosmoleds are filled in!";
                    unset($_SESSION["COL"]);
                    //redirection vers le formulaire de selection des cosmoleds
                    echo "<script type='text/javascript'>document.location.replace('http://localhost/Cosmorrow/main.php?Action=FormCOL');</script>
                    <script>alert('ERROR : The cosmorrow was not filled in! or not the whole of cosmoleds are filled in!');</script>";
                }
            break;
            case "Rendering":                                                                           //affichage du rendu
                $this->_display->getRender();
            break;
            case "Download":                                                                            //telecharge le PDF du rendu
                $this->_display->getPDF($_SESSION["Rendering"]{"Conso"}, $_SESSION["Rendering"]{"PE"}, $_SESSION["Rendering"]{"PF"});
            break;
            default: 
                $this->_display->getCOM();
            break;
        }
    }
    //verifie la validité du cosmorrow selectionnné
    public function verifCOM(string $refCOM)
    {
        $_SESSION["COM"] = null;
        if(isset($_SESSION["COMList"]{"ref"}) and $_SESSION["COMList"]{"ref"} != null)
        {
            foreach($_SESSION["COMList"]{"ref"} as $i => $ref)
            {
                if($refCOM == $_SESSION["COMList"]{"ref"}{$i})
                {
                    $_SESSION["COM"]{"ref"} = $_SESSION["COMList"]{"ref"}{$i};
                    $_SESSION["COM"]{"design"} = $_SESSION["COMList"]{"design"}{$i};
                    $_SESSION["COM"]{"pin"} = $_SESSION["COMList"]{"pin"}{$i};
                    $_SESSION["COM"]{"pow"} = $_SESSION["COMList"]{"pow"}{$i};
                    $_SESSION["COM"]{"spacing"} = $_SESSION["COMList"]{"spacing"}{$i};
                    $_SESSION["COM"]{"img3D"} = $_SESSION["COMList"]{"img3D"}{$i};
                    $_SESSION["COM"]{"img2D"} = $_SESSION["COMList"]{"img2D"}{$i};
                    //var_dump($refCOM);
                    if(isset($_SESSION["COMList"]{"preset"}{$i}) and $_SESSION["COMList"]{"preset"}{$i} != null)
                    {
                        $_SESSION["COM"]{"preset"} = $_SESSION["COMList"]{"preset"}{$i};
                    }
                    //var_dump("SESSION[COM]{preset}".$i, $_SESSION["COM"]{"preset"});
                    //var_dump("SESSION[COMList]{preset}".$i, $_SESSION["COMList"]{"preset"}{$i});
                }
            }
        }
        //retourne vrai si le cosmorrow renseigné existe
        return in_array($refCOM, $_SESSION["COMList"]{"ref"});
    }
    //verifie la validité des cosmoleds selectionnnées
    public function verifCOL(array $refCOL)
    {
        $_SESSION["COL"] = null;
        //parcours la liste des cosmoleds selectionnées
        foreach($refCOL as $i => $value)
        {
            //parcours la liste des cosmoleds disponibles
            foreach($_SESSION["COLList"]{"ref"} as $j => $ref)
            {
                if($refCOL{$i} == $_SESSION["COLList"]{"ref"}{$j})
                {
                    $_SESSION["COL"]{"ref"}{$i} = $_SESSION["COLList"]{"ref"}{$j};
                    $_SESSION["COL"]{"design"}{$i} = $_SESSION["COLList"]{"design"}{$j};
                    $_SESSION["COL"]{"pow"}{$i} = $_SESSION["COLList"]{"pow"}{$j};
                    $_SESSION["COL"]{"PF"}{$i} = $_SESSION["COLList"]{"PF"}{$j};
                    $_SESSION["COL"]{"PE"}{$i} = $_SESSION["COLList"]{"PE"}{$j};
                    $_SESSION["COL"]{"img"}{$i} = $_SESSION["COLList"]{"img"}{$j};
                    $_SESSION["COL"]{"spectrum"}{$i}{"waveLength"} = $_SESSION["COLList"]{"spectrum"}{$j}{"waveLength"};
                    $_SESSION["COL"]{"spectrum"}{$i}{"irradiance"} = $_SESSION["COLList"]{"spectrum"}{$j}{"irradiance"};
                    //var_dump("WAVELENGHT".($i),$_SESSION["COL"]{"spectrum"}{$i}{"waveLength"});
                    //var_dump("IRRADIANCE".($i),$_SESSION["COL"]{"spectrum"}{$i}{"irradiance"});
                }
                elseif($refCOL{$i} == "None")
                {
                    $_SESSION["COL"]{"ref"}{$i} = "";
                    $_SESSION["COL"]{"design"}{$i} = "";
                    $_SESSION["COL"]{"pow"}{$i} = 0;
                    $_SESSION["COL"]{"PF"}{$i} = 0;
                    $_SESSION["COL"]{"PE"}{$i} = 0;
                    $_SESSION["COL"]{"img"}{$i} = "";
                    $_SESSION["COL"]{"spectrum"}{$i} = array("waveLength" => 0, "irradiance" => 0);
                }
            }
        }
        //retourne vrai si les cosmoleds renseignées existent et sont aux nombres de places du cosmorrow selectionné
        $isvalide = true;
        if($_SESSION["COM"]{"pin"} == count($refCOL))
        {
            foreach($refCOL as $i => $value)
            {
                if($refCOL{$i} != 'none' and !in_array($refCOL{$i}, $_SESSION["COLList"]{"ref"}))
                {
                    $isvalide = false;
                }
            }
        }
        else
        {
            if($isvalide == false)
            {
                echo "aucune cosmoleds n\'a été selectionnnée";
            }

            $isvalide = false;
        }

        return $isvalide;
    }
}
?>