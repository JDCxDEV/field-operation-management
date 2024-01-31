<?php
namespace App\Traits;

trait CoordinatorTrait 
{

  /**
   * Get Last login of coordinator
   * 
   * @return string
   */
  public function latestLogin() 
  {
    $model = get_class($this) == "App\\Models\\Company" ? $this->coordinators() : $this->directors();
    $lastLogin = $model->where("last_login", "!=", null)->latest("last_login")->first();
    if($lastLogin) {
      return $lastLogin->last_login->format('j M Y, g:i A') . " — " . $lastLogin->name;
    }
    return "Never";
  }
  
}