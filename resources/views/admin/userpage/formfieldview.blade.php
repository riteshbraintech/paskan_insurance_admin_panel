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
            <i class="fa-solid fa-eye text-info me-2"></i> {{ $user->name }} Enquiry Information Details
        </h4>
    </div>

    @if ($categories->isEmpty())
        <div class="text-center text-muted py-4">No insurance details found for this user.</div>
    @else
        @foreach ($categories as $category)
            <!-- User Info Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white fw-bold d-flex justify-content-between align-items-center">
                    <span>{{ $category->title }}</span>
                </div>

                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="35%">Field Name</th>
                                <th>Field Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($records[$category->id] ?? [] as $record)
                                <tr>
                                    <td>{{ $record->formField->label ?? 'N/A' }}</td>
                                    <td>{{ $record->formfieldvalue ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    @endif
</div>
