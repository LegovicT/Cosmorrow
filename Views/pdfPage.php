<?php
/*
* la classe PDFPage permet de créer un pdf avec des données transmises
*                   génère le pdf telechargable du rendu 
*/

//inclusion de la classe qui gere la creation d'un pdf
include 'Resources/fpdf182/draw.php';

class PDFPage extends PDF_Draw
{
//VARIABLES
    protected   $B = 0;
    protected   $I = 0;
    protected   $U = 0;
    protected   $HREF = '';
    protected   $_cosmorrow;                                            //nom du cosmorrow
    protected   $_designCOM;                                            //designation du cosmorrow
    protected   $_cosmoleds;                                            //tableau des noms des cosmoleds
    protected   $_designCOL;                                            //tableau des designation des cosmoleds
    protected   $_COLref;                                               //ref des cosmoleds separes par des ', '
    protected   $_COLdesign;                                               //design des cosmoleds separes par des ', '
    protected   $_conso;                                                //texte de consommation en w
    protected   $_pe;                                                   //texte de l'efficatite photonique
    protected   $_pf;                                                   //texte du flux lumineux
    protected $_spectrum = array();                                            //tableau 1 du spectre d'arbsorbtion
    protected   $_spectrum2;                                            //tableau 2 du spectre d'arbsorbtion
    protected   $_spectrum3;                                            //tableau 3 du spectre d'arbsorbtion
    protected   $_spectrum4;                                            //tableau 4 du spectre d'arbsorbtion
    protected   $_spectrum5;                                            //tableau 5 du spectre d'arbsorbtion
    protected   $_spectrum6;                                            //tableau 6 du spectre d'arbsorbtion
    protected   $_spectrum7;                                            //tableau 7 du spectre d'arbsorbtion
    protected   $_spectrum8;                                            //tableau 8 du spectre d'arbsorbtion
    protected   $_spectrum9;                                            //tableau 1 du spectre d'arbsorbtion
    protected   $_spectrum10;                                            //tableau 2 du spectre d'arbsorbtion
    protected   $_spectrum11;                                            //tableau 3 du spectre d'arbsorbtion
    protected   $_spectrum12;                                            //tableau 4 du spectre d'arbsorbtion
    protected   $_spectrum13;                                            //tableau 5 du spectre d'arbsorbtion
    protected   $_spectrum14;                                            //tableau 6 du spectre d'arbsorbtion
    protected   $_spectrum15;                                            //tableau 7 du spectre d'arbsorbtion
    protected   $_spectrum16;                                            //tableau 8 du spectre d'arbsorbtion
    protected   $_imageCOM;                                             //image du cosmorrow avec les cosmoleds montées dessus
    protected   $_imageCOL = array();                                   //images des cosmoleds
    protected   $_linkSJWeb = 'https://www.secretjardin.com';           //lien vers le site secret jardin
    protected   $_linkCosmoWeb = 'http://localhost/Cosmorrow/main.php'; //lien vers le site cosmorrow

//GETTERS
    // En-tête
    function Header()
    {
        // Police Arial gras 22
        $this->SetFont('Arial','B',22);
        // Calcul de la largeur du titre et positionnement
        $w = $this->GetStringWidth('COSMORROW')+6;
        //$this->SetX(($w)/2);
        // Couleurs du cadre, du fond et du texte
        $this->SetDrawColor(220,170,0);
        $this->SetFillColor(20,20,20);
        $this->SetTextColor(220,170,0);
        // Epaisseur du cadre (1 mm)
        $this->SetLineWidth(1);
        // Décalage à droite
        $this->Cell(190,20,'',1,1,'C',true);
        // Titre
        $this->WriteHTML(180,20,'<a href="'.$this->_linkCosmoWeb.'">COSMORROW</a>','C');
        // Logo
        $this->Image('Resources/pics/SJhome.png',12,12,30,0,'',$this->_linkSJWeb);
        // Saut de ligne
        $this->Ln(10);
    }
    // Corps
    function Body()
    {
        // 1er conteneur
            $this->SetLineWidth(1);
            $this->SetFillColor(20,20,20);
            $this->SetDrawColor(220,170,0);
            $this->SetTextColor(220,170,0);
            $this->SetXY(5,40);
            $this->Cell(200,120,'',1,1,'',true);
            $this->SetXY(0,40);
            $this->SetLineWidth(0.5);
            $this->SetDrawColor(220,170,0);
            $this->SetFillColor(20,20,20);
            $this->SetTextColor(220,170,0);
            $this->SetFont('Arial','B',16);
            $this->Cell(210,10,'SPECIFICATIONS OVERVIEW',1,1,'C',true);
            $this->SetFont('Arial','',12);
            $this->SetLineWidth(0.25);
            $this->SetXY(5,50);
            $this->Cell(42,10,'Energy consumption: ',1,1,'');
            $this->SetXY(47,50);
            $this->Cell(23,10,$this->_conso.' W',1,1,'');
            $this->SetXY(70,50);
            $this->Cell(40,10,'Photonic Efficiency: ',1,1,'');
            $this->SetXY(110,50);
            $this->Cell(30,10,$this->_pe.' '.chr(0xB5).'mol.s/W',1,1,'');
            $this->SetXY(140,50);
            $this->Cell(35,10,'Photon Flux: ',1,1,'');
            $this->SetXY(175,50);
            $this->Cell(30,10,$this->_pf.' '.chr(181).'mol/s',1,1,'');
            $this->SetXY(5,60);
            $this->SetLineWidth(0.25);
            $this->Cell(200,100,'',1,1,'');
            $this->SetFillColor(20,220,220);
            $style6 = array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => '10,10', 'color' => array(250, 255, 0));
            //$this->Curve(5, 160, 101, 20, 102.5, 60, 205, 160, 'F', $style6);
            //$this->Polygon(array(5, 160, 7, 160, 9, 160, 11, 155, 13, 136), 'F', array('all' => $style6), array(0, 250, 0));
            $spectrum = array();
            $spectrum = $_SESSION["Spectrum"];
            //var_dump($_SESSION["Spectrum"]);
            //http://www.fpdf.org/fr/script/script72.php
            //generation des couleurs
            $colorBK=array('r'=>0,'g'=>0,'b'=>0);
            $colorPP=array('r'=>155,'g'=>0,'b'=>240);
            $colorBL=array('r'=>0,'g'=>0,'b'=>255);
            $colorCN=array('r'=>12,'g'=>250,'b'=>250);
            $colorGN=array('r'=>0,'g'=>255,'b'=>0);
            $colorYL=array('r'=>255,'g'=>255,'b'=>0);
            $colorRD=array('r'=>255,'g'=>0,'b'=>0);
            $coords=array(0,0,1,0);
            $this->LinearGradient(5,60,15,100,$colorBK,$colorPP,$coords);
            $this->LinearGradient(20,60,25,100,$colorPP,$colorBL,$coords);
            $this->LinearGradient(45,60,25,100,$colorBL,$colorCN,$coords);
            $this->LinearGradient(70,60,25,100,$colorCN,$colorGN,$coords);
            $this->LinearGradient(95,60,30,100,$colorGN,$colorYL,$coords);
            $this->LinearGradient(125,60,45,100,$colorYL,$colorRD,$coords);
            $this->LinearGradient(170,60,35,100,$colorRD,$colorBK,$coords);
            $this->Polygon($spectrum, 'F', array('all' => null), array(0, 0, 0));
            $this->SetXY(5,60);
            $this->SetLineWidth(0.0);
            $this->SetFont('Arial','B',12);
            $this->Cell(20,10,'Spectrum:',0,1,'C');
            //legende
            $this->SetFont('Arial','B',9);
            $this->SetTextColor(20,20,20);
            $this->SetXY(4.5,160);
            $this->Cell(1,1,'l',0,1,'C');
            $this->SetXY(5,163);
            $this->Cell(1,1,'350',0,1,'C');
            $this->SetXY(29.5,160);
            $this->Cell(1,1,'l',0,1,'C');
            $this->SetXY(30,163);
            $this->Cell(1,1,'400',0,1,'C');
            $this->SetXY(54.5,160);
            $this->Cell(1,1,'l',0,1,'C');
            $this->SetXY(55,163);
            $this->Cell(1,1,'450',0,1,'C');
            $this->SetXY(79.5,160);
            $this->Cell(1,1,'l',0,1,'C');
            $this->SetXY(80,163);
            $this->Cell(1,1,'500',0,1,'C');
            $this->SetXY(104.5,160);
            $this->Cell(1,1,'l',0,1,'C');
            $this->SetXY(105,163);
            $this->Cell(1,1,'550',0,1,'C');
            $this->SetXY(129.5,160);
            $this->Cell(1,1,'l',0,1,'C');
            $this->SetXY(130,163);
            $this->Cell(1,1,'600',0,1,'C');
            $this->SetXY(154.5,160);
            $this->Cell(1,1,'l',0,1,'C');
            $this->SetXY(155,163);
            $this->Cell(1,1,'650',0,1,'C');
            $this->SetXY(179.5,160);
            $this->Cell(1,1,'l',0,1,'C');
            $this->SetXY(180,163);
            $this->Cell(1,1,'700',0,1,'C');
            $this->SetXY(204.5,160);
            $this->Cell(1,1,'l',0,1,'C');
            $this->SetXY(205,163);
            $this->Cell(1,1,'750',0,1,'C');

