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

<div class="mt-3">
    <div class="card">
        <div class="card-header">
            Update Information
        </div>
        <div class="card-body">
            <form action="{{ route('admin.client.storeMerge') }}" method="post">
                @csrf
                <div class="row">
                    <input type="hidden" name="removeIds" value="{{ $idString ?? '' }}">
                    <input type="hidden" name="updateId" value="{{ $clients[0]->id ?? '' }}">
                    <div class="col-md-4">
                        <label for="client_name" class="form-label">Client name</label>
                        <input type="text" name="client_name" class="form-control" id="client_name" value="{{ $clients[0]->client_name ?? '' }}" placeholder="">

                        @if ($errors->has('client_name'))
                            <div class="text-danger">{{ $errors->first('client_name') }}</div>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <label for="mobile" class="form-label">Mobile</label>
                        <input type="number" name="mobile" class="form-control" id="mobile" value="{{ $clients[0]->mobile ?? '' }}" placeholder="">
                        
                        @if ($errors->has('mobile'))
                            <div class="text-danger">{{ $errors->first('mobile') }}</div>
                        @endif
                    </div>

                    <div class="col-md-4">
                        <label for="email" class="form-label">Email<span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" id="email" value="{{ $clients[0]->email ?? '' }}" placeholder="">
                        
                        @if ($errors->has('email'))
                            <div class="text-danger">{{ $errors->first('email') }}</div>
                        @endif
                    </div>

                    <div class="col-md-4">
                        <label for="skype" class="form-label">Skype</label>
                        <input type="text" name="skype" class="form-control" id="skype" value="{{ $clients[0]->skype ?? '' }}" placeholder="Enter skype">
                        
                        @if ($errors->has('skype'))
                            <div class="text-danger">{{ $errors->first('skype') }}</div>
                        @endif
                    </div>

                    <div class="col-md-4">
                        <label for="linkedin" class="form-label">Linkedin</label>
                        <input type="text" name="linkedin" class="form-control" id="linkedin" value="{{ $clients[0]->linkedin ?? '' }}" placeholder="">
                        
                        @if ($errors->has('linkedin'))
                            <div class="text-danger">{{ $errors->first('linkedin') }}</div>
                        @endif
                    </div>

                    <div class="col-md-4">
                        <label for="other" class="form-label">Other</label>
                        <input type="text" name="other" class="form-control" id="other" value="{{ $clients[0]->other ?? '' }}" placeholder="">
                       
                        @if ($errors->has('other'))
                            <div class="text-danger">{{ $errors->first('other') }}</div>
                        @endif
                    </div>
                    <div class="col-md-12">
                        <label for="location" class="form-label">Location</label>
                        <textarea name="location" id="location" class="form-control" cols="30" rows="1">{{ $clients[0]->location ?? '' }}</textarea>
                        
                        @if ($errors->has('location'))
                            <div class="text-danger">{{ $errors->first('location') }}</div>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            Client Information
        </div>
        <div class="card-body">
            <div class="table-responsive mt-3">
                <table class="table align-middle" style="background-color:#8e8e8">
                    <thead class="table-secondary">
                        <tr>
                            <td>#</td>
                            <td>Name</td>
                            <td>Mobile</td>
                            <td>Email</td>
                            <td>Linkedin</td>
                            <td>Skype</td>
                            <td>Other</td>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($clients && !empty($clients))
                            @forelse ($clients as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $item->client_name }}</td>
                                    <td class="no-wrap">{{ $item->mobile }}</td>
                                    <td class="no-wrap">{{ $item->email }}</td>
                                    <td class="no-wrap">{{ $item->linkedin }}</td>
                                    <td class="no-wrap">{{ $item->skype }}</td>
                                    <td>{{ $item->other }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">
                                        No Record Found !
                                    </td>
                                </tr>
                            @endforelse
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
