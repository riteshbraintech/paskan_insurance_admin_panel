<div class="table-responsive mt-3">
    <table class="table align-middle" style="background-color:#8e8e8">
        <thead class="table-secondary">
            <tr>
                <td>#</td>
                <td>
                    <div class="d-flex align-items-center gap-3 cursor-pointer">
                        <label class="form-check-label" for="flexCheckDefault">Role Name</label>
                    </div>
                </td>
                <td>Action</td>
            </tr>
        </thead>
        <tbody>
            @forelse ($roles as $key => $item)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-3 cursor-pointer">
                            <label class="form-check-label" for="flexCheckDefault">{{ $item->role_name }}</label>
                        </div>
                    </td>
                    <td>
                        <div class="table-actions d-flex align-items-center gap-3 fs-6">
                            <a href="{{ route('admin.role.edit', ['id' => $item->id]) }}" class="text-warning" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit"><i class="fa-solid fa-pen-to-square text-warning"></i></a>

                            <a href="javascript:void(0);" onclick="deleteItem('{{ route('admin.role.delete', ['id' => $item->id ]) }}')" class="text-danger" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete"><i class="fa-solid fa-trash text-danger"></i></a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">
                        No Record Found !
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- pagination with filter --}}
@include('admin.elements.filter-with-pagi', ['data' => $roles])
