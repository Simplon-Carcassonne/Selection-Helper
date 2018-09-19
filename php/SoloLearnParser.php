<?php
//file_get_contents($url) se fait bloquer
/*$url = 'https://www.sololearn.com/Profile/9271485';
$html = file_get_contents($url);
echo $html;*/

// Afficher le code source de la page
//echo htmlentities($result);*/

require 'simple_html_dom.php';


class SoloLearnParser{

    private $agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)';
    private $rootURL = 'https://www.sololearn.com';
    private $url = 'https://www.sololearn.com/Profile/';
    private $id;
    private $userDatas;

    private $debugOn;

    public function __construct($debugOn=false){
        $this->userDatas = [];
        $this->debugOn = $debugOn;
    }

    public function getPageContent($id= "9271485"){
        $this->id = $id;
        $url = $this->url.$this->id;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, $this->agent);//revelant 
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);//revelant
        //curl_setopt($ch, CURLOPT_HEADER, 0);//revelant !! pas de header 
        //curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($ch);

        if(curl_errno($ch))
        {
            return 'Scraper error (cURL): ' . curl_error($ch);
            exit;
        }
        curl_close($ch);

        return $data;
    }

    //based on simple_html_dom.php
    //full documentation : http://simplehtmldom.sourceforge.net/
    //download at : https://github.com/samacs/simple_html_dom
    public function getJSONInfos($rawDatas){

        $html = new simple_html_dom();
        $html->load($rawDatas);

        $this->preparePHPJSON($html);

        if($this->debugOn){
           $this->showDebug();
        }
        else{
            return json_encode($this->userDatas);
        }
    }
    private function preparePHPJSON($html){

        //PSEUDO
        $pseudoElt = $html->find('h1[class=name]');
        $pseudo = $pseudoElt[0]->plaintext;
        $this->userDatas['PSEUDO'] = $pseudo;

        //LEVEL
        $levelElt = $html->find('div[class=detail]');
        $level = $levelElt[0]->first_child ()->plaintext;
        $this->userDatas['LEVEL'] = $level;
                       
        //AVATAR
        $avatarElt = $html->find('div[class=avatar]');
        $avatar = $avatarElt[0]->first_child ()->getAttribute('src');
        $this->userDatas['AVATAR'] = $avatar;

        //COURSES Level
        $this->userDatas['COURSES'] = [];
        $coursesElts = $html->find('a[class=course]');
        foreach($coursesElts as $course){
            $this->userDatas['COURSES'][] = ['CourseName' => $course->getAttribute('title'),
                                             'IconURL' => $this->rootURL.$course->first_child ()->getAttribute('src'),
                                             'Progression' => $course->prev_sibling()->getAttribute('data-percent'),
                                             'XP' => $course->next_sibling()->plaintext];
        }
       
    }

    private function showDebug(){
        echo '<h1>Affichage</h1>';
        //echo $rawDatas; //for debug !!
        echo '<br/> PSEUDO : ';
        echo $this->userDatas['PSEUDO']. "<br/>";
        echo '<br/> LEVEL : ';
        echo $this->userDatas['LEVEL']. "<br/>";
        echo '<br/> AVATAR : ';
        echo "<img src='".$this->userDatas['AVATAR']."'><br/>";
        echo $this->userDatas['AVATAR']. "<br/>";
        echo '<br/> COURSES : ';
        foreach($this->userDatas['COURSES'] as $course){
            echo $course['CourseName'];
            echo "<img src='".$course['IconURL']."'><br/>";
            echo $course['IconURL'];
            echo $course['Progression'];
            echo $course['XP'];
        }

        echo '<br/> JSON to send : ';
        echo json_encode($this->userDatas);
    }
    

    /********** get DOM from HTML  *********/
    //revelant : http://scripthere.com/how-to-scrape-content-from-a-website-using-php/
    //@Deprecated better with simple_html_dom.php Library
    public function getDomFromHTML($rawDatas){
        $scriptDoc = new DOMDocument();
        libxml_use_internal_errors(TRUE); //disable libxml errors
        if(!empty($rawDatas)){
            $scriptDoc->loadHTML($rawDatas);
        }
        libxml_clear_errors();//remove errors for bad HTML
        $scriptXpath = new DOMXPath($scriptDoc);//get DOMxPath
        $scriptRow = $scriptXpath->query('//h2');
        $filtered = $scriptXpath->query("//img[@name='text1']");

        var_dump($filtered);
        if($scriptRow->length > 0){
            foreach($scriptRow as $row){
                echo $row->nodeValue . "<br/>";
            }
        }

        if($filtered->length > 0){
            foreach($filtered as $row){
                echo $row->nodeValue . "<br/>";
            }
        }

    }

}
?>