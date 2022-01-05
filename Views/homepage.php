<?php
/*
* la classe HomePage permet d'afficher la page HTML
*                    effectue le traitement des actions
*/

class HomePage
{
//VARIABLES
    private $_html;                                 //Code html a afficher
    private $_head;                                 //entete de la page html
    private $_section;                              //section de la page html

//CONSTRUCTEUR
    public function __construct()
    {
        //VARIABLES
            private $_html;                                 //Code html a afficher
            private $_head =                                //entete de la page html
                    "<!DOCTYPE html>
                    <html>
                        <head>
                            <title>Cosmorrow</title>
                            <meta charset=\"utf-8\"/>
                            <meta name=\"application-name\" content=\"Cosmorrow\" />
                            <link rel=\"icon\" sizes=\"144x144\" href=\"resources/pics/sjlogo.png\" />
                            <link href=\"/default/CMS/css/cache/39885748d851c6389c515ebeb172ae1b.css\" media=\"screen,print\" rel=\"stylesheet\" type=\"text/css\" />
                            <script src=\"https://cdn.jsdelivr.net/npm/chart.js@2.8.0\"></script>
                            <script src=\"https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js\"></script>
                        </head>";
            private $_section;                              //section de la page html
    }

//GETTER
    //Obtenir la page html
    public function printPage()
    {
        $this->_html = $this->_head;

        //Controle de l'action
        switch($_SESSION["Action"])
        {
            case "FormCOM":                                                                            //formulaire de selection du cosmorrow
                $this->_html .= $this->_section->getCOM();
            break;
            case "ValideCOM":                                                                          //validation du formulaire du cosmorrow
                if(isset($_SESSION["COM"]) and in_array($_SESSION["COM"]{"ref"}, $_SESSION["COMList"]{"ref"}))
                {
                    setCOM($_SESSION["COM"]{"ref"});
                    //redirection vers le formulaire de selection des cosmoleds
                    echo "<script type='text/javascript'>document.location.replace('http://localhost/Cosmorrow/main.php?Action=FormCOL');</script>";
                }
                else
                {
                    unset($_SESSION["COM"]);
                    //redirection vers le formulaire de selection du cosmorrow
                    echo "<script type='text/javascript'>document.location.replace('http://localhost/Cosmorrow/main.php?Action=FormCOM');</script>";
                }
            break;
            case "FormCOL":                                                                           //formulaire de selection des cosmoleds
                if(isset($_SESSION["COM"]) and $_SESSION["COM"] != null)
                {
                    $this->_html .= $this->_section->getCOL();
                }
                else
                {
                    $this->_html .= $this->_section->getCOM();
                    $this->_html .= "ERROR : The cosmorrow was not filled in !";
                }
            break;
            case "ValideCOL":                                                                          //validation du formulaire des cosmoleds
                $refCOLTab = array();
                foreach($_SESSION["COL"]{"ref"} as $key => $value)
                {
                    if(in_array($value, $_SESSION["COLList"]{"ref"}))
                    {
                        $refCOLTab{$key} = $value;
                    }
                }
                setCOL($refCOLTab);
            break;
            case "Rendering":                                                                           //affichage du rendu
                $this->_html .= $this->_section->getRender();
            break;
            default: 
                $this->_html .= $this->_section->getCOM();
            break;
        }

        $this->_html .= "</html>";
        return $this->_html;
    }
}
?>