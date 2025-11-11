<style>
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
            <i class="fa-solid fa-eye text-info me-2"></i> View Category Form Field Page
        </h4>
    </div>

    @php
        $lang = app()->getLocale() ?? 'en';
        $categoryTranslation = $record->category?->translations?->where('lang_code', $lang)->first();
    @endphp

    <!-- Category Info -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white fw-bold">
            Category Form Field Details
        </div>
        <div class="card-body">
            <table class="table table-bordered mb-0">
                <tr>
                    <th width="25%">Category</th>
                    <td>
                        {{ $categoryTranslation->title ?? ($record->category->title ?? 'Unnamed Category') }}
                    </td>
                </tr>
                <tr>
                    <th>Field Name</th>
                    <td>{{ $record->name }}</td>
                </tr>
                <tr>
                    <th>Field Type</th>
                    <td>{{ ucfirst($record->type) }}</td>
                </tr>
                <tr>
                    <th>Form Is Required</th>
                    <td>
                        @if ($record->is_required)
                            <span class="badge bg-success">Yes</span>
                        @else
                            <span class="badge bg-danger">No</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Translations -->
    <div class="card shadow-sm">
        <div class="card-header bg-info text-white fw-bold">
            Translations
        </div>
        <div class="card-body">
            @forelse ($translations as $langCode => $data)
                <div class="border rounded p-3 mb-4">
                    <h5 class="fw-bold text-primary mb-3">
                        <i class="fa-solid fa-language me-2"></i> {{ strtoupper($langCode) }} Translation
                    </h5>
                    <table class="table table-bordered mb-0">
                        <tr>
                            <th width="25%">Label</th>
                            <td>{{ $data['label'] }}</td>
                        </tr>

                        <tr>
                            <th width="25%">Place Holder</th>
                            <td>{{ $data['place_holder'] }}</td>
                        </tr>

                        {{-- ✅ Hide options if field type is "number", "text", or "textarea" --}}
                        @unless (in_array($record->type, ['number', 'text', 'textarea']))
                            <tr>
                                <th>Options</th>
                                <td>{!! $data['options'] ?? '-' !!}</td>
                            </tr>
                        @endunless
                    </table>
                </div>
            @empty
                <p class="text-muted">No translations available for this page.</p>
            @endforelse
        </div>
    </div>

</div>
