@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="row">
    <div class="card-body">
        <form action="">
            <div class="row">
                <div class="col-md-4">
                    <div class="m-form__section m-form__section--first">
                        <div class="form-group">
                            <label class="form-control-label">Start Date</label>
                            <input class="form-control date" type="text" name="fromdate" id="m_datepicker_2" placeholder="Start Date" required>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="m-form__section m-form__section--first">
                        <div class="form-group">
                            <label class="form-control-label">End Date </label>
                            <input class="form-control date" type="text" name="todate" id="m_datepicker_3" placeholder="End Date" required>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group filter">
                        <button class="btn btn-primary" onClick="filterLiveAsync();" type="submit">
                            Apply Filter
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="card-body">
        <form action="">
            <div class="row">

                <div class="col-md-6">
                    <div class="m-form__section m-form__section--first">
                        <div class="form-group">
                            <label class="form-control-label">Title</label>
                            <input class="form-control" type="text" name="title" placeholder="text" required>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group filter">
                        <button class="btn btn-primary" onClick="filterLiveAsync();" type="submit">
                            sarch
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        Session History
    </div>

    <div class="card-body">

        <div class="table-responsive">
            <table class=" table   table-striped   datatable datatable-User" id="liveChatHistory">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Date</th>
                        <th> session Name</th>
                        <th>start time</th>
                        <th>session type</th>
                        <th>issue code</th>
                        <th>chat Reason</th>
                        <!-- <th>{{ trans('cruds.counselor.fields.chat_type') }}</th>
                        <th>Escalation Reason</th>
                        <th>Counsellor Remarks</th>
                        <th>Issue Code</th>
                        <th>{{ trans('cruds.counselor.fields.feedback') }}</th>
                        <th>User Comments</th> -->
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection