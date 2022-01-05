<?php
/*
* la page fileReader permet d'effectuer les lectures des données dans les fichiers ressources
*                    renseigne les variables sessions des cosmorrows et cosmoleds de references => COMList et COLList
*/

//COSMORROW
    //lire le fichier des propriétés des cosmorrows pour extraire les references,designations,pins,puissances,espacements,noms des presets vers la variable de session COMList
    function readCOMFile()
    {
        //reset la variable session de la liste des cosmorrows
        $_SESSION["COMList"] = null;
        //ouverture du fichier des noms des cosmorrows
        $file = fopen('Resources/COM/speccom.txt', 'r');
        $line = "0";
        $i = 0;
        //remplissage du tableau de caracteristique des cosmorrows (ref, design, pin, pow, spacing)
        while($line != null)
        {
            $line = fgets($file);
            foreach(explode(";", $line) as $k => $value)
            {
                    switch($k)
                    {
                        case 0 : 
                            $_SESSION["COMList"]{"ref"}{$i} = $value;
                        break;
                        case 1 : 
                            $_SESSION["COMList"]{"design"}{$i} = $value;
                        break;
                        case 2 : 
                            $_SESSION["COMList"]{"pin"}{$i} = $value;
                        break;
                        case 3 : 
                            $_SESSION["COMList"]{"pow"}{$i} = $value;
                        break;
                        case 4 : 
                            $_SESSION["COMList"]{"spacing"}{$i} = $value;
                        break;
                        case 5 : 
                            $presets = $value;
                        break;
                        default:
                        break;
                    }
            }
            if($presets != null)
            {
                $presets .= ',';
                //renseigne les presets du cosmorrow
                $_SESSION["COMList"]{"preset"}{$i} = getPresets($presets);
            }
            //renseigne les images 2D et 3D du cosmorrow
            $_SESSION["COMList"]{"img2D"}{$i} = "Resources/COM/".$_SESSION["COMList"]{"ref"}{$i}."2D.png";
            $_SESSION["COMList"]{"img3D"}{$i} = "Resources/COM/".$_SESSION["COMList"]{"ref"}{$i}."3D.png";
            $i++;
        }
        array_pop($_SESSION["COMList"]{"ref"});
        array_pop($_SESSION["COMList"]{"img2D"});
        array_pop($_SESSION["COMList"]{"img3D"});
        fclose($file);
    }
    //renseigne les presets du cosmorrow
    function getPresets(string $presetList)
    {
        $tab = null;
        foreach(explode(",", $presetList) as $i => $presetName)
        {
            $presNme = null;
            $presNme{0} = $presetName;
            if($presetName != null and $presetName != '')
            {
                $preset = null;
                //lecture du fichier d'un preset d'un cosmorrow
                $file = fopen("Resources/COM/".$presetName.".pre", 'r');
                $line = fgets($file);
                //recupere le tableau des references des cosmoleds d'un preset d'un cosmorrow
                foreach(explode(",", $line) as $k => $refCOL)
                {
                    $preset{$k} = $refCOL;
                }
                
                $presNme{1} = $preset;
            }
            //var_dump("presNme", $presNme);
            $tab{$i} = $presNme;
        }
        array_pop($tab);
        //var_dump("tab", $tab);
        return $tab;
    }

//COSMOLED
    //lire le fichier des propriétés des cosmoleds pour extraire leurs noms
    function readCOLFile()
    {
        //reset variable session de la liste des cosmoleds
        unset($_SESSION["COLList"]);
        //ouverture du fichier des noms des cosmoleds
        $file = fopen('Resources/COL/speccol.txt', 'r');

        //remplissage du tableau de caracteristique des cosmoleds
        $tab;
        $line = "0";
        $i = 0;
        while($line != null)
        {
            //lecture du fichier
            $line = fgets($file);
            $tab{$i} = trim($line, " \t\n\r\0\x0B");
            $i += 1;
        }

        array_pop($tab);
        //obtenir les proprietes des cosmoleds
        getCOLFiles($tab);
        fclose($file);
    }
    //lire le fichiers des propriétés des cosmoleds pour extraire les references,designations,puissances,flux de photons, efficatitee photonique, spectres d'absorbtions vers la variable de session COLList
    function getCOLFiles(array $files)
    {
        $tabCOL = array();
        foreach($files as $key => $value)   
        {
            //obtenir les noms de fichiers de proprietes des cosmoleds
            $fileName = "Resources/COL/".trim($value, " \t\n\r\0\x0B").".txt";
            $file = fopen($fileName,'r');
            $line = "0";
            $valWav = array();

            //affectation des proprietes dans la variable tabCOL des données : (ref,design,pow,PF,PE)
            $line = fgets($file);
            $tabCOL{"ref"}{$key} = trim($line, " \t\n\r\0\x0B");
            $line = fgets($file);
            $tabCOL{"design"}{$key} = trim($line, " \t\n\r\0\x0B");
            $line = fgets($file);
            $tabCOL{"pow"}{$key} = trim($line, " \t\n\r\0\x0B");
            $line = fgets($file);
            $tabCOL{"PF"}{$key} = trim($line, " \t\n\r\0\x0B");
            $line = fgets($file);
            $tabCOL{"PE"}{$key} = trim($line, " \t\n\r\0\x0B");
            $line = fgets($file);
            $tabCOL{"img"}{$key} = "Resources/COL/".trim($tabCOL{"ref"}{$key}, " \t\n\r\0\x0B").".png";
            $valWav = explode(";", trim($line, " \t\n\r\0\x0B"));
            $spectr = null;
        
            //affectation des proprietes dans la variable tabCOL des données : (spectrum(waveLength,irradiance))
            foreach($valWav as $k => $val) 
            {
                if($val != null && $val != "")
                {
                    $spectr{"waveLength"} = null;
                    $spectr{"irradiance"} = null;
                    List($spectr{"waveLength"}{$k}, $spectr{"irradiance"}{$k}) = explode(",", $val);

                    if($spectr{"waveLength"}{$k} != null)
                    {
                        $tabCOL{"spectrum"}{$key}{"waveLength"}{$k} = $spectr{"waveLength"}{$k};
                    }
    
                    if($spectr{"irradiance"}{$k} != null)
                    {
                        $tabCOL{"spectrum"}{$key}{"irradiance"}{$k} = $spectr{"irradiance"}{$k};
                    }
                }
            }
            fclose($file);
        }
        $_SESSION["COLList"] = $tabCOL;
    }
