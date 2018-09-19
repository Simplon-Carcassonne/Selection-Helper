# Selection-Helper
Mini application permettant d'automatiser la récupération de données ainsi que leur traitement

## Formulaire d'inscription pour la pré-sélection
- Le dossier Frontend contient la logique JS/HTML avec Vue JS (via CDN) du formulaire
<br/><br/>
- Le script formRegister.js enverra les datas du formulaire au script php côté backend (registerEntryPoint.php).
<br/>A terme le script php peut être chargé de persister les données en BDD ou dans un fichier

## Récupérer les données issues de sololearn
- Les scripts utiles sont dans le dossier /php. Le point d'entrée est le fichier ajaxEntryPoint.php qui attend une variable POST nommée 'id' qui correspond à l'id du profile sur SoloLearn (ie: https://www.sololearn.com/Profile/9271485)
<br/><br/>
- Le parser utilise une librairie simple_html_dom.php afin de faciliter la lecture en php du DOM HTML récupéré.
<br/><br/>
- Il y a un mode debug qui permet de tester juste la page php sans envoyer de variable POST. Pour cela il faut créer l'objet passer la variable $debugOn à true dans le fichier ajaxEntryPoint.php
<br/> Avec le mode debug activé on voit le résultat visuel scrappé, ainsi que le json renvoyé. Par défault ce maode est activé !!
<br/>Pour tester la validité du JSON, on peut utiliser : https://jsonformatter.curiousconcept.com/