        // 2nd conteneur
            $this->SetFillColor(20,20,20);
            $this->SetLineWidth(0.25);
            $this->SetDrawColor(220,170,0);
            $this->SetTextColor(220,170,0);
            $this->SetXY(5,165);
            $this->Cell(200,110,'',1,1,'',true);
            $this->SetLineWidth(0.5);
            $this->SetDrawColor(220,170,0);
            $this->SetFillColor(20,20,20);
            $this->SetTextColor(220,170,0);
            $this->SetFont('Arial','B',16);
            $this->SetXY(0,165);
            $this->Cell(210,10,'PRODUCT OVERVIEW',1,1,'C',true);
            // Cosmorrow result
            $this->SetXY(10,180);
            $this->SetFont('Arial','B',12);
            $this->Write(1,"COSMORROW : ");
            $this->SetXY(10,190);
            $this->SetFont('Arial','',10);
            $this->Write(1,"REFERENCE : ".$_SESSION["COM"]{"ref"});
            $this->SetXY(10,200);
            $this->Write(1,"DESIGNATION : ".$_SESSION["COM"]{"design"});
            $this->SetXY(100,180);
            $this->Image($_SESSION["COM"]{"img3D"},160,175,30,0);
            // Cosmoleds result
            $this->SetFont('Arial','',9);
            $i = $_SESSION["COM"]{"pin"};
            $columnNbr;
            $rowNbr;
            $imgSize;
            switch($i)
            {
                case 1:
                    $columnNbr = 1;
                    $rowNbr = 1;
                    $imgSize = 65 / 4;
                break;
                case 2:
                    $columnNbr = 1;
                    $rowNbr = 2;
                    $imgSize = 65 / 4;
                break;
                case 4:
                    $columnNbr = 2;
                    $rowNbr = 2;
                    $imgSize = 65 / 4;
                break;
                case 6:
                    $columnNbr = 2;
                    $rowNbr = 3;
                    $imgSize = 65 / 6;
                break;
                case 8:
                    $columnNbr = 2;
                    $rowNbr = 4;
                    $imgSize = 65 / 8;
                break;
                case 12:
                    $columnNbr = 3;
                    $rowNbr = 4;
                    $imgSize = 65 / 12;
                break;
                case 16:
                    $columnNbr = 4;
                    $rowNbr = 4;
                    $imgSize = 65 / 16;
                break;
                case 24:
                    $columnNbr = 4;
                    $rowNbr = 6;
                    $imgSize = 65 / 24;
                break;
                case 32:
                    $columnNbr = 4;
                    $rowNbr = 8;
                    $imgSize = 65 / 32;
                break;
                default:
                    $columnNbr = 1;
                    $rowNbr = 1;
                    $imgSize = 65 / 4;
                break;
            }
            $cln=0;
            $row=0;
            foreach($_SESSION["COL"]{"ref"} as $k => $ref)
            {
                $varX = 5 + (200 / $columnNbr * $cln);
                $varY = 210 + (65 / $rowNbr * $row);
                $varW = 200 / $columnNbr;
                $varH = 65 / $rowNbr;
                //var_dump(($k+1)."=(".$varX.",".$varY.",".$varW.",".$varH.")");
                $this->SetXY($varX, $varY);
                $this->Write(1, "Led ".($k+1)."/".$_SESSION["COM"]{"pin"}." : ".$ref);
                //$this->SetXY($varX+($varW/2), $varY);
                $this->SetXY($varX, $varY);
                $this->Rotate(90);
                $this->Image($_SESSION["COL"]{"img"}{$k}, ($varX + 2 - ($imgSize * 2)), ($varY + 2), $imgSize, 0);
                //$this->Image($_SESSION["COL"]{"img"}{$k}, ($varX + $varW), ($varY + $varH), ($varH / 2), 0);
                $this->Rotate(0);
                $cln++;
                if($cln == $columnNbr)
                {
                    $row++;
                    $cln=0;
                }
            }
            //$this->Image($_SESSION["COL"]{"img"}{0},12,12,30,0);
        }
    // Pied de page
    function Footer()
    {
        // Positionnement à 2cm du bas
        $this->SetY(-40);
        $this->SetX(0);
        // Police Arial italique 8
        //$this->SetFont('Arial','G',12);
        // Couleur du texte en gris
        $this->SetTextColor(128);
        // Indication du matteriel selectionne
        $this->MultiCell(10,185,'Rendu de = (COSMORROW:'.$this->_cosmorrow.'
        COSMOLEDS:'.$this->_COLref.')',0,1);
    }

