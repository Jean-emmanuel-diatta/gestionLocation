<?php

namespace App\Http\Livewire;

use App\Models\ProprieteArticle;
use App\Models\TypeArticle;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class TypeArticleComp extends Component
{
    use WithPagination;

    public $search ="";
    public $isAddTypeArticle =false;
    protected $paginationTheme ="bootstrap";
    public $newTypeArticleName ="";
    public  $newPropModel=[];
    public $newValue ="";
    public $selectedTypeArticle;
    public $editPropModel =[];
    public function render()
    {
        Carbon::setLocale("fr");
        $searchCriteria ="%".$this->search."%";
        $data = [
            //ca permet de faire une recherche au niveau de article
            "typearticles"=> TypeArticle::where("nom","like",$searchCriteria)->latest()->paginate(5),
            "proprietesTypeArticles"=>ProprieteArticle::where("type_article_id",optional($this->selectedTypeArticle)->id)->get()
        ];
        return view('livewire.typearticles.index',$data)
            ->extends("layouts.master")
            ->section("contenu");
    }
    public function toggleShowAddTypeArticleForm()
    {
        if ($this->isAddTypeArticle){
            $this->isAddTypeArticle =false;
            $this->newTypeArticleName ="";
            $this->resetErrorBag(["newTypeArticleName"]);
        }else{
            $this->isAddTypeArticle =true;

        }
    }
    public function addNewTypeArticle()
    {
       $validated = $this->validate([
        "newTypeArticleName" => "required|max:50|unique:type_articles,nom"
       ]);
       TypeArticle::create(["nom"=> $validated["newTypeArticleName"]]);
       $this->toggleShowAddTypeArticleForm();
       $this->dispatchBrowserEvent("showSuccessMessage", ["message"=>"Type d'article ajouter avec succes!"]);

    }

    public function editTypeArticle(TypeArticle $typeArticle)
    {
        $this->dispatchBrowserEvent("showEditForm", ["typearticle" => $typeArticle]);
    }
    //la fonction de la mise a jour d'un article
    public function updateTypeArticle(TypeArticle $typeArticle, $valueFormJs)
    {
        $this->newValue = $valueFormJs;
        $validated =$this->validate([
           "newValue" =>["required","max:50", Rule::unique("type_articles","nom")->ignore($typeArticle->id)]
        ]);
        $typeArticle->update(["nom" =>$validated["newValue"]]);
      $this->dispatchBrowserEvent("showSuccessMessage", ["message"=>"Type d'article mise a jour avec succes!"]);
    }
    //confirmer un message de suppression
    public function confirmDelete($name,$id)
    {
        $this->dispatchBrowserEvent("showConfirmMessage", ["message" => [
            "text" => "vous etes sur le point de supprimer $name de la liste des types d'article",
            "voulez-vous contunuer",
            "title"=>"Etes vous sure de continuer?",
            "type"=>"warning",
            "data"=>[
                "type_article_id"=>$id
            ]
        ]]);
    }
    // suppression d'un type d'article
    public function deleteTypeArticle(TypeArticle $typeArticle)
    {
        $typeArticle->delete();
        $this->dispatchBrowserEvent("showSuccessMessage",
            ["message"=>"Type d'article supprimer avec succes!"]);
    }

    public function showProp(TypeArticle $typeArticle)
    {
        $this->selectedTypeArticle =$typeArticle;
        $this->dispatchBrowserEvent("showModal", []);
    }
    //ajout d'une propriete
    public function addProp(){
        $validated= $this->validate([
           "newPropModel.nom"=>
               [
               "required",
               Rule::unique("propriete_articles","nom")->where("type_article_id", $this->selectedTypeArticle->id)
              ],
            "newPropModel.estObligatoire"=>"required"
         ]);
        ProprieteArticle::create([
                "nom"=>$this->newPropModel["nom"],
                "estObligatoire"=> $this->newPropModel["estObligatoire"],
                "type_article_id"=>$this->selectedTypeArticle->id,
            ]);
              $this->newPropModel = [];
              $this->resetErrorBag();
              $this->dispatchBrowserEvent("showSuccessMessage", ["message"=>"Propriete ajoutee avec succes!"]);
    }
    //permet de ferme une femetre
    public function closeModal(){
        $this->dispatchBrowserEvent("closeModal",[]);
    }
    //message de suppression
    public function showDeletePrompt($name,$id){
        $this->dispatchBrowserEvent("showConfirmMessage", ["message" => [
            "text" => "vous etes sur le point de supprimer '$name' de la liste des proprietes d'article",
            "voulez-vous contunuer",
            "title"=>"Etes vous sure de continuer?",
            "type"=>"warning",
            "data"=>[
                "propriete_id"=>$id
            ]
        ]]);
    }
    //message de suppression d'une propriete
    public function deleteProp(ProprieteArticle $proprieteArticle)
    {
        $proprieteArticle ->delete();
        $this->dispatchBrowserEvent("showSuccessMessage", ["message"=>"Propriete supprimée avec succes!"]);
    }
    //editer une propriete
    public function editProp(ProprieteArticle $proprieteArticle){
        $this->editPropModel["nom"] =$proprieteArticle->nom;
        $this->editPropModel["estObligatoire"] =$proprieteArticle->estObligatoire;
        $this->editPropModel["id"] =$proprieteArticle->id;
    @$this->dispatchBrowserEvent("showEditModal", []);
    }

    //pour la mise a jour des proprietes
    public function updateProp(){
     $this->validate([
            "editPropModel.nom"=>
                [
                    "required",
                    Rule::unique("propriete_articles","nom")->ignore( $this->editPropModel["id"])
                ],
            "editPropModel.estObligatoire"=>"required"
        ]);
        ProprieteArticle::find($this->editPropModel["id"])->update([
            "nom" =>$this->editPropModel["nom"],
            "estObligatoire" =>$this->editPropModel["estObligatoire"],
        ]);
        $this->dispatchBrowserEvent("showSuccessMessage", ["message"=>"Propriete Mise a jour avec succes!"]);
        $this->closeEditModal();
    }
    //permet de ferme une femetre
    public function closeEditModal(){
        $this->editPropModel = [];
        $this->resetErrorBag();
        $this->dispatchBrowserEvent("closeEditModal",[]);
    }
}
