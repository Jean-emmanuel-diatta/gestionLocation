<?php

namespace App\Http\Livewire;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Utilisateurs extends Component
{
    use WithPagination;
    protected $paginationTheme ="bootstrap";

    public $currentPage =PAGELIST;

    public $newUser =[];
    public $editUser =[];
    public $rolePermissions = [];
    public $rechech ="";
/*    protected $messages =[
        'newUser.nom.required'=>"le nom de l'utilisateur est requis",

    ];

    protected $validationAttributes =[
        'newUser.telephone1' => "veuillez inserer le numero de telephone1"
    ];*/

    public function render()
    {
        $rechercheUser ="%".$this->rechech."%";
        Carbon::setLocale("fr");

        return view('livewire.utilisateurs.index',[
            "users"=>User::where("prenom","like",$rechercheUser)->latest()->paginate(5)
        ])
            ->extends("layouts.master")
            ->section("contenu");
    }

    public function rules(){
        if($this->currentPage == PAGEEDITFORM){
            return[
                'editUser.nom' => 'required',
                'editUser.prenom' => 'required',
                'editUser.email' => ['required','email',Rule::unique("users","email")->ignore($this->editUser['id'])],
                'editUser.telephone1' => ['required','numeric',Rule::unique("users","telephone1")->ignore($this->editUser['id'])],
                'editUser.pieceIdentite' =>  'required',
                'editUser.sexe' => 'required',
                'editUser.numeroPieceIdentite' =>['required',Rule::unique("users","numeroPieceIdentite")->ignore($this->editUser['id'])],
            ];
        }
        return [
            'newUser.nom' => 'required',
            'newUser.prenom' => 'required',
            'newUser.email' => 'required|email|unique:users,email',
            'newUser.telephone1' => 'required|numeric|unique:users,telephone1',
            'newUser.pieceIdentite' => 'required',
            'newUser.sexe' => 'required',
            'newUser.numeroPieceIdentite' => 'required|unique:users,numeroPieceIdentite',
        ];
    }

    public function goToAddUser()
    {
        $this->currentPage =PAGECREATEFORM;
    }

    public function goToEditUser($id)
    {
        $this->editUser = User::find($id)->toArray();
        $this->currentPage =PAGEEDITFORM;
        $this->populateRolePermissions();
    }
    public function populateRolePermissions()
    {
        $this->rolePermissions["roles"] =[];
        $this->rolePermissions["permissions"] =[];
        $mapForCB = function($value){
            return $value["id"];
        };
        $roleIds = array_map($mapForCB, User::find($this->editUser["id"])->roles->toArray());
        $permissionIds = array_map($mapForCB, User::find($this->editUser["id"])->permissions->toArray());
        foreach (Role::all()as $role)
        {
            if(in_array($role->id, $roleIds)){
                array_push($this->rolePermissions["roles"], ["role_id"=>$role->id, "role_nom"=>$role->nom,"active"=>true]);
            }else
            {
                array_push($this->rolePermissions["roles"], ["role_id"=>$role->id, "role_nom"=>$role->nom,"active"=>false]);
            }
        }
        foreach (Permission::all()as $permission)
        {
            if(in_array($permission->id, $permissionIds)){
                array_push($this->rolePermissions["permissions"], ["permission_id"=>$permission->id, "permission_nom"=>$permission->nom,"active"=>true]);
            }else
            {
                array_push($this->rolePermissions["permissions"], ["permission_id"=>$permission->id, "permission_nom"=>$permission->nom,"active"=>false]);
            }
        }
       // dump($this->rolePermissions);
        //la logique pour charger les roles et permissions
    }

    public function updatRoleAndPermissions()
    {
        DB::table("user_role")->where("user_id", $this->editUser["id"])->delete();
        DB::table("user_permission")->where("user_id", $this->editUser["id"])->delete();
        foreach ($this->rolePermissions["roles"] as $role){
            if($role["active"]) {
                User::find($this->editUser["id"])->roles()->attach($role["role_id"]);
            }
        }
        foreach ($this->rolePermissions["permissions"] as $permission){
            if($permission["active"]) {
                User::find($this->editUser["id"])->permissions()->attach($permission["permission_id"]);
            }
        }
        $this->dispatchBrowserEvent("showSuccessMessage",
            ["message"=>"Role et permission mise a jour avec succes!"]);
    }
    public function goToListUser(){
        $this->currentPage = PAGELIST;
        $this->editUser=[];
    }
    public function addUser(){
      //verifier que les informations envoyees sont correctes
       $validationAttributes = $this->validate();
        $validationAttributes["newUser"]["password"] = "password";

        //ajouteer un nouvel utilisateur
        User::create($validationAttributes["newUser"]);
        $this->newUser = [];

        $this->dispatchBrowserEvent("showSuccessMessage",
            ["message"=>"Utilisateur creer avec succes!"]);
    }
    public function updateUser(){
        //recuperer l'utilisateur
        $validationAttributes =$this->validate();

        User::find($this->editUser["id"])->update($validationAttributes["editUser"]);

        $this->dispatchBrowserEvent("showSuccessMessage",
            ["message"=>"Utilisateur mise a jour avec succes!"]);
    }

    public function confirmPwdReset(){
        $this->dispatchBrowserEvent("showSuccessMessage", ["message" => [
            "text" => "vous etes sur le point de reinitialiser le mot de passe de cette utilisateur",
            "voulez-vous contunuer",
            "title"=>"Etes vous sure de continuer?",
            "type"=>"warning",
        ]]);
    }

    public function resetPassword(){
         User::find($this->editUser["id"])->update(["password"=>Hash::make(DEFAULTPASSWORD)]);
        $this->dispatchBrowserEvent("showSuccessMessage",
            ["message"=>"Mot de passe utilisateur reinitialiser avec succes!"]);
    }

    public function confirmDelete($name,$id)
    {
        $this->dispatchBrowserEvent("showConfirmMessage", ["message" => [
            "text" => "vous etes sur le point de supprimer $name de la liste des utilisateurs",
                "voulez-vous contunuer",
            "title"=>"Etes vous sure de continuer?",
            "type"=>"warning",
            "data"=>[
                "user_id"=>$id
            ]
        ]]);
    }

    public function deleteUser($id){
        User::destroy($id);
        $this->dispatchBrowserEvent("showSuccessMessage",
            ["message"=>"utilisateur supprimer avec succes!"]);
    }
}
