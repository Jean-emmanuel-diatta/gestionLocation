<div class="row p-4 pt-5">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-gradient-primary d-flex align-items-center">
                <h3 class="card-title flex-grow-1"><i class="fa  fa-list fa-2x"></i>Liste des articles</h3>
                <div class="card-tools d-flex align-items-center">
                    <a class="btn btn-link text-white mr-4 d-block" wire:click="showAddArticleModal"><i class="fas fa-user"></i> Ajouter un article</a>
                    <div class="input-group input-group-md" style="width: 250px;">
                        <input type="text" name="table_search" wire:model.debounce.500ms="search" class="form-control float-right" placeholder="Search">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body table-responsive p-0 table-striped">
                <div class="d-flex justify-content-end p-4 bg-indigo">
                    <div class="form-group mr--3">
                        <label for="filtreType">Filtrer par type</label>
                        <select id="filtreType" wire:model="filtreType" class="form-control">
                            <option value=""></option>
                            @foreach($typearticles as $typearticle)
                                <option value="{{$typearticle->id}}">{{$typearticle->nom}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="filtreEtat">Filtrer par Etat</label>
                        <select id="filtreEtat" wire:model="filtreEtat" class="form-control">
                            <option value=""></option>
                            <option value="1">Disponible</option>
                            <option value="0">Indisponible</option>
                        </select>
                    </div>
                </div>
                <div style="height: 350px;">
                    <table class="table table-head-fixed text-nowrap">
                        <thead>
                        <tr>
                            <th></th>
                            <th>Article</th>
                            <th class="text-center">Type</th>
                            <th class="text-center">Etat</th>
                            <th class="text-center">Ajoute</th>
                            <th class="text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($articles as $article)
                            <tr>
                                <td>
                                        <img src="{{asset($article->imageUrl)}}"alt="" style="width: 60px; height: 60px;">
                                </td>
                                <td>{{$article->nom}} - {{$article->noSerie}}</td>
                                <td class="text-center">{{$article->type->nom}}</td>
                                <td class="text-center">
                                    @if($article->estDisponible)
                                        <span class="badge badge-success">Disponible</span>
                                    @else
                                        <span class="badge badge-danger"> Indisponible</span>
                                    @endif
                                </td>
                                <td class="text-center">{{optional($article->created_at)->diffForHumans()}}</td>
                                <td class="text-center">

                                    <a title="Tarifs {{$article->nom}}"
                                        href="{{route('admin.gestarticles.articles.tarifs',
                                    ['articleId'=>$article->id]) }}" class="btn btn-link"
                                    ><i class="fas fa-money-check"></i></a>
                                    <button class="btn btn-link" wire:click="editArticle({{$article->id}})"><i class="far fa-edit"></i>Editer</button>
                                    <button class="btn btn-link"wire:click="confirmDelete({{$article->id}})"><i class="far fa-trash-alt">Supprimer</i></button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="alert alert-danger ">
                                        <h5><i class="icon fas fa-ban"></i> Alert!</h5>
                                       Aucune donnee trouvee par rapport aux elements de recherche entrer
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <div class="float-right">
                    {{ $articles->links()}}
                </div>
            </div>
        </div>
    </div>
</div>

