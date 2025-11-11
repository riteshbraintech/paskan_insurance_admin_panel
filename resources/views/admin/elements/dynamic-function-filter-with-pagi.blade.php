<div class="text-secondary d-flex justify-content-end align-items-center pb-5">Rows per page:
    <select class="form-select data" onchange="ajaxBrowseTableData()" aria-label="Default select example" id="filter-per-page">
        <option value="10" @if($datas->perPage() == 10) selected @endif>10</option>
        <option value="1" @if($datas->perPage() == 1) selected @endif>1</option>
        <option value="20" @if($datas->perPage() == 20) selected @endif>20</option>
        <option value="50" @if($datas->perPage() == 50) selected @endif>50</option>
        <option value="100" @if($datas->perPage() == 100) selected @endif>100</option>
    </select>
    <p> {{ $datas->count() }} of {{ $datas->total() }}</p>
    {{ $datas->onEachSide(5)->links('vendor.pagination.dynamic-function-bootstrap-4') }}
</div>
