@extends('layouts.distributor.app')

@section('title', translate('Importação/Exportação em Massa'))

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">{{ translate('Importação/Exportação em Massa') }}</h1>
                <p class="mb-0">{{ translate('Baixe o template, preencha seus produtos (CSV, JSON ou XML) e faça upload para cadastrar em massa.') }}</p>
            </div>
        </div>
    </div>

    <div class="row g-2">
        <div class="col-lg-6">
            <div class="card shadow--card-2 border-0 h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ translate('Baixar Template') }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2">
                        <a class="btn btn-outline-primary" href="{{ route('distributor.food.bulk.template', ['format' => 'csv']) }}">CSV</a>
                        <a class="btn btn-outline-primary" href="{{ route('distributor.food.bulk.template', ['format' => 'json']) }}">JSON</a>
                        <a class="btn btn-outline-primary" href="{{ route('distributor.food.bulk.template', ['format' => 'xml']) }}">XML</a>
                    </div>
                    <hr>
                    <div class="alert alert-info">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <strong>{{ translate('Precisa dos IDs de categoria?') }}</strong>
                                <div class="small text-muted">{{ translate('Baixe a lista de categorias para usar o campo category_id corretamente.') }}</div>
                            </div>
                            <div class="d-flex gap-2">
                                <a class="btn btn-sm btn-outline-info" href="{{ route('distributor.food.categories.export', ['format' => 'csv']) }}">Categorias CSV</a>
                                <a class="btn btn-sm btn-outline-info" href="{{ route('distributor.food.categories.export', ['format' => 'json']) }}">Categorias JSON</a>
                            </div>
                        </div>
                    </div>
                    <p class="mb-1"><strong>{{ translate('Campos do arquivo:') }}</strong></p>
                    <ul class="mb-0">
                        <li><code>name</code> - {{ translate('Nome do produto (obrigatório)') }}</li>
                        <li><code>description</code> - {{ translate('Descrição do produto (opcional)') }}</li>
                        <li><code>category_id</code> - {{ translate('ID da categoria (obrigatório)') }}</li>
                        <li><code>price</code> - {{ translate('Preço, use ponto como separador decimal. Ex: 9.90 (obrigatório)') }}</li>
                        <li><code>maximum_cart_quantity</code> - {{ translate('Qtd máxima por pedido (opcional, padrão 1)') }}</li>
                        <li><code>image_url</code> - {{ translate('URL da imagem (opcional). Será baixada e anexada ao produto.') }}</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow--card-2 border-0 h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ translate('Exportar Seu Estoque') }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2">
                        <a class="btn btn-outline-secondary" href="{{ route('distributor.food.export', ['format' => 'csv']) }}">CSV</a>
                        <a class="btn btn-outline-secondary" href="{{ route('distributor.food.export', ['format' => 'json']) }}">JSON</a>
                        <a class="btn btn-outline-secondary" href="{{ route('distributor.food.export', ['format' => 'xml']) }}">XML</a>
                    </div>
                    <p class="text-muted mt-2 mb-0">{{ translate('Faça backup do seu catálogo atual.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-2 mt-3">
        <div class="col-lg-12">
            <div class="card shadow--card-2 border-0">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ translate('Fazer Upload do Arquivo') }}</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('warning'))
                        <div class="alert alert-warning" style="white-space: pre-line;">{{ session('warning') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('distributor.food.bulk.upload') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="input-label">{{ translate('Formato do Arquivo') }}</label>
                                <select name="format" class="form-control js-select2-custom" required>
                                    <option value="csv">CSV</option>
                                    <option value="json">JSON</option>
                                    <option value="xml">XML</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="input-label">{{ translate('Categoria Padrão (opcional)') }}</label>
                                <select name="default_category_id" class="form-control js-select2-custom">
                                    <option value="">-- {{ translate('Selecionar') }} --</option>
                                    @foreach($categories as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">{{ translate('Usada quando category_id não for informado no arquivo.') }}</small>
                            </div>
                            <div class="col-md-4">
                                <label class="input-label">{{ translate('Arquivo') }}</label>
                                <input type="file" name="file" class="form-control" required>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <button class="btn btn-primary" type="submit">{{ translate('Importar Produtos') }}</button>
                        </div>
                    </form>

                    <hr>
                    <p class="mb-0">{{ translate('Após importar, todos os produtos ficarão com disponibilidade controlada pela coluna "Disponibilidade" na lista de produtos.')}} {{ translate('Ative para disponibilizar a todos os jornaleiros ou desative para indisponibilizar para todos.') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
