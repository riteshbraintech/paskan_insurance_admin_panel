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
            <i class="fa-solid fa-eye text-info me-2"></i> View Banner Page
        </h4>
    </div>

    <!-- Translations -->
    <div class="card shadow-sm">
        <div class="card-header bg-info text-white fw-bold">
            Translations
            @if ($record->is_active)
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
                            <th width="25%">Sub Title</th>
                            <td>{{ $data['sub_title'] }}</td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td>{!! $data['description'] !!}</td>
                        </tr>
                        <tr>
                            <th>Image</th>
                            <td>
                                @if (!empty($record['image']))
                                    <img src="{{ asset('public/admin/banners/img/' . $record['image']) }}" alt="Banner Image"
                                        style="object-fit: contain; border-radius: 6px; border: 1px solid #ddd; background-color: #f8f9fa; max-width: 80px; height: 80px; padding: 4px;">
                                @else
                                    <span class="text-muted">No image available</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            @empty
                <p class="text-muted">No translations available for this page.</p>
            @endforelse
        </div>
    </div>

</div>
