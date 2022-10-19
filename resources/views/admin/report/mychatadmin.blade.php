<div class="table-responsive" id="filterdata">
            <table class="table table-striped datatable datatable-counselors" id="myChat">
                <thead>
                    <tr>
                        <th>
                            S.No.
                        </th>
                        <th>
                           Date
                        </th>
                        <th>
                            {{ trans('cruds.counselor.fields.user_name') }}
                        </th>
                        <th>
                        User {{ trans('cruds.counselor.fields.age') }}
                        </th>
                        <th>
                        User {{ trans('cruds.counselor.fields.gender') }}
                        </th>
                        <th>
                        User {{ trans('cruds.counselor.fields.topic') }}
                        </th>
                        <th>
                        Chat type
                        </th>
                        <th>
                            Ops Lead Remarks
                        </th>

                        <th>
                            Issue Code 
                        </th>
                        <th>
                            Escalated By
                        </th>
                        <th> User Feedback </th>
                        <th> Action </th>
                    </tr>
                </thead>
                <tbody>
                        <?php  $i = 1;?>
                         @foreach($adminpastchats as $key => $adminpastchat)
                            <tr data-entry-id="{{ $adminpastchat->id }}">
                                <td>{{ $adminpastchat->id }}</td>   
                                <td>{{ $adminpastchat->date }}</td>
                                <td> @if($adminpastchat->getUser != " " ){{ $adminpastchat->getUser->name }} @endif  </td>
                                <td> @if($adminpastchat->getUser != " " ){{ $adminpastchat->getUser->age }} @endif  </td>
                                <td> @if($adminpastchat->getUser != " " ){{ $adminpastchat->getUser->gender }} @endif  </td>
                                <td> @if($adminpastchat->getCategory != " " ){{ $adminpastchat->getCategory->category_name }} @endif  </td>
                                <td> @if($adminpastchat->chat_type == 1) Live @else Async @endif</td>

                                <td>{{ $adminpastchat->remark }}</td>
                                <td>{{ $adminpastchat->issue_code }}</td>
                                <td> 
                                @if(!empty($counsellors))
                                    @foreach($counsellors as $counsellor)
                                        @if($adminpastchat->assign_by == $counsellor->id)
                                        {{ $counsellor->name }}
                                        @endif 
                                    @endforeach
                                @endif
                                </td>
                                <td> @if($adminpastchat->getFeedback != " ") {{ $adminpastchat->getFeedback->feedback }} @endif </td>
                                <td>
                                    @can('counselor_past_cases_show')
                                        <a class="iconeye btn btn-gradient-primary btn-rounded btn-icon" href="{{ route('admin.past-chat-history.show', $adminpastchat->id) }}">
                                           <i class="fa fa-eye" aria-hidden="true"></i>
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                </tbody>
            </table>
        </div>