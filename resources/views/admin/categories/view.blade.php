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
            <i class="fa-solid fa-eye text-info me-2"></i> View Category Page
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
            @forelse ($translations as $langCode => $data)
                <div class="border rounded p-3 mb-4">
                    <h5 class="fw-bold text-primary mb-3">
                        <i class="fa-solid fa-language me-2"></i> {{ strtoupper($langCode) }} Translation
                    </h5>
                    <table class="table table-bordered mb-0">
                        <tr>
                            <th width="25%">Title</th>
                            <td>{{ $data['title'] }}</td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td>{!! $data['description'] !!}</td>
                        </tr>
                        <tr>
                            <th>Meta Title</th>
                            <td>{{ $data['meta_title'] }}</td>
                        </tr>
                        <tr>
                            <th>Meta Description</th>
                            <td>{{ $data['meta_description'] }}</td>
                        </tr>
                        <tr>
                            <th>Meta Keywords</th>
                            <td>{{ $data['meta_keywords'] }}</td>
                        </tr>
                    </table>
                </div>
            @empty
                <p class="text-muted">No translations available for this page.</p>
            @endforelse
        </div>
    </div>

</div>
