<?php
namespace MediGeek;

/**
 * Serbian JMBG number (Unique Master Citizen Number, Jedinstveni matični broj građana) number
 * https://www.paragraf.rs/propisi/zakon-o-jedinstvenom-maticnom-broju-gradjana.html
 */


class Person {
    private string $JMBGNumber;
    private array $JMBGNumberArrayOfCharacters;
    private string $DayOfBirth;
    private string $MonthOfBirth;
    private string $YearOfBirthLastThreeDigits;
    private string $RegistrationAreaNumber;
    private string $RegistrationArea;
    private string $BirthSerialNumber;
    private string $BirthSerialNumberDescriptive;
    private string $Sex;
    private string $ControlNumber;
    private string $YearOfBirthAssumed;
    private string $DateOfBirthAssumed;
    private bool $DateOfBirthAssumedValidDate;
    private int $ControlNumberCalculated;
    private bool $ControlNumberMatch;
    
    public function get(string $key, string $returntype = "array") {
        
        $tmpString = $this->$key;
        
        if ($returntype == "array") {
            return $tmpString;
        }
        elseif ($returntype == "json") {
            $jsonArray = json_encode($tmpString);
            return $jsonArray;
        }
    }
    
    public function set(string $key, $value) {
        $this->$key = $value;
        return true;
    }
    
    public function getMultiple(array $keys, string $returntype = "array") {
        $tmpArray = [];
        foreach ($keys as $key) {
            $tmpArray[] = $this->$key;
        }
        
        if ($returntype == "array") {
            return $tmpArray;
        }
        elseif ($returntype == "json") {
            $jsonArray = json_encode($tmpArray);
            return $jsonArray;
        }
    }
    
    public function setMultiple(array $keyValuePairs) {
        foreach ($keyValuePairs as $key => $value) {
            $this->$key = $this->$value;
        }
        
    }
    
    public function getAll(string $returntype = "array") {
        $keyValuePairs = get_object_vars($this);
        
        if ($returntype == "array") {
            return $keyValuePairs;
        }
        elseif ($returntype == "json") {
            $KeyValuePairsJSON = json_encode($keyValuePairs);
            return $KeyValuePairsJSON;
        }
    }
    
}

class JMBG {
    
    private $Person;
    
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
        $this->preValidate($JMBGNumber);
        $this->Person = new Person();
        $this->Person->set("JMBGNumber", $JMBGNumber);
        $this->Person->set("JMBGNumberArrayOfCharacters", str_split($JMBGNumber));
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
        
