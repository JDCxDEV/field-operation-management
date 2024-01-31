<?php
namespace App\Traits;
use App\Models\User;
use App\Models\Company;

trait RoleTrait 
{
  
  /**
   * Check if user is a super admin
   * 
   * @return boolean
   */
  public function superAdmin()
  {
    return $this->role_type == User::SUPER_ADMIN; 
  }

  /**
   * Check if user is a super admin
   * 
   * @return boolean
   */
  public function agent()
  {
    return $this->role_type == User::AGENT; 
  }  

  /**
   * Check if user is a company executive
   * 
   * @return boolean
   */
  public function companyCoordinator() 
  {
    
    if($this->info->company) {
      $companyCoordinators = $this->info->company->coordinators->pluck("id")->toArray();
      if(in_array($this->id, $companyCoordinators)) {
        return true;
      }
    }

    return false;
  }

  /**
   * Check if user is a market executive
   * 
   * @return boolean
   */
  public function marketDirector() 
  {
    
    if($this->info->market) {
      $marketDirectors = $this->info->market->directors->pluck("id")->toArray();
      if(in_array($this->id, $marketDirectors)) {
        return true;
      }
    }

    return false;
  }

  public function isVerified() 
  {
    if($this->superAdmin()) {
      return true;
    }
    return $this->verified;
  }

  /**
   * Check if user is a market executive
   * 
   * @return boolean
   */
  public function podLeader() 
  {
    
    if($this->role_type === User::POD_LEADER) {
      return true;
    }

    return false;
  }

  /**
   * Get user access list
   * 
   * @return array
   */
  public function getMyAccessList()
  {
    $faqs = [
      "title" => "FAQs",
      "routes"=> [
        "faqs.index"
      ]
    ];

    $dashboard = [
      "title" => "Dashboard",
      "routes"=> [
        
      ]
    ];

    $markets = [
      "title" => "Markets",
      "routes"=> [
        "markets.index", "markets.fetch", "markets.create", "markets.appoint", 
        "markets.overview", "markets.directors-and-users", "markets.common-users", "markets.markets",
        "markets.appoint-users", "markets.directors-remove", "markets.store", "markets.find",
        "markets.update", "markets.delete", "markets.restore"
      ]
    ];

    $users = [
      "title" => "Users",
      "routes"=> [
        "users.index", "users.store", "users.find", "users.fetch", 
        "users.update", "users.deactivate", "users.batch-deactivate", "users.activate",
        "users.batch-activate", "users.block", "users.unblock", "users.reset-password",
      ]
    ];

    $leadForm = [
      "title" => "Lead Form",
      "routes"=> [
        "agent-submission-forms.create", "agent-submission-forms.store",
        "agent-submission-forms.fetch", "agent-submission-forms.find", "agent-submission-forms.show",
        "agent-submission-forms.view", "agent-submission-forms.client-validation", "agent-submission-forms.client-address-validation",
        "agent-submission-forms.enrollment-data", "agent-submission-forms.additional-files", "agent-submission-forms.employment-data",
        "agent-submission-forms.plan-information", "agent-submission-forms.payment-information", "agent-submission-forms.save-signature",
        "agent-submission-forms.submit", "agent-submission-forms.update-dependent", "agent-submission-forms.delete-dependent",
        "agent-submission-forms.save-changes", "agent-submission-forms.update-status", "agent-submission-forms.export", 
        "agent-submission-forms.delete-file"
      ]
    ];

    $agentSubmissionForm = [
      "title" => "Agent Submission Forms",
      "routes"=> [
        "agent-submission-forms.index",
      ]
    ];

    $recruits = [
      "title" => "Recruit Check In",
      "routes"=> [
        "recruits.index", "recruits.fetch", "recruits.create", "recruits.store",
        "recruits.find", "recruits.update", "recruits.delete", "recruits.restore",
      ]
    ];    
    
    switch($this->role_type) {
      case User::COMPANY_COORDINATOR:
      case User::COMPANY_AND_MARKET_COORDINATOR:
        return [ $dashboard, $markets, $users, $leadForm, $agentSubmissionForm, $recruits, $faqs ];
      case User::MARKET_DIRECTOR:
      case User::POD_LEADER:
        return [ $dashboard, $users, $leadForm, $agentSubmissionForm, $recruits, $faqs ];
      case User::AGENT:
        return [ $dashboard, $leadForm, $agentSubmissionForm, $recruits, $faqs ];
      default: 
        return [];
    }
  }

  /**
   * Set as role type to coordinator
   * 
   * @param $list = array or int
   * @param int $type 
   */
  public static function setAsCoordinator($list, $type) 
  {
    $users = User::whereIn("id", $list)->get();
    foreach($users as $user) {
      if(in_array($user->role_type, [User::COMPANY_COORDINATOR, User::MARKET_DIRECTOR])) {
        $type = User::COMPANY_AND_MARKET_COORDINATOR;
      }
      $user->update(["role_type" => $type]);
    }
  }

  /**
   * Set as role type to pod leader
   * 
   * @param $list = array or int
   * @param int $type 
   */
  public static function setAsPodLeader($list) 
  {
    $users = User::whereIn("id", $list)->update(["role_type" => USER::POD_LEADER]);
  }

  /**
   * Removing user from coordinator
   * 
   * @param int id
   * @param string type
   */
  public static function removeFromCoordinator($id, $type)
  {
    $user = User::find($id);
    $role = "";
    if($type == "market") {
      if($user->companyCoordinator()) {
        $role = User::COMPANY_COORDINATOR;
      }
    } else {
      if($user->marketDirector()) {
        $role = User::MARKET_DIRECTOR;
      }
    }

    if($role) {
      $user->update(["role_type" => $role]);
    } else {
      User::setToAgent($user->id);
    }
  }

  /**
   * Set as role type to agent
   * 
   * @param int $int 
   */
  public static function setToAgent($id) 
  {
    User::where("id", $id)->update(["role_type" => User::AGENT]);
  }

  /**
   * Render role
   * 
   * @return string
   */
  public function renderRole() 
  {
    switch($this->role_type) {
      case User::SUPER_ADMIN:
        return ["description" => "Super Admin", "class" => "badge badge-primary"];
      case User::ADMIN:
        return ["description" => "Admin", "class" => "badge badge-primary"];
      case User::COMPANY_COORDINATOR:
        return ["description" => "Coordinator", "class" => "badge badge-success"];        
      case User::MARKET_DIRECTOR:
        return ["description" => "Market Director", "class" => "badge badge-info"];        
      case User::COMPANY_AND_MARKET_COORDINATOR:
        return ["description" => "Coordinator & Market Director", "class" => "badge badge-success"];  
      case User::POD_LEADER:
        return ["description" => "Pod Leader", "class" => "badge badge-info"]; 
      case User::AGENT:
        return ["description" => "Agent", "class" => "badge badge-secondary"];
      default: 
        return ["description" => "Not Assigned", "class" => "badge badge-secondary"];
    }
  }

}