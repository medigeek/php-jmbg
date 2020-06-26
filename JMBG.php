<?php

namespace MediGeek;

/**
 * Serbian JMBG number (Unique Master Citizen Number, Jedinstveni matični broj građana) number
 * https://www.paragraf.rs/propisi/zakon-o-jedinstvenom-maticnom-broju-gradjana.html
 */

class JMBG {
    
    private $JMBGNumber;
    private $JMBGNumberParsed = [];
    private $JMBGNumberArrayOfCharacters = [];
    
    protected $RegistrationAreaNumber = [
        71 => "Beograd",
        72 => "Aranđelovac, Batočina, Despotovac, Jagodina, Knić, Kragujevac, Lapovo, Paraćin, Rača, Rekovac, Svilajnac, Topola i Ćuprija",
        73 => "Aleksinac, Babušnica, Bela Palanka, Blace, Dimitrovgrad, Doljevac, Gadžin Han, Kuršumlija, Merošina, Niš, Niška Banja, Pirot, Prokuplje, Ražanj, Svrljig i Žitorađa",
        74 => "Bojnik, Bosilegrad, Bujanovac, Crna Trava, Lebane, Leskovac, Medveđa, Preševo, Surdulica, Trgovište, Vladičin Han, Vlasotince i Vranje",
        75 => "Boljevac, Bor, Kladovo, Knjaževac, Majdanpek, Negotin, Soko Banja i Zaječar",
        76 => "Golubac, Kučevo, Malo Crniće, Petrovac na Mlavi, Požarevac, Smederevo, Smederevska Palanka, Velika Plana, Veliko Gradište, Žabari i Žagubica",
        77 => "Bogatić, Koceljeva, Krupanj, Lajkovac, Loznica, Ljig, Ljubovija, Mali Zvornik, Mionica, Osečina, Ub, Valjevo, Vladimirci i Šabac",
        78 => "Aleksandrovac, Brus, Gornji Milanovac, Kraljevo, Kruševac, Lučani, Novi Pazar, Raška, Sjenica, Trstenik, Tutin, Varvarin, Vrnjačka Banja, Ćićevac i Čačak",
        79 => "Arilje, Bajina Bašta, Ivanjica, Kosjerić, Nova Varoš, Požega, Priboj, Prijepolje, Užice i Čajetina",
        80 => "Bač, Bačka Palanka, Bački Petrovac, Beočin, Novi Sad, Sremski Karlovci, Temerin, Titel i Žabalj",
        81 => "Apatin, Odžaci i Sombor",
        82 => "Ada, Bačka Topola, Kanjiža, Kula, Mali Iđoš, Senta i Subotica",
        83 => "Bečej, Srbobran i Vrbas",
        84 => "Kikinda, Nova Crnja, Novi Kneževac i Čoka",
        85 => "Novi Bečej, Sečanj, Zrenjanin i Žitište",
        86 => "Alibunar, Kovačica, Kovin, Opovo i Pančevo",
        87 => "Bela Crkva, Plandište i Vršac",
        88 => "Inđija, Irig, Pećinci, Ruma i Stara Pazova",
        89 => "Sremska Mitrovica i Šid",
        91 => "Glogovac, Kosovo Polje, Lipljan, Novo Brdo, Obilić, Podujevo i Priština",
        92 => "Kosovska Mitrovica, Leposavić, Srbica, Vučitrn, Zubin Potok i Zvečan",
        93 => "Dečani, Istok, Klina i Peć",
        94 => "Đakovica",
        95 => "Dragaš, Gora, Mališevo, Opolje, Orahovac, Prizren i Suva Reka",
        96 => "Kačanik, Uroševac, Štimlje i Štrpce",
        97 => "Gnjilane, Kosovska Kamenica i Vitina."
    ];
    
    
    /**
     * JMBG constructor
     * 
     * @param int $JMBGNumber
     */
    public function __construct($JMBGNumber)
    {
        //$this->validate($JMBGNumber);
        $this->preValidate((string) $JMBGNumber);
        $this->JMBGNumber = (string) $JMBGNumber;
        $this->JMBGNumberArrayOfCharacters = str_split($this->JMBGNumber);
        $this->parse();
    }
    
    /**
     * Pre-Validate JMBG number
     */
    public function preValidate($string)
    {
        $this->preValidateLengthOfChars($string); // must be 13
        $this->preValidateIsNumeric($string);
    }
    
    public function preValidateLengthOfChars($string)
    {
        if (strlen($string) != 13) {
            die("error JMBG not 13 chars\n");
            //return false;
        }
        return true;
    }
    
    public function preValidateIsNumeric($string)
    {
        if (!is_numeric($string)) {
            die("error JMBG not numeric\n");
            //return false;
        }
        return true;
    }
    
    /**
     * Validate JMBG number
     */
    public function validate()
    {
        $this->validateDateOfBirth();
        $this->validateControlNumber();
        var_dump($this->JMBGNumberParsed);
        
        return $this;
    }
    
