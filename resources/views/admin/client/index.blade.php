@extends('admin.layouts.app')
@section('content')

    <style>
        .rolesList>button {
            margin-left: 10px;
        }
    </style>

    @include('admin.components.FlashMessage')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">All Clients</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('admin.client.list') }}">Clients Account</a></li>
                    <li class="breadcrumb-item active" aria-current="page">All Clients</li>
                </ol>
            </nav>
        </div>

        <div class="ms-auto">
            <div class="btn-group">
                <a href="{{ route('admin.client.create') }}">
                    <a href="{{ route('admin.client.create') }}" class="btn btn-primary">+ Add Client</a>
                </a>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="card-header py-3">
                <div class="row g-2">
                    @include('admin.elements.search')
                    @include('admin.elements.perPage', ['datas' => $clients])
                </div>
            </div>

            <div class="card-body">
                <div class="load-table-data">
                    @include('admin.client.table')
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).on("click",".selectItems",function(){
            if($('.selectItems:checked').length > 1){
                $('.mergeIcon').show();
            }else{
                $('.mergeIcon').hide();
            }
        });

        // submt page 
        $(document).on("click",".mergeIcon",function(){
            var Query = $('.selectItems:checked');
            var ids = [];
            if(Query.length > 0){
                $.each(Query, function(key, q){
                    ids.push(q.value);
                });  
                jQuery.facebox({ ajax: '{{ route("admin.client.merge") }}?ids='+ids });
            }
        });
    </script>
@endpush