        return $this;
    }
    
    /**
     * Validate JMBG number
     */
    public function getParsed(string $returntype = "array")
    {
        return $this->Person->getAll($returntype);
    }
    
    /**
     * Validate Control Number
     */
    public function validateControlNumber()
    {
        /*
            Kontrolna cifra se izračunava sledećom formulom DDMMGGGRRBBBK = ABVGDĐEŽZIJKL
            L = 11 – (( 7*(A+E) + 6*(B+Ž) + 5*(V+Z) + 4*(G+I) + 3*(D+J) + 2*(Đ+K) ) % 11)
            % je MOD ili ostatak deljenja, a ne "/" (znak za deljenje)
            Ako je kontrolna cifra između 1 i 9, ostaje ista (L = K)
            Ako je kontrolna cifra veća od 9, postaje nula (L = 0)
            (?? Ili matičar pomera reg broj za jedan kako bi modulo bi jednocifren?)
        */
        
        
        $i = $this->Person->get("JMBGNumberArrayOfCharacters");
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
            $this->Person->set("ControlNumberCalculated", "0");
        }
        else {
            $this->Person->set("ControlNumberCalculated", $modulo);
        }
        
        $this->Person->set("ControlNumberMatch", (
            $this->Person->get("ControlNumberCalculated") == $this->Person->get("ControlNumber")
        ));
        
        return $this;
    }

    public function validateDateOfBirth()
    {
        $dateNow = date("Y-m-d"); // this date format is string comparable
        $dateNowYear = date("Y");
        $dateNowYearTrimmedFirstInt = substr($dateNowYear, 1);
        
        //we have the 3 last digits for YearOfBirthLastThreeDigits
        //if the 3 last digits > the current year date (i.e. 985 > 020)
        //assume the first digit is 1 (i.e. 1985)
        if ($this->Person->get("YearOfBirthLastThreeDigits") > $dateNowYearTrimmedFirstInt) {
            $this->Person->set("YearOfBirthAssumed", sprintf(
                '1%s',
                $this->Person->get("YearOfBirthLastThreeDigits")
            ));
        }
        //if the 3 last digits <= the current year date (i.e. 001 <= 020)
        //assume the first digit is 2 (i.e. 2001)
        else {
            $this->Person->set("YearOfBirthAssumed", sprintf(
                '2%s',
                $this->Person->get("YearOfBirthLastThreeDigits")
            ));
        }
        
        $this->Person->set("DateOfBirthAssumed", sprintf(
            '%s-%s-%s',
            $this->Person->get("YearOfBirthAssumed"),
            $this->Person->get("MonthOfBirth"),
            $this->Person->get("DayOfBirth")
        ));
        
        if (checkdate(
                $this->Person->get("MonthOfBirth"),
                $this->Person->get("DayOfBirth"),
                $this->Person->get("YearOfBirthAssumed")
        )) {
            $this->Person->set("DateOfBirthAssumedValidDate", true);
        }
        else {
            die("error Date of Birth not a valid date\n");
            //$this->Person->set("DateOfBirthAssumedValidDate", true);
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
        $this->Person->set("DayOfBirth", substr($this->Person->get("JMBGNumber"), 0, 2));
        
        //MonthOfBirth
        $this->Person->set("MonthOfBirth", substr($this->Person->get("JMBGNumber"), 2, 2));
        
        //YearOfBirth
        $this->Person->set("YearOfBirthLastThreeDigits", substr($this->Person->get("JMBGNumber"), 4, 3));
        
        //Registration area number
        $this->Person->set("RegistrationAreaNumber", substr($this->Person->get("JMBGNumber"), 7, 2));
        $this->parseRegistrationArea();
        
        //Serial number (if the baby born was the 1st, 2nd, 3rd... child that day)
        //000-499 male
        //500-999 female
        $this->Person->set("BirthSerialNumber", substr($this->Person->get("JMBGNumber"), 9, 3));
        
        //Sex
        $this->parseSex();
        
        //BirthSerialNumberDescriptive
        $this->parseBirthSerialNumber();
        
        //Control number
        $this->Person->set("ControlNumber", substr($this->Person->get("JMBGNumber"), 12, 1));
        
        return $this;
    }
    
    public function parseBirthSerialNumber() {
        //Serial number (if the baby born was the 1st, 2nd, 3rd... child that day)
        //000-499 male
        //500-999 female
        $sex = $this->Person->get("Sex");
        if ($sex == "male") {
            $number = (int) ($this->Person->get("BirthSerialNumber") + 1);
        }
        elseif ($sex == "female") {
            $number = (int) ($this->Person->get("BirthSerialNumber") - 500 + 1);
        }
        $numberWithSuffix = $this->addOrdinalNumberSuffix($number);
        $this->Person->set("BirthSerialNumberDescriptive", sprintf(
            '%s %s child born on that day',
            $numberWithSuffix,
            $sex
        ));
        
        return $this;
    }
    
    public function addOrdinalNumberSuffix(int $number) {
        if (!in_array(($number % 100), array(11,12,13))) {
            switch ($number % 10) {
                case 1:  return $number.'st';
                case 2:  return $number.'nd';
                case 3:  return $number.'rd';
            }
        }
        
        return $number.'th';
    }
    
    public function parseSex() {
        //Sex
        //000-499 male
        //500-999 female
        if ($this->Person->get("BirthSerialNumber") >= 000 and $this->Person->get("BirthSerialNumber") <= 499) {
            $this->Person->set("Sex", "male");
        }
        elseif ($this->Person->get("BirthSerialNumber") >= 500 and $this->Person->get("BirthSerialNumber") <= 999) {
            $this->Person->set("Sex", "female");
        }
        else {
            $this->Person->set("Sex", null);
            die("error jmbg sex number\n");
        }
        
        return $this;
    }
    
    public function parseRegistrationArea() {
        
        $number = $this->Person->get("RegistrationAreaNumber");
        $this->Person->set("RegistrationArea", $this->RegistrationAreaNumber[$number]);
        
        return $this;
    }
    
}

