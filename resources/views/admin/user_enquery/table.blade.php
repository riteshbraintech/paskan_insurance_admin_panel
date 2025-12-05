@php
    $i = $records->firstItem();
@endphp
<div class="table-responsive mt-3">
    <table class="table align-middle" style="background-color:#8e8e8">
        <thead class="table-secondary">
            <tr>
                <th class="no-wrap">@sortablelink('id', 'Sl.No.')</th>
                <th class="no-wrap">User Name</th>
                <th class="no-wrap">Category Name</th>
                <th class="no-wrap">Enqury Time</th>
                <th class="no-wrap">Status</th>
                <th class="no-wrap text-center" width="100px">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($records as $key => $item)
                <tr class="">
                    <td>{{ $i }}</td>
                    <td>{{ $item->user->name }}</td>
                    <td>{{ $item->category->title }}</td>
                    <td>{{ $item->enquery_time }}</td>
                    {{-- <td>{{$item->status}}</td> --}}
                    <td>
                        <form action="{{ route('admin.user_enquery.updateStatus', $item->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="new" {{ $item->status == 'new' ? 'selected' : '' }}>New
                                </option>
                                <option value="processing" {{ $item->status == 'processing' ? 'selected' : '' }}>
                                    Processing</option>
                                </option>
                                </option>
                                <option value="closed" {{ $item->status == 'closed' ? 'selected' : '' }}>Closed
                                </option>
                            </select>
                        </form>
                    </td>

                    <td>
                        <div class="table-actions d-flex align-items-center gap-3 fs-6">
                            <a href="{{ route('admin.user_enquery.view', ['id' => $item->id]) }}"
                                class="text-success facebox" rel="facebox" data-bs-toggle="tooltip"
                                data-bs-placement="bottom" title="View"><i class="fa-solid fa-eye fs-5"></i></a>

                            {{-- <a href="javascript:void(0);"
                                onclick="deleteItem('{{ route('admin.user_enquery.delete', ['id' => $item->id]) }}')"
                                class="text-danger" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                title="Delete"><i class="fa-solid fa-trash fs-5"></i></a> --}}
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
