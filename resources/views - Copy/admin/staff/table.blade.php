@php
    $i = $staffList->firstItem();
@endphp

<div class="table-responsive mt-3">
    <table class="table align-middle" style="background-color:#8e8e8">
        <thead class="table-secondary">
            <tr>
                <td>#</td>
                <td>Staff ID</td>
                <td>Full Name</td>
                <td>Email</td>
                <td>Role</td>
                <td>Gender</td>
                <td>Status</td>
                <td width="100px">Action</td>
            </tr>
        </thead>
        <tbody>
            @forelse ($staffList as $key => $item)
                <tr class="{{($item->is_test == 1 && admin()->user()->role_id == 'superadmin') ? 'table-warning' : ''}}">
                    <td>{{ $i }}</td>
                    <td>{{ $item->staff_id }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->email }}</td>
                    <td>{{ ucfirst($item->role_id) }}</td>
                    <td>{{ Str::ucfirst($item->gender ?? '') }}</td>
                    <td style="width:10%">
                        <span class="badge badge-{{ $item->status == 'active' ? 'success' : 'danger' }} status-{{$item->id}}" onclick="changeStatus(event,{{$item->id}})">{{ ucfirst($item->status) }}</span>
                    </td>
                    <td>
                        <div class="table-actions d-flex align-items-center gap-3">
                            <a href="{{ route('admin.staff.edit', ['id' => $item->id]) }}" class="text-warning" data-bs-toggle="tooltip" title="Edit"><i class="fa-solid fa-pen-to-square text-warning fs-5"></i></a>

                            <a href="javascript:void(0);" onclick="deleteItem('{{ route('admin.staff.delete', ['id' => $item->id]) }}')" class="text-danger" title="Delete"><i class="fa-solid fa-trash fs-5"></i></a>
                        </div>
                    </td>
                </tr>
                @php
                    $i++;
                @endphp
            @empty
                <tr>
                    <td colspan="10" class="text-center">
                        No Record Found !
                    </td>
                </tr>
            @endforelse

        </tbody>
    </table>
</div>

{{-- pagination with filter --}}
@include('admin.elements.filter-with-pagi', ['data' => $staffList])
