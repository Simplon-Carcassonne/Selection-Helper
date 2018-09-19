<?php
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

    /**
     * Get the whole page content from the $url
     *
     * @param string $id The profile's id we want to scrap
     * @return string $data raw HTML 
     */ 
    public function getPageContent($id= "9271485"):string{
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

    /**
     * Parse raw HTML , extract exact informations we want, and prepare the JSON return
     * ==> based on simple_html_dom.php
     * full documentation : http://simplehtmldom.sourceforge.net/
     * download at : https://github.com/samacs/simple_html_dom
     *
     * @param string $rawDatas raw HTML we have to parse
     * @return string json_encode($this->userDatas) JSON returned to JS
     */
    public function getJSONInfos($rawDatas):?string{

        $html = new simple_html_dom();
        $html->load($rawDatas);

        $this->preparePHPJSON($html);

        if($this->debugOn){
           $this->showDebug();
           return null;
        }
        else{
            return json_encode($this->userDatas);
        }
    }

    /**
     * Extract informations from DOM and prepare the JSON
     *
     * @param simple_html_dom $html object with HTML parsed to DOM 
     */
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

    /**
     * Render all the informations scrapped and the JSON ready to be sent
     */
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
}
?>