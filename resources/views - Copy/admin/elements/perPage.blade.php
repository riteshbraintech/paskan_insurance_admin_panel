<div class="col-lg-1 " style="width:9%;">
    <select class="form-select" onchange="ajaxTableData()" aria-label="Default select example" id="filter-per-page">
        <option value="1" @if ($datas->perPage() == 1) selected @endif>1</option>
        <option value="5" @if ($datas->perPage() == 5) selected @endif>5</option>
        <option value="10" @if ($datas->perPage() == 10) selected @endif>10</option>
        <option value="20" @if ($datas->perPage() == 20) selected @endif>20</option>
        <option value="50" @if ($datas->perPage() == 50) selected @endif>50</option>
        <option value="100" @if ($datas->perPage() == 100) selected @endif>100</option>
    </select>
</div>
