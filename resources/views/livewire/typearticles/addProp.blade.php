<div class="modal fade" id="modalProp" tabindex="-1" role="dialog" wire:ignore.self>
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Gestion des caracteristiques "{{optional($selectedTypeArticle)->nom}}"</h5>
            </div>
            <div class="modal-body">
                <div class="d-flex my-4 bg-gray-light p-3">
                    <div class="d-flex flex-grow-1 mr-2">
                        <div class="flex-grow-1 mr-2">
                            <input type="text" class="form-control @error("newPropModel.nom") is-invalid @enderror" placeholder="Nom"
                                   wire:model="newPropModel.nom" >
                            @error("newPropModel.nom")
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                        <div class="flex-grow-1">
                            <select class="form-control @error("newPropModel.estObligatoire") is-invalid @enderror"
                                    wire:model="newPropModel.estObligatoire">
                                <option value="">champ obligatoire</option>
                                <option value="1">Oui</option>
                                <option value="0">Non</option>
                            </select>
                            @error("newPropModel.estObligatoire")
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <button class="btn btn-success" wire:click="addProp()">Ajouter</button>
                    </div>
                </div>
                <table class="table table-bordered">
                    <thead class="bg-primary">
                    <th>Nom</th>
                    <th>Est Obligatoire</th>
                    <th>Action</th>
                    </thead>
                    <tbody>
                    @forelse($proprietesTypeArticles as $prop)
                        <tr>
                            <td>{{$prop->nom}}</td>
                            <td>{{$prop-> estObligatoire == 0 ? "NON": "OUI"}}</td>
                            <td>
                                <button class="btn btn-link" wire:click="editProp({{$prop->id}})"><i class="far fa-edit"></i>Editer</button>
                                @if(count($prop->articles)==0)
                                    <button class="btn btn-link" wire:click="showDeletePrompt('{{$prop->nom}}','{{$prop->id}}')"><i class="far fa-trash-alt">Supprimer</i></button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">
                                <span class="text-info">Vous avez pas encore de proprietes pour definir ce type d'article</span>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" wire:click="closeModal">Fermer</button>
            </div>
        </div>
    </div>
</div>