// Interpreteur html
    function WriteHTML($x,$y,$html,$pos)
    {
        // Parseur HTML
        $html = str_replace("\n",' ',$html);
        $a = preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
        foreach($a as $i=>$e)
        {
            if($i%2==0)
            {
                // Texte
                if($this->HREF)
                    $this->PutLink($x,$y,$this->HREF,$e,$pos);
                else
                    $this->Write(5,$e);
            }
            else
            {
                // Balise
                if($e[0]=='/')
                    $this->CloseTag(strtoupper(substr($e,1)));
                else
                {
                    // Extraction des attributs
                    $a2 = explode(' ',$e);
                    $tag = strtoupper(array_shift($a2));
                    $attr = array();
                    foreach($a2 as $v)
                    {
                        if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
                            $attr[strtoupper($a3[1])] = $a3[2];
                    }
                    $this->OpenTag($tag,$attr);
                }
            }
        }
    }
    function OpenTag($tag, $attr)
    {
        // Balise ouvrante
        if($tag=='B' || $tag=='I' || $tag=='U')
            $this->SetStyle($tag,true);
        if($tag=='A')
            $this->HREF = $attr['HREF'];
        if($tag=='BR')
            $this->Ln(5);
    }
    function CloseTag($tag)
    {
        // Balise fermante
        if($tag=='B' || $tag=='I' || $tag=='U')
            $this->SetStyle($tag,false);
        if($tag=='A')
            $this->HREF = '';
    }
    function SetStyle($tag, $enable)
    {
        // Modifie le style et sélectionne la police correspondante
        $this->$tag += ($enable ? 1 : -1);
        $style = '';
        foreach(array('B', 'I', 'U') as $s)
        {
            if($this->$s>0)
                $style .= $s;
        }
        $this->SetFont('',$style);
    }
    function PutLink($x, $y, $URL, $txt, $pos)
    {
        // Place un hyperlien
        $this->SetXY($x-6,$y-6);
        $this->SetFont('Arial','B',20);
        $this->SetDrawColor(220,170,0);
        $this->SetFillColor(20,20,20);
        $this->SetTextColor(220,170,0);
        $this->SetLineWidth(1);
        $this->SetStyle('B',true);
        $this->SetX($x);
        $this->SetLeftMargin(($x-6)/2);
        $this->Write(5,$txt,$URL);
        $this->SetStyle('B',false);
        $this->SetTextColor(0);
    }

//SETTERS
    // Renseigne le cosmorrow et les cosmoleds utilisés
    function setMatterial(string $cosmorrow, array $cosmoleds)
    {
        $this->_cosmorrow = $cosmorrow;
        $this->_cosmoleds = $cosmoleds;
        $this->_COLref = '';
        $this->_COLdesign = '';
        foreach($this->_cosmoleds as $i => $col)
        {
            $this->_COLref .= $col.', ';
            $this->_COLdesign .= $_SESSION["COL"]{"design"}{$i}.', ';
        }
        substr($this->_COLref,2,-2);
        substr($this->_COLdesign,2,-2);
    }
    // Renseigne le 1er cadre
    function setParameters(string $conso, string $pe, string $pf)
    {
        $this->_conso = $conso;
        $this->_pe = $pe;
        $this->_pf = $pf;
    }
    // Renseigne le spectre lumineux
    function setSpectrum(array $spectrum, float $maxIrrad)
    {
        array_push($this->_spectrum, 4.5, 60);
        foreach($spectrum as $waveLength => $irradiance)
        {
            //$this->_spectrum{($waveLength-350)/2+5} = (160 - $irradiance*100/$maxIrrad);
            if($waveLength == 350)
            {
                $wl = 5;
            }
            else
            {
                $wl = ($waveLength-350)/2+5;
            }
            if($irradiance == 0)
            {
                $ir = 160;
            }
            else
            {
                $ir = 160 - ($irradiance * 100 / $maxIrrad);
            }
            
            //array_push($this->_spectrum, intval(round($wl, 0)), intval(round($ir, 0)));
            array_push($this->_spectrum, round($wl, 1), round($ir, 1));
        }
        array_push($this->_spectrum, 205.5, 60);
        $_SESSION["Spectrum"] = $this->_spectrum;
    }
}
?>