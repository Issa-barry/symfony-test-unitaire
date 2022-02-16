<?php

namespace App\Tests\Entity\tools;



trait AssertEntityTrait 
{
  public function assertHasErrors($entity, int $numberErrors=0, string $property=null, string $message=null):void  
  {
   $kernel = self::bootKernel();
   $container = static::getContainer();
   $errors = $container->get('validator')->validate($entity);

   $debugMessages = [];
   $errorMessages = [];
   $errorFields   = [];
   foreach ($errors as $error) {
      $debugMessages[] = "Property: '".$error->getPropertyPath()."' has value '".
      $error->getMessage()."'";
      $errorMessages[] = $error->getMessage();
      $errorFields[] = $error->getPropertyPath();
   }
    $this->assertCount($numberErrors, $errors, implode(",", $debugMessages));
    if(!is_null($property) && !is_null($message)){
      $this->assertContains($property, $errorFields, implode(",", $debugMessages));
      $this->assertContains($message, $errorMessages, implode(",", $debugMessages));

   }

  }
  
}
