<?php
/*
* la classe Display permet d'afficher la page HTML
*                   effectue le traitement des actions
*/

//inclusion de la classe qui gere la creation d'un pdf
require('pdfPage.php');

class Display
{
//VARIABLES
    private $_html;                                 //Code html a afficher
    private $_head;                                 //entete de la page html
    private $_style;
    private $_script;
    private $_header;
    private $_section;
    private $_COM;
    private $pdf;                                   //classe qui gere la creation d'un pdf
    public $spectrumEQ;
    public $maxIrrad;
    
//CONSTRUCTEUR
    public function __construct()
    {
        $this->_head =
            "
            <?xml version=\"1.0\" encoding=\"ISO-8859-1\" standalone=\"yes\" lang=\"fr\"?>
            <!DOCTYPE html>
            <html>
                <head>
                    <title>Cosmorrow</title>
                    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />
                    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
                    <meta name=\"application-name\" content=\"Cosmorrow\" />
                    <!--<meta http-equiv=\"refresh\" content=\"5;\">-->
                    <link rel=\"icon\" sizes=\"144x144\" href=\"resources/pics/sjlogo.png\" />
                    <link href=\"/default/CMS/css/cache/39885748d851c6389c515ebeb172ae1b.css\" media=\"screen,print\" rel=\"stylesheet\" type=\"text/css\" />
                    <link href=\"Style/cosmo.css\" type=\"text/css\" rel=\"stylesheet\" media=\"all\" />
                    <link rel=\"stylesheet\" href=\"https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css\" integrity=\"sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh\" crossorigin=\"anonymous\">
                    <!--inclusion de JQuery-->
                    <script src=\"https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js\"></script>
                    <!--inclusion de bootstrap-->
                    <script src=\"https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js\" integrity=\"sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6\" crossorigin=\"anonymous\"></script>
                    <!--inclusion de utilJS-->
                    <script src=\"https://cdnjs.cloudflare.com/ajax/libs/javascript.util/0.12.12/javascript.util.min.js\"></script>
                    <!--inclusion de chartJS-->
                    <script src=\"https://cdn.jsdelivr.net/npm/chart.js@2.9.3/dist/Chart.min.js\"></script>
                    <script src=\"https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js\"></script>
                </head>";

        $this->_header = "
                <body style=\"background:#111111;\">
                <div class=\"container-fluid col-xs-12\" style=\"margin-right: auto; margin-left: auto;\">
                    <div class=\"row\" style=\"text-align: center; text-decoration: none; border:#DCAA00 5px ridge; margin:0px; padding:0px;\">
                        <a class=\"col-xs-0 col-sm-2\" id=\"siteLink\" href=\"https://www.secretjardin.com/\" style=\"text-decoration: none;\"><img class=\"img-thumbnail d-none d-sm-block\" style=\"background:#111111; border:none\" src=\"resources/pics/SJhome.png\" ></a>
                        <a class=\"col-xs-8 col-md-8\" id=\"titleLink\" href=\"main.php?Action=Home\" style=\"text-decoration: none;\"><h1 style=\"color:#DCAA00; font-size: 400%;\">Cosmorrow</h1><h3 style=\"color:#DCAA00; font-size: 250%;\">from Secret Jardin</h3></a>
                    </div>";
        
        $this->pdf = new PDFPage();
    }

//GETTERS
    //obtenir le formulaire de choix du cosmorrow
    public function getCOM()
    {
        $this->_COM = '
                    <!-- CAROUSEL -->
                        <div class="container" style="text-align: center; text-decoration: none;">
                            <p class="h1" style="color:#DCAA00;">Choose Your Cosmorrow</p>';
        //<CAROUSEL-->
        $this->_COM .= '
                                <div id="carouselExampleCaptions" class="carousel slide" data-ride="carousel">
                                    <ol class="carousel-indicators">';
        //inclusion des reperes
        foreach($_SESSION["COMList"]{"ref"} as $k => $value)
        {
            if($k == 0)
            {
                $this->_COM .= '
                                        <li style="background-color:#DCAA00;" data-target="#carouselExampleCaptions" data-slide-to="0" class="active"></li>';
            }
            else
            {
                $this->_COM .= '
                                        <li style="background-color:#DCAA00;" data-target="#carouselExampleCaptions" data-slide-to="'.$k.'"></li>';
            }
        }
        $this->_COM .= '
                                    </ol>
                                    <div class="carousel-inner">';
        //inclusion des images references et designations des cosmorrows
        foreach($_SESSION["COMList"]{"ref"} as $k => $value)
        {
            if($k == 0)
            {
                $this->_COM .= '
                                        <div class="carousel-item active">'; 
            }
            else
            {
                $this->_COM .= '
                                        <div class="carousel-item">'; 
            }
            $this->_COM .= '
                                            <img src="'.$_SESSION["COMList"]{"img3D"}{$k}.'" alt="'.$_SESSION["COMList"]{"ref"}{$k}.'">
                                            <div class="carousel-caption d-none d-md-block">
                                                <h5 style="color:#111111;">'.$_SESSION["COMList"]{"ref"}{$k}.'</h5>
                                                <p style="color:#111111;">'.$_SESSION["COMList"]{"design"}{$k}.'</p>
                                            </div>
                                        </div>';
        }
        //inclusion des controles
        $this->_COM .= '
                                    </div>
                                    <a style="background-color:#111111" class="carousel-control-prev" href="#carouselExampleCaptions" role="button" data-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Previous</span>
                                    </a>
                                    <a style="background-color:#111111" class="carousel-control-next" href="#carouselExampleCaptions" role="button" data-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Next</span>
                                    </a>
                                </div>';
        //<--CAROUSEL>


                        $this->_COM .= '
                                
                            <form class="form-row align-middle" style="width:100%; text-align:center;" id="COM" action="main.php?Action=ValideCOM" method="GET">
                                <div class="row row-xs-12 row-sm-12 align-middle">
                                    <div class="row-xs-12 row-sm-12  align-middle" style="width:100%; text-align: center;">';
       
        //<SELECTION COSMORROW-->
        foreach($_SESSION["COMList"]{"ref"} as $k => $value)
        {
            $this->_COM .= '
                                        <div class="col align-middle" style="width:100%; margin: auto; text-align: center;">
                                            <label class="control-label align-middle" style="width:50%; margin: auto; text-align: center;"><input style="width:100%" type="radio" name="COM" value="'.$value.'" >
                                            <img class="img-responsive" style="width:100%" src="';
            if(file_exists($_SESSION["COMList"]{"img3D"}{$k}))
            {
                $this->_COM .= $_SESSION["COMList"]{"img3D"}{$k};
            }
            else
            {
                $this->_COM .= 'resources/pics/misspix.png';
            }
            $this->_COM .= '" /><h5 style="color:#DCAA00">'.$_SESSION["COMList"]{"design"}{$k}.'</h5>
                                            </label>
                                        </div>';
        }
        $this->_COM .= '        
                                    </div>
                                    <div class="form-control row-xs-12 row-md-6" style="position:relative; bottom:0; border:none; background-color:#11111100;">
                                        <input style="border:#DCAA00 2px ridge; background-color:#111111; color:#DCAA00;" class="form-control" type="submit" value="Next">
                                        <a style="text-decoration:none; border:#DCAA00 2px ridge; background-color:#111111; color:#DCAA00;" class="form-control" href="http://localhost/Cosmorrow/main.php?Action=Reset" value="Reset">Reset</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>';
        $this->_section = $this->_COM;
        //<--SELECTION COSMORROW>

        //renseigne le style de la page
        $this->_style = "


            <style>
                .carousel-item img{
                    margin-left: auto;
                    margin-right: auto;
                }<!--
                .form-control {
                    border:none; 
                    background-color:#00000000;
                    text-align: center; 
                    text-decoration: none;
                    style-decoration: none;
                    list-style: none;
                }-->
            </style>";
    
        
        //renseigne le script de la page
        $this->_script = "
                <script>
                    //--Mettre en surbrillance le cosmorrow selectionné dans les selections et dans le carousel--//
                        $(function(){
                            $('#COM').on('change', function(){
                                var lab = $(\"input[name='COM']:checked\");
                                $(\"input[name='COM']\").parent().css({
                                    filter : 'none'
                                });
                                lab.parent().css({
                                    filter : 'drop-shadow(0px 0px 30px rgba(220, 170, 0, 0.8)) invert(15%)'
                                });
                                
                                var txt = $(\".carousel-item\");
                                txt.each(function(){
                                    var par = $(this);
                                    //alert(par.children('img').attr('alt'));
                                    if(par.children('img').attr('alt') == lab.val())
                                    {
                                        par.css({
                                            filter : 'drop-shadow(0px 0px 30px rgba(220, 170, 0, 0.8)) invert(15%)'
                                        });
                                    }
                                    else
                                    {
                                        par.css({
                                            filter : 'none'
                                        });
                                    }
                                });
                            })
                        })
                </script>";
    }
    //obtenir le formulaire de choix des cosmoleds
    public function getCOL()
    {
        $this->_COL = '
                        <form id="COL" action="main.php?Action=ValideCOL" style="margin:0px; padding:0px;" method="GET" >';
        //selection des presets
        $this->_COL .= '
                                    <h6 style="margin:0px; color:#DCAA00;">Presets of <strong>'.$_SESSION["COM"]{"ref"}.'</strong> '.$_SESSION["COM"]{"design"}.' : </h6>
                                    <select style="margin:0px; margin-bottom:15px; background-color:#111111; color:#DCAA00;" name="presets">
                                            <option>None</option>';
        if(isset($_SESSION["COM"]{"preset"}) and $_SESSION["COM"]{"preset"} != null)
        {
            foreach($_SESSION["COM"]{"preset"} as $d => $prs)
            {
                //var_dump("preset".$d." ", $_SESSION["COM"]{"preset"}{$d});
                if($prs != null)
                {
                    $this->_COL .= '
                                            <option>'.$prs{0}.'</option>';
                }
            }
        }
        $this->_COL .= '
                                    </select><br/>
                                    <ol id="tabCOLPin" style="margin:0px; padding:0px;">
                                    <div class="row" style="margin:0px; padding:0px; justify-content:center;">';
        //selection des cosmoleds suivant les pins
        foreach(range(1, $_SESSION["COM"]{"pin"}) as $key => $value)
        {
            //tableau avec 1er ligne => numero de la pin, et 2nd ligne deroulant des cosmoleds a dispositions
            $this->_COL .= '            
                                        <div class="col-xs-1 col-sm-1 col-md-1" style="margin:0px; padding:0px;">
                                        <ul style="border:#DCAA00 2px ridge; margin:0px; padding:0px;" id="pinNo'.strval($key+1).'/'.strval($_SESSION["COM"]{"pin"}).'">';
            //liste des pins du cosmorrow selectionné
            $this->_COL .= '
                                            <li class="pin" style="color:#DCAA00; list-style:none; margin:0px; padding:0px;" id="piname'.strval($key+1).'/'.strval($_SESSION["COM"]{"pin"}).'">'.strval($key+1).'/'.strval($_SESSION["COM"]{"pin"}).'</li>';
            //liste des listes des cosmoleds disponibles
            $this->_COL .= '
                                            <li class="COL container-fluid" style="margin:0px; padding:0px;" id="COL'.strval($key+1).'/'.strval($_SESSION["COM"]{"pin"}).'">
                                                <select class="form-control align-middle" style="list-style:none; padding:0px; background-color:#111111; color:#DCAA00; margin: auto; text-align: center;" name="COL'.strval($key+1).'">
                                                    <option value="none">None</option>';
            foreach($_SESSION["COLList"]{"ref"} as $k => $val)
            {
                if($_SESSION["COLList"]{"pow"}{$k} == $_SESSION["COM"]{"pow"})
                {
                    $this->_COL .= '
                                                    <option value="'.strval($_SESSION["COLList"]{"ref"}{$k}).'">'.strval($_SESSION["COLList"]{"design"}{$k});
                }
            }
            $this->_COL .= '
                                                </select>
                                                <img id="imgCOL'.strval($key+1).'" class="img-responsive center-block" src="Resources/COL/COL20BK.png"/></br>
                                                <span id="labCOL'.strval($key+1).'" style="color:#DCAA00;">None</span>';
            $this->_COL .= '
                                            <br />
                                            </li>
                                        </ul>
                                        </div>';
        }
        $_SESSION["COM"]{"spacing"} = 0;
        $this->_COL .= '
                                    </div>
                                    </ol>';
        $this->_COL .= '
                            <div class="form-control row-xs-12 row-md-6" style="text-align:center; position:relative; bottom:0; border:none; background-color:#11111100;">
                                <input style="text-align:center; border:#DCAA00 2px ridge; background-color:#111111; color:#DCAA00;" class="form-control" type="submit" value="Next">
                                <a style="text-decoration:none; border:#DCAA00 2px ridge; background-color:#111111; color:#DCAA00;" class="form-control" href="http://localhost/Cosmorrow/main.php?Action=Back" value="Back">Back</a>
                            </div>
                        </form>';
        
        
        $this->_style = "
                <style>

                </style>";

        $this->_section = $this->_COL;

        //Script js : changer le label et l'image des cosmoleds selectionné
        $this->_script = "
                <script>
                    $(function(){
                        $('select').on('change', function(){
                            var col = $(this).children(\"option:selected\");
                            if($(this).attr('name') != $('select[name=presets]').attr('name'))
                            {
                                $('select[name=presets]').val('None');
                            }
                            if(col.val() == \"none\")
                            {
                                var imgSrc = col.parent().parent().children('img').attr('src', 'Resources/COL/COL".$_SESSION["COM"]{"pow"}."BK.png');
                                var span = col.parent().parent().children('span').html('None');
                            }
                            else
                            {
                                var imgSrc = col.parent().parent().children('img').attr('src', 'Resources/COL/' + col.val() + '.png');
                                var span = col.parent().parent().children('span').html(col.val());
                            }
                        });
                    })
                </script>";
        //Script js : changer les cosmoleds lors de la selection d'un preset
                    
            $this->_script .= "
                <script>
                    var presetTab = [];";
            if(isset($_SESSION["COM"]{"preset"}) and $_SESSION["COM"]{"preset"} != null)
            {
                foreach($_SESSION["COM"]{"preset"} as $i => $pres)
                {
                    $this->_script .= "
                        presetTab[".$i."] = new Array();
                        presetTab[".$i."].push('".$pres{0}."');";
                    foreach($pres{1} as $k => $colVal)
                    {
                        $this->_script .= "
                        presetTab[".$i."].push('".$colVal."');";
                    }
                }
                $this->_script .= "
                    </script>
                    <script>
                        $('select[name=presets]').on('change', function(){
                            var pre = $(this).children(\"option:selected\");
                            for(i = 0; i < presetTab.length; i++)
                            {
                                if(pre.val() == \"None\")
                                {
                                    for(id = 0; id < ".$_SESSION["COM"]{"pin"}."; id++)
                                    {
                                        $('select[name=COL'+(id+1)+']').val('None');
                                        $('#imgCOL'+(id+1)).attr('src', 'Resources/COL/COL".$_SESSION["COM"]{"pow"}."BK.png');
                                        $('#labCOL'+(id+1)).html('None');
                                    }
                                }
                                else if(pre.val() == presetTab[i][0])
                                {
                                    for(id = 0; id < ".$_SESSION["COM"]{"pin"}."; id++)
                                    {
                                        $('select[name=COL'+(id+1)+']').val(presetTab[i][id+1]);
                                        $('#imgCOL'+(id+1)).attr('src', 'Resources/COL/'+presetTab[i][id+1]+'.png');
                                        $('#labCOL'+(id+1)).html(presetTab[i][id+1]);
                                    }
                                }
                            }
                        })
                    </script>";
            }



                            /*var contain = preset.parent().parent().children('ol').children('div');
                            contain.each(function(div ul li img) 
                            {
                                if(preset.val() == \"none\")
                                {
                                    $(this).attr('src', 'Resources/COL/COL".$_SESSION["COM"]{"pow"}."BK.png');
                                }
                                else
                                {
                                    $(this).attr('src', 'Resources/COL/".$_SESSION["COM"]{"pow"}."\"+preset.val()+\".png');
                                }
                            });
                            contain.each(function(div ul li span) 
                            {

                                if(preset.val() == \"none\")
                                {
                                    $(this).html('None');
                                }
                                else
                                {
                                    $(this).html('COL".$_SESSION["COM"]{"pow"}."\"+preset.val());
                                }
                            });
                        });
                    })
                </script>";*/




        /*$this->_script .= "
                    $(function(){
                        $('select[name=presets]').on('change', function(){
                            var preset = $(this).children(\"option:selected\");
                            alert(preset.val());";
        foreach($_SESSION["COM"]{"preset"} as $i => $pres)
        {
            var_dump("pres".$i, $pres{0});
            var_dump("preset.val()");
            if($pres{0} == "preset.val()")
            {
                foreach($_SESSION["COM"]{"preset"}{$i}{1} as $k => $colVal)
                {
                    $this->_script .= "
                            alert('".$colVal."');
                            $('#imgCOL".($k+1)."').attr('src', 'Resources/COL/".$colVal.".png');
                            $('#labCOL".($k+1)."').html('".$colVal."');";
                }               
            }
            //elseif('none' == "preset.val()")
            else
            {
                foreach($_SESSION["COM"]{"pin"} as $k => $pinVal)
                {
                        $this->_script .= "
                            $('#imgCOL".($k+1)."').attr('src', 'Resources/COL/COL".$_SESSION["COM"]{"pow"}."BK.png');
                            $('#labCOL".($k+1)."').html('None');";
                }
            }
        }
        $this->_script .= "
                        });
                    })
                </script>";*/
                /* 
                    $(function(){
                        $('select[name=presets]').on('change', function(){
                            var preset = $(this).children(\"option:selected\");
                            var col = preset.val().substring(preset.val().length - 2, preset.val().length); 
                            alert(col);
                            var contain = preset.parent().parent().children('ol').children('div');
                            contain.each(function(div ul li img) 
                            {
                                if((preset.val() == \"none\") && (".$_SESSION["COM"]{"pow"}." == 20))
                                {
                                    $(this).attr('src', 'Resources/COL/COL20BK.png');
                                }
                                else if((col.val() == \"none\") && (".$_SESSION["COM"]{"pow"}." == 40))
                                {
                                    $(this).attr('src', 'Resources/COL/COL40BK.png');
                                }
                                else if((col.val() != \"none\") && (".$_SESSION["COM"]{"pow"}." == 20))
                                {
                                    $(this).attr('src', 'Resources/COL/COL20\"+col.val()+\".png');
                                }
                                else if((col.val() != \"none\") && (".$_SESSION["COM"]{"pow"}." == 40))
                                {
                                    $(this).attr('src', 'Resources/COL/COL40\"+col.val()+\".png');
                                }
                            });
                            contain.each(function(div ul li span) 
                            {
                                if((preset.val() == \"none\") && (".$_SESSION["COM"]{"pow"}." == 20))
                                {
                                    $(this).html('None');
                                }
                                else if((col.val() == \"none\") && (".$_SESSION["COM"]{"pow"}." == 40))
                                {
                                    $(this).html('None');
                                }
                                else if((col.val() != \"none\") && (".$_SESSION["COM"]{"pow"}." == 20))
                                {
                                    $(this).html('COL20\"+col.val());
                                }
                                else if((col.val() != \"none\") && (".$_SESSION["COM"]{"pow"}." == 40))
                                {
                                    $(this).html('COL40\"+col.val());
                                }
                            });
                        });
                    })
                */
    }
    //obtenir l'affichage du rendu
    public function getRender()
    {
        //CALCULES
            //calcule des puissances, flux de photons, efficacité photonique et spectre d'absorbtion
            $Poweq = 0;
            $PEeq = 0;
            $PFeq = 0;
            $divider = 0;
            //pour chaques emplassements du cosmorrow selectionné
            foreach(range(1, $_SESSION["COM"]{"pin"}) as $k => $val)
            {
                //var_dump("PIN".$k, $_SESSION["COL"]{"pow"}{$k});
                //ajoute +1 au diviseur pour faire la moyenne du PE équivalant si la cosmoled $k est selectionnée
                if(isset($_SESSION["COL"]{"ref"}{$k}) and ($_SESSION["COL"]{"ref"}{$k} != "0") and ($_SESSION["COL"]{"ref"}{$k} != null))
                {
                    $divider++;
                }

                //fait les sommes respactives des puissances de consomations, PE et PF équivalants
                if(isset($_SESSION["COL"]{"ref"}{$k}) and ($_SESSION["COL"]{"ref"}{$k} != null))
                {
                    $Poweq += $_SESSION["COL"]{"pow"}{$k};
                    $PEeq += $_SESSION["COL"]{"PE"}{$k};
                    $PFeq += $_SESSION["COL"]{"PF"}{$k};
                }
            }
            //divise le PE équivalant par le nombre d'emplassements si aucun des deux n'est égale à 0
            if($PEeq != 0 or $divider != 0)
            {
                $PEeq /= $divider;
            }




            //Calcules des spectres équivalants

            //pour chaques longueurs d'ondes faire la moyenne de chaques irradiances associées de 350nm à 750nm
            $spectrumTab;
            //collecte toutes les irradiances par chaques longueurs d'ondes dans un tableau par nombres de cosmoleds
            for($i=350; $i <= 750; $i++)
            {
                foreach($_SESSION["COL"]{"spectrum"} as $key => $spectab)
                {
                    foreach($spectab{"waveLength"} as $num => $wavlength)           //pour chaque longueurs d'ondes de la cosmoled ciblée
                    {
                        if($i == $wavlength)
                        {
                            $spectrumTab{$i}{$key+1} = $spectab{"irradiance"}{$num};
                        }
                        if(!isset($spectrumTab{$i}{$key+1}) or $spectrumTab{$i}{$key+1} == null)
                        {
                            $spectrumTab{$i}{$key+1} = 0;
                        }
                    }
                }
            }
            //moyenne des irradiances 
            $spectrumEQ;
            $maxIrrad = 0;
            foreach($spectrumTab as $i => $irradTab)   //$wl => tab des irradiances par nombres de cosmoleds
            {
                $wl = 0;
                foreach($irradTab as $key => $irr)     //$ld => irradiances
                {
                    $wl += $irr;
                    if($irr > $maxIrrad)
                    {
                        $maxIrrad = $irr;
                    }
                }
                if(count($irradTab) != 0)
                {
                    $spectrumEQ{$i} = ($wl / count($irradTab));
                }
            }

            //obtenir le graphique de rendu du spectre (sous forme d'image png)
            $spectrum = "";
            $this->pdf->setSpectrum($spectrumEQ, $maxIrrad);
            $spectrum = $this->setRender($spectrumEQ);



        //AFFICHAGES

            //Affichage final du rendu
            $this->_getRendering = '';
            $this->_getRendering .= '
            <ol id="renderTab" style="display:block; list-style:none; color:#DCAA00; padding:0px; margin-right:auto; margin-left:auto;">
                <li style="border:#DCAA00 ridge 3px;">
                    <ul id="pow" style="border:#DCAA00 ridge 1px;">
                        <p style="color:#DCAA00; font-weight: bolder; font-size: 16px;">Energy consumption:</p><p style="text-align:center;">'.$Poweq.' W</p>
                    </ul>
                    <ul id="PE" style="border:#DCAA00 ridge 1px;">
                        <p style="color:#DCAA00; font-weight: bolder; font-size: 16px;">Photonic Efficiency:</p><p style="text-align:center;">'.$PEeq.' μmol.s/W</p>
                    </ul>
                    <ul id="PF" style="border:#DCAA00 ridge 1px;">
                        <p style="color:#DCAA00; font-weight: bolder; font-size: 16px;">Photon Flux:</p><p style="text-align:center;">'.$PFeq.' μmol/s</p>
                    </ul>
                    <ul id="specLine" style="border:#DCAA00 ridge 1px;">
                        <p style="color:#DCAA00; font-weight: bolder; font-size: 16px;">Spectrum:</p> <br/><p style="text-align:center;">'.$spectrum.'</p>
                    </ul>
                </li>
                <li style="display:block; list-style:none; border:#DCAA00 ridge 3px;">
                    <p style="color:#DCAA00; font-weight: bolder; font-size: 16px;"><h2 style="text-align: center; font-weight: bolder;">Product overview : </h2><br/>
                        <ul style="border:#DCAA00 ridge 1px;">
                            <h3>COSMORROW =
                            '.$_SESSION["COM"]{"ref"}.' </h3><h4> '.$_SESSION["COM"]{"design"}.'</h4><img class="img-responsive center-block" style="width:20%; margin:2%; margin-left:40%;" src="'.$_SESSION["COM"]{"img3D"}.'">
                        </ul>
                        <ul style="border:#DCAA00 ridge 1px;">
                            <h3>COSMOLEDS =</h3>';
            foreach($_SESSION["COL"]{"ref"} as $i => $ref)
            {
                $this->_getRendering .= '
                            <li style="display:block; margin:0px; padding:0px;">
                                <h5 style=" margin:0px; padding:0px;">'.strval($i+1).'/'.strval($_SESSION["COM"]{"pin"}).' = '.$_SESSION["COL"]{"ref"}{$i}.' </h5><h6 style=" magin:0px; padding:0px;">'.$_SESSION["COL"]{"design"}{$i}.'</h6>
                                <img class="img-responsive" style="width:4%; transform: rotate(90deg); margin:-20%; margin-left:40%;" src="'.$_SESSION["COL"]{"img"}{$i}.'">
                            </li>';
            }    
            $this->_getRendering .= '
                        </ul>
                        <a id="saveBtn" href="http://localhost/Cosmorrow/main.php?Action=Download&Conso='.$Poweq.'&PE='.$PEeq.'&PF='.$PFeq.'" Style="font-weight: bolder; font-size: 32px; animation: animTitle 1s linear 0ms infinite alternate both; 
                        list-style: none; border:#DCAA00 ridge 1px; border-radius: 10px; background-color: rgb(50, 50, 50); padding: 10px; color:#DCAA00; margin: 0px; margin-left: 45%; margin-right: 45%;">SAVE</a>
                    </p>
                </li>
            </ol>';
            

            //$this->_getRendering;
            $this->_section = $this->_getRendering;
    }
    //renvoi le code html du graphique du spectre equivalant des LEDs renseignées
    function setRender(array $spectrumTab)
    {
        //mise en forme des listes de données (ex: list = "'val1', 'val2', 'val3'")
        $list1="";      //longueurs d'ondes
        $list2="";      //irradiances
        foreach($spectrumTab as $wl => $irr)
        {
            $list1 .= "'".$wl."', ";
            $list2 .= "'".$irr."', ";
        }
        $list1 = substr($list1, 0, -2);
        $list2 = substr($list2, 0, -2);

        //var_dump("list1", $list1);
        //var_dump("list2", $list2);


        $render = "
                <canvas id=\"graph\" style=\"width:800; height:400; rotation: 180deg;\"></canvas>";

                //TUTO : mise en png            ====> https://stackoverflow.com/questions/923885/capture-html-canvas-as-gif-jpg-png-pdf
                //TUTO : gradiant responsive    ====> https://stackoverflow.com/questions/42873920/javascript-chart-js-with-responsive-gradient-line-filling
                //TUTO : canvas events          ====> https://inserthtml.developpez.com/tutoriels/javascript/interactivite-avec-balise-html5-canvas/
        $this->_script .= "
                <script>
                
                    $(document).ready(function() { 
                        $(window).resize(function() { 
                            $(graph).fadeOut(60000, function(){
                                form.html(msg).fadeIn().delay(300);
                                });
                        }); 
                    });
                    

                    $(graph).parent().resize(function() { 
                        $(graph).fadeOut(60000, function(){
                            form.html(msg).fadeIn().delay(300);
                            });
                    }); 


                    //rafraicissement de la balise canvas
                        setInterval(function(){
                        graph.css('visibility', 'hidden');
                        graph.css('visibility', 'visible');
                        }, 1000);

                    // appel l'affichage de canvas quand le navigateur change de taille
                        $( window ).resize(function() {
                            afficheCanvas(window.innerWidth());
                        $(graph).fadeOut(60000, function(){
                            form.html(msg).fadeIn().delay(300);
                            });
                        });
                        
                        canvas = document.getElementById('graph');/*
                        canvas.addEventListener('mousemove', mousemovement, true);
                        canvas.addEventListener('mousedown', mouseclick, false);
                        canvas.addEventListener('mouseup', mouseunclick, false);*/

                    // fonction d'affichage de canvas
                        function afficheCanvas(winwidth) {
                        
                            var ctx = document.getElementById('graph').getContext('2d');

                            var gradientStroke = ctx.createLinearGradient(-20, 0, $('canvas').parent().width() - 40, 0);
                            gradientStroke.addColorStop(0.1, 'rgba(0, 0, 0, 1)');
                            gradientStroke.addColorStop(0.2, 'rgba(200, 0, 200, 1)'); 
                            gradientStroke.addColorStop(0.33, 'rgba(0, 0, 255, 1)');
                            gradientStroke.addColorStop(0.43, 'rgba(0, 255, 255, 1)');
                            gradientStroke.addColorStop(0.53, 'rgba(0, 255, 0, 1)');
                            gradientStroke.addColorStop(0.63, 'rgba(255, 255, 0, 1)');
                            gradientStroke.addColorStop(0.73, 'rgba(200, 100, 0, 1)');
                            gradientStroke.addColorStop(0.86, 'rgba(255, 0, 0, 1)');
                            gradientStroke.addColorStop(0.99, 'rgba(0, 0, 0, 1)');
                
                            var chart = new Chart(ctx, {
                                type: 'line', responsive: true, maintainAspectRatio: false,
                                data: 
                                {
                                    labels: [".$list1."],
                                    datasets: 
                                    [{
                                        label: 'Spectrum rendering', data: [".$list2."],
                                        borderColor: 'rgb(50, 50, 50)',  borderWidth: 1,
                                        backgroundColor: gradientStroke 
                                    }]  
                                },
                                options: 
                                {
                                    elements: { point: { radius: 0 } },
                                    responsive: true, maintainAspectRatio: false,
                                    legend: { display: false},
                                    ticks: { reverse: true }
                                }
                            }); 
                            
                            var image = chart.toDataURL('image.png');
                            document.write('<img src='+image+'/>');
                            chart.render();
                            chart.exportChart({format: 'png'});
                            document.getElementById('saveBtn').addEventListener('click',function(){
                                chart.exportChart({format: 'png'});
                            }); 
                        }
                        
                    // appel le premier affichage de canvas
                    var ww=400;
                    afficheCanvas(ww);

                </script>  
                    ";
             
        //var_dump("waveLength", $_SESSION["COL"]{"ref"}{0});
        //var_dump("liste1", $list1);
        //var_dump("irradiance", $_SESSION["COL"]{"ref"}{0});
        //var_dump("liste2", $list2);
        return $render;
    }   
    //Obtenir le pdf
    public function getPDF(string $conso, string $pe, string $pf)
    {
        //TUTO => https://stackoverflow.com/questions/3293473/how-to-print-to-pdf-using-php-with-a-canvas-element
        
        $this->pdf->AddPage();
        $this->pdf->SetFont('Times','',12);
        //$this->pdf->setMatterial($_SESSION["COM"]{"ref"}, $_SESSION["COL"]{"ref"});
        $this->pdf->setMatterial($_SESSION["COM"]{"ref"}, $_SESSION["COL"]{"ref"});
        $this->pdf->setParameters($conso, $pe, $pf);
        $this->pdf->Body();
        $name = "";
        $name .= $_SESSION["COM"]{"ref"};
        foreach($_SESSION["COL"]{"ref"} as $k => $value)
        {
            $name .= $value;
        }
        $this->pdf->Output('F', 'render'.$name.'.pdf');
        $this->getRender();
        if(header("location: render".$name.".pdf"))
        {
            echo "<script>alert('The report has been downloaded!');</script>";
        }
        else
        {
            echo "<script>alert('An error occurred during downloading!');</script>";
        }
    } 
    //Obtenir la page html
    public function printPage()
    {
        $this->_html = $this->_head;
        $this->_html .= $this->_header;
        $this->_html .= $this->_section;
        $this->_html .= $this->_script;
        $this->_html .= "
                </body>
            </html>";
        //$this->_html .= $this->_style;
        return $this->_html;
    }

}
?>