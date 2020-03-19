<?php

namespace Hoangtu92\LaravelLineLogin\Line\API\v2\Response;

class Profile
{
  var $name;
  var $id;
  var $email = null;
  var $pictureUrl;
  var $statusMessage;

  public function __construct(array $data){
    $this->name = $data["name"];
    $this->id = $data["id"];
    $this->pictureUrl = $data["pictureUrl"];
    $this->statusMessage = $data["statusMessage"];
  }

  public function name(){
    return $this->name;
  }

  public function id(){
    return $this->id;
  }

  public function pictureUrl(){
    return $this->pictureUrl;
  }

  public function statusMessage(){
    return $this->statusMessage;
  }
}