?>


<!--
    //renseigne la variable de session COL
    function setCOL(array $refCOLTab)
    {
        //refixe la variable session de la liste des cosmoleds selectionnées
        unset($_SESSION["COL"]);
        foreach($refCOLTab as $k => $val)
        {
            foreach($_SESSION["COLList"]{"ref"} as $key => $value)
            {
                //on associe les valeurs du repertoire dans la variable de session COL pour chaque cosmoleds selectionnées
                if($refCOLTab{$k} == $_SESSION["COLList"]{"ref"}{$key})
                {
                    $_SESSION["COL"]{"ref"}{$k} = $_SESSION["COLList"]{"ref"}{$key};
                    $_SESSION["COL"]{"design"}{$k} = $_SESSION["COLList"]{"design"}{$key};
                    $_SESSION["COL"]{"pow"}{$k} = $_SESSION["COLList"]{"pow"}{$key};
                    $_SESSION["COL"]{"PF"}{$k} = $_SESSION["COLList"]{"PF"}{$key};
                    $_SESSION["COL"]{"PE"}{$k} = $_SESSION["COLList"]{"PE"}{$key};
                    foreach($_SESSION["COLList"]{"spectrum"}{$key}{"waveLength"} as $num => $wav)
                    {
                        $_SESSION["COL"]{"spectrum"}{$k}{"waveLength"}{$num} = $wav;
                    }
                    foreach($_SESSION["COLList"]{"spectrum"}{$key}{"irradiance"} as $num => $intVal)
                    {
                        $_SESSION["COL"]{"spectrum"}{$k}{"irradiance"}{$num} = $intVal;
                    }
                    $_SESSION["COL"]{"img"}{$k} = $_SESSION["COLList"]{"img"}{$key};
                }
                elseif(($refCOLTab{$k} == null) or ($refCOLTab{$k} == "none") or ($refCOLTab{$k} == "None"))
                {
                    $_SESSION["COL"]{"ref"}{$k} = "None";
                    $_SESSION["COL"]{"design"}{$k} = "None";
                    $_SESSION["COL"]{"pow"}{$k} = 0;
                    $_SESSION["COL"]{"PF"}{$k} = 0;
                    $_SESSION["COL"]{"PE"}{$k} = 0;
                    $_SESSION["COL"]{"spectrum"}{$k}{"waveLength"} = 0;
                    $_SESSION["COL"]{"spectrum"}{$k}{"irradiance"} = 0;
                    $_SESSION["COL"]{"img"}{$k} = "";
                }
            }
        }
    }
    
    //renseigne la variable de session COM
    function setCOM(String $refCOM)
    {
        foreach($_SESSION["COMList"]{"ref"} as $key => $value)
        {
            if($refCOM == $_SESSION["COMList"]{"ref"}{$key})
            {
                $_SESSION["COM"]{"ref"} = $_SESSION["COMList"]{"ref"}{$key};
                $_SESSION["COM"]{"design"} = $_SESSION["COMList"]{"design"}{$key};
                $_SESSION["COM"]{"pin"} = $_SESSION["COMList"]{"pin"}{$key};
                $_SESSION["COM"]{"pow"} = $_SESSION["COMList"]{"pow"}{$key};
                $_SESSION["COM"]{"spacing"} = $_SESSION["COMList"]{"spacing"}{$key};
                $_SESSION["COM"]{"img3D"} = $_SESSION["COMList"]{"img3D"}{$key};
                $_SESSION["COM"]{"img2D"} = $_SESSION["COMList"]{"img2D"}{$key};
                $_SESSION["COM"]{"preset"} = $_SESSION["COMList"]{"preset"}{$key};
            }
        }
    }
    -->