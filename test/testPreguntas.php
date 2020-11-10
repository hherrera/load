<?php

require_once  '../vendor/autoload.php';



$url="http://soylider.sifinca.net/lider/web/app.php/admin/home/question/";
$headers = array(
    'Accept: application/json',
    'Content-Type: application/json',
    'x-login: Username Username="dmejia@araujoysegovia.com", Password="araujo123"'  ,  
);

$q = new api($url, $headers);


 $q->delete();





/*
{
    "question": "Pregunta XXXXXX",
    "hasImage": false,
    "category": {
        "id": 1
    },
    "answers":[{
          "answer": "Answer 1",
          "selected": false
    },{
          "answer": "Answer 2",
          "selected": false
    },{
          "answer": "Answer 3",
          "selected": true
    },{
          "answer": "Answer 4",
          "selected": false
    }]
}
*/