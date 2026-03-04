@extends('admin.layouts.app')

@section('title', 'Create Leave')
@section('page-title', 'Create Leave')

@section('content')
<div class="container-fluid">

    {{-- ================= CREATE LEAVE FORM ================= --}}
    <div class="row">
        <div class="col-md-8 offset-md-2">

            

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create Leave Request</h3>
                </div>



                <form action="{{ route('leaves.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="card-body">

                        {{-- Request Type --}}
                        <div class="form-group">
                            <label for="request_type">Leave Type</label>
                            <select class="form-control" name="request_type" id="request_type" required>
                                <option value="{{ old('request_type') ?? '' }}">{{ old('request_type') ?? 'Select Leave Type' }}</option>
                                <option value="annual">Annual</option>
                                <option value="sick">Sick</option>
                                <option value="maternity">Maternity</option>
                                <option value="bereavement">Bereavement</option>
                            </select>
                        </div>

                        {{-- Start Date --}}
                        <div class="form-group">
                            <label for="start_date">Start Date</label>
                            <input type="date" class="form-control" name="start_date" id="start_date" value="{{ old('start_date') }}" required>
                        </div>

                        {{-- End Date --}}
                        <div class="form-group">
                            <label for="end_date">End Date</label>
                            <input type="date" class="form-control" name="end_date" id="end_date"  value="{{ old('end_date') }}" required>
                        </div>

                        {{-- Destination --}}

                        <div class="form-group">
                            <label for="destination">Destination</label>
                            <input type="text" class="form-control" name="destination" id="destination" placeholder="Where will you be during your leave?" required>
                        </div>

                        {{-- Reason --}}

                        <div class="form-group">
                            <label for="reason">Reason</label>
                            <textarea class="form-control" name="reasons" id="reason" rows="4" value="{{ old('reasons') }}" required></textarea>
                        </div>

                        {{-- Report File --}}
                        <div class="form-group">
                            <label for="report_path">Upload Report (Optional)</label>
                            <input type="file" class="form-control" name="report_path" id="report_path">
                        </div>

                        {{-- Admin Remark (optional for now, keep hidden or read-only) --}}
                        {{-- We keep it hidden since only admin adds remarks --}}
                        <input type="hidden" name="admin_remark" value="">

                    </div>

                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-primary">Submit Request</button>
                    </div>
                </form>

            </div>

        </div>
    </div>

</div>
@endsection
