  <table class=" table   table-striped   datatable datatable-User" id="pastChat">
                <thead>
                    <tr>
                        <th>
                            {{ trans('cruds.counselor.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.counselor.fields.date') }}
                        </th>
                        <th>
                            {{ trans('cruds.counselor.fields.user_name') }}
                        </th>
                        <th>
                            {{ trans('cruds.counselor.fields.age') }}
                        </th>
                        <th>
                            {{ trans('cruds.counselor.fields.gender') }}
                        </th>
                        <th>
                            {{ trans('cruds.counselor.fields.topic') }}
                        </th>
                        <th>
                            {{ trans('cruds.counselor.fields.chat_type') }}
                        </th>
                        <th>
                             Escalation Reason
                        </th>
                        <th>
                            Counsellor Remarks
                        </th>
                        <th>
                            Issue Code 
                        </th>
                        <th>
                            {{ trans('cruds.counselor.fields.feedback') }}
                        </th>
                         <th>
                            User Comments
                        </th>
                        <th>
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                       <?php  $i = 1;?>
                         @foreach($chatHistorys as $key => $counselorpastcase)

                         <?php $date = date('Y-m-d',strtotime($counselorpastcase->created_at));
                            $time = date('H:i:s',strtotime($counselorpastcase->created_at));
                          ?>
                            <tr data-entry-id="{{ $counselorpastcase->session_id }}">
                                <td>{{ $key+1 }}</td>   
                                <td>{{ $date }}</td>
                                <td> @if($counselorpastcase->getUser != " " ){{ $counselorpastcase->getUser->name }} @endif  </td>
                                <td> @if($counselorpastcase->getUser != " " ){{ $counselorpastcase->getUser->age }} @endif  </td>
                                <td> @if($counselorpastcase->getUser != " " ){{ $counselorpastcase->getUser->gender }} @endif  </td>
                                <td> @if($counselorpastcase->getCategory != " " ){{ $counselorpastcase->getCategory->category_name }} @endif  </td>
                                <td> @if($counselorpastcase->chat_type == 1) Live @else Async @endif</td>
                                <td>{{ $counselorpastcase->close_reason }}</td>
                                <td>{{ $counselorpastcase->close_remark }}</td>
                                <td>{{ $counselorpastcase->close_issue_code }}</td>

                                <td> <?php 
                                    if($counselorpastcase->chat_type == 1) {

                                    if(isset($counselorpastcase->feedback_star_reviews) AND $counselorpastcase->feedback_star_reviews !='0'){
                                    for ($ii=1; $ii <= 6; $ii++) { 
                                        if($counselorpastcase->feedback_star_reviews >= $ii){
                                            echo '<span class="fa fa-star" style="color: orange;"></span>';
                                        } else {
                                            echo '<span class="fa fa-star" ></span>';
                                        }
                                    } 
                                    }
                                    } ?>  </td>

                                    <td>@if(isset($counselorpastcase->feedback_comment) AND $counselorpastcase->feedback_comment != " ") {{ $counselorpastcase->feedback_comment }} @endif </td>
                                <td>
                                    @can('counselor_past_cases_show')
                                        <a class="iconeye btn btn-gradient-primary btn-rounded btn-icon" href="{{ route('admin.past-chat-history.show', $counselorpastcase->session_id) }}">
                                           <i class="fa fa-eye" aria-hidden="true"></i>
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                </tbody>
            </table>