@php
    $i = $records->firstItem();
@endphp
<div class="table-responsive mt-3">
    <table class="table align-middle" style="background-color:#8e8e8">
        <thead class="table-secondary">
            <tr>
                <th class="no-wrap">@sortablelink('id','Sl No.')</th>
                <th class="no-wrap">Name</th>
                <th class="no-wrap">Email</th>
                <th class="no-wrap">Id Number</th>
                <th class="no-wrap">Status</th>
                <th class="no-wrap text-center" width="100px">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($records as $key => $item)
                <tr class="">
                    <td>{{ $i }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->email }}</td>
                    <td>{{ $item->id_number }}</td>

                    <td>
                        <span 
                            class="badge badge-{{ $item->is_active ? 'success' : 'danger' }} status-{{ $item->id }}" 
                            style="cursor: pointer;"
                            onclick="changeStatus(event, {{ $item->id }})">
                            {{ $item->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <div class="table-actions d-flex align-items-center gap-3 fs-6">
                            <a href="{{ route('admin.user.formfieldview', ['id' => $item->id]) }}"
                                class="text-success facebox" rel="facebox" data-bs-toggle="tooltip"
                                data-bs-placement="bottom" title="View"><i class="fa-solid fa-list-ul"></i></a>

                            <a href="{{ route('admin.user.view', ['id' => $item->id]) }}"
                                class="text-success facebox" rel="facebox" data-bs-toggle="tooltip"
                                data-bs-placement="bottom" title="View"><i
                                    class="fa-solid fa-eye text-success fs-5"></i></a>

                            <a href="{{ route('admin.user.edit', ['id' => $item->id]) }}" class="text-warning"
                                data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit"><i
                                    class="fa-solid fa-pen-to-square text-warning fs-5"></i></a>

                            <a href="javascript:void(0);"
                                onclick="deleteItem('{{ route('admin.user.delete', ['id' => $item->id]) }}')"
                                class="text-danger" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                title="Delete"><i class="fa-solid fa-trash fs-5"></i></a>
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
<script></script>
{{-- pagination with filter --}}
@include('admin.elements.filter-with-pagi', ['data' => $records])
