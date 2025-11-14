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
            <i class="fa-solid fa-eye text-info me-2"></i> View User Details
        </h4>
    </div>

    <!-- User Info Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-info text-white fw-bold d-flex justify-content-between align-items-center">
            <span>User Information</span>
            @if($record->is_active)
                <span class="badge bg-success">Active</span>
            @else
                <span class="badge bg-danger">Inactive</span>
            @endif
        </div>

        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th width="30%">Name</th>
                    <td>{{ $record->name }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $record->email }}</td>
                </tr>
                <tr>
                    <th>ID Number</th>
                    <td>{{ $record->id_number }}</td>
                </tr>
                <tr>
                    <th>Date of Birth</th>
                    <td>{{ $record->dob }}</td>
                </tr>
                <tr>
                    <th>Gender</th>
                    <td>{{ ucfirst($record->gender) }}</td>
                </tr>
                <tr>
                    <th>Nationality</th>
                    <td>{{ $record->nationality }}</td>
                </tr>
                <tr>
                    <th>Marital Status</th>
                    <td>{{ ucfirst($record->marital_status) }}</td>
                </tr>
                <tr>
                    <th>Mobile</th>
                    <td>{{ $record->phone }}</td>
                </tr>
                <tr>
                    <th>Address</th>
                    <td>{{ $record->address }}</td>
                </tr>
                
            </table>

            {{-- <div class="mt-3">
                <a href="{{ route('admin.user.index') }}" class="btn btn-outline-secondary px-5 radius-30">Back</a>
            </div> --}}
        </div>
    </div>

</div>
