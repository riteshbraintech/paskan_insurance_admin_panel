<style scoped>
    #facebox {
        top: 78.3px;
        left: 25%;
        width: 50%;
    }

    #facebox .content {
        width: 100%;
    }
</style>

<div class="container-fluid py-4 w100">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold">
            <i class="fa-solid fa-eye text-info me-2"></i> View User Enquery Page
        </h4>
    </div>

    <!-- Translations -->
    <div class="card shadow-sm">
        <div class="card-header bg-info text-white fw-bold">
            Translations 
            @if($record->is_active)
                <span class="badge bg-success">Active</span>
            @else
                <span class="badge bg-danger">Inactive</span>
            @endif
            
        </div>
        <div class="card-body">
                <div class="border rounded p-3 mb-4">
                   
                    <table class="table table-bordered mb-0">
                        <tr>
                            <th width="25%">Full Name</th>
                            <td>{{ $record->user->name }}</td>
                        </tr>
                        <tr>
                            <th>Category</th>
                            <td>{!! $record->category->title !!}</td>
                        </tr>
                        <tr>
                            <th>Enquery Time</th>
                            <td>{{ $record->enquery_time }}</td>
                        </tr>
                    </table>
                </div>
        </div>
    </div>

</div>