    public function validateControlNumber()
    {
        /*
            Kontrolna cifra se izračunava sledećom formulom DDMMGGGRRBBBK = ABVGDĐEŽZIJKL
            L = 11 – (( 7*(A+E) + 6*(B+Ž) + 5*(V+Z) + 4*(G+I) + 3*(D+J) + 2*(Đ+K) ) % 11)
            % je MOD ili ostatak deljenja, a ne "/" (znak za deljenje)
            Ako je kontrolna cifra između 1 i 9, ostaje ista (L = K)
            Ako je kontrolna cifra veća od 9, postaje nula (L = 0)
        */
        $i = $this->JMBGNumberArrayOfCharacters;
        $modulo = 11 - (
            ( 
                7 * ($i[0]+$i[6])  + 
                6 * ($i[1]+$i[7])  + 
                5 * ($i[2]+$i[8])  + 
                4 * ($i[3]+$i[9])  + 
                3 * ($i[4]+$i[10]) + 
                2 * ($i[5]+$i[11])
            ) 
            % 11
        );
        
        if ($modulo > 9) {
            $this->JMBGNumberParsed["ControlNumberCalculation"] = "0";
        }
        else {
            $this->JMBGNumberParsed["ControlNumberCalculation"] = $modulo;
        }
        
        $this->JMBGNumberParsed["ControlNumberMatch"] = (
            $this->JMBGNumberParsed["ControlNumberCalculation"] == $this->JMBGNumberParsed["ControlNumber"]
        );
        
        return $this;
    }

    public function validateDateOfBirth()
    {
        $dateNow = date("Y-m-d"); // this date format is string comparable
        $dateNowYear = date("Y");
        $dateNowYearTrimmedFirstInt = substr($dateNowYear, 1);
        
        //we have the 3 last digits for YearOfBirth3digits
        //if the 3 last digits > the current year date (i.e. 985 > 020)
        //assume the first digit is 1 (i.e. 1985)
        if ($this->JMBGNumberParsed['YearOfBirth3digits'] > $dateNowYearTrimmedFirstInt) {
            $this->JMBGNumberParsed['YearOfBirthAssumed'] = sprintf(
                '1%s',
                $this->JMBGNumberParsed['YearOfBirth3digits']
            );
        }
        //if the 3 last digits <= the current year date (i.e. 001 <= 020)
        //assume the first digit is 2 (i.e. 2001)
        else {
            $this->JMBGNumberParsed['YearOfBirthAssumed'] = sprintf(
                '2%s',
                $this->JMBGNumberParsed['YearOfBirth3digits']
            );
        }
        
        $this->JMBGNumberParsed['DateOfBirthAssumed'] = sprintf(
            '%s-%s-%s',
            $this->JMBGNumberParsed['YearOfBirthAssumed'],
            $this->JMBGNumberParsed['MonthOfBirth'],
            $this->JMBGNumberParsed['DayOfBirth']
        );
        
        if (checkdate(
                $this->JMBGNumberParsed['MonthOfBirth'],
                $this->JMBGNumberParsed['DayOfBirth'],
                $this->JMBGNumberParsed['YearOfBirthAssumed']
        )) {
            $this->JMBGNumberParsed["DateOfBirthAssumedValidDate"] = true;
        }
        else {
            die("error Date of Birth not a valid date\n");
            //$this->JMBGNumberParsed["DateOfBirthAssumedValidDate"] = false;
        }
        
        return $this;
    }
    
    /**
     * Parse JMBG number
     */
    public function parse()
    {
        
        /*
            Matični broj se sastoji od 13 cifara koje potiču iz šest grupa podataka, i to: 
            I grupa - dan rođenja (dve cifre);
            II grupa - mesec rođenja (dve cifre);
            III grupa - godina rođenja (tri cifre);
            IV grupa - broj registracionog područja (dve cifre);
            V grupa - kombinacija pola i rednog broja za lica rođena istog datuma (tri cifre), muškarci 000-499, žene 500-999;
            VI grupa - kontrolni broj (jedna cifra). 
        */
        
        //$this->parse();
        //DayOfBirth
        $this->JMBGNumberParsed['DayOfBirth'] = substr($this->JMBGNumber, 0, 2);
        
        //MonthOfBirth
        $this->JMBGNumberParsed['MonthOfBirth'] = substr($this->JMBGNumber, 2, 2);
        
        //YearOfBirth
        $this->JMBGNumberParsed['YearOfBirth3digits'] = substr($this->JMBGNumber, 4, 3);
        
        //Registration area number
        $this->JMBGNumberParsed['RegistrationAreaNumber'] = substr($this->JMBGNumber, 7, 2);
        $this->parseRegistrationArea();
        
        //Serial number (if the baby born was the 1st, 2nd, 3rd... child that day)
        //000-499 male
        //500-999 female
        $this->JMBGNumberParsed['SerialNumber'] = substr($this->JMBGNumber, 9, 3);
        
        //Sex
        $this->parseSex();
        
        //Control number
        $this->JMBGNumberParsed['ControlNumber'] = substr($this->JMBGNumber, 12, 1);
        
        return $this;
    }
    
    public function parseSex() {
        //Sex
        //000-499 male
        //500-999 female
        if ($this->JMBGNumberParsed['SerialNumber'] >= 000 and $this->JMBGNumberParsed['SerialNumber'] <= 499) {
            $this->JMBGNumberParsed['Sex'] = "male";
        }
        elseif ($this->JMBGNumberParsed['SerialNumber'] >= 500 and $this->JMBGNumberParsed['SerialNumber'] <= 999) {
            $this->JMBGNumberParsed['Sex'] = "female";
        }
        else {
            $this->JMBGNumberParsed['Sex'] = null;
            die("error jmbg sex number\n");
        }
        
        return $this;
    }
    
    public function parseRegistrationArea() {
        
        $number = $this->JMBGNumberParsed['RegistrationAreaNumber'];
        $this->JMBGNumberParsed['RegistrationArea'] = $this->RegistrationAreaNumber[$number];
        
        return $this;
    }
    
}

