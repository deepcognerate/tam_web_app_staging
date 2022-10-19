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
                             reason
                        </th>
                        <th>
                            remark
                        </th>
                        <th>
                            {{ trans('cruds.counselor.fields.feedback') }}
                        </th>
                         <th>
                            Comment
                        </th>
                        <th>
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                        <?php  $i = 1;?>
                         @foreach($counselorpastcases as $key => $counselorpastcase)
                            <tr data-entry-id="{{ $counselorpastcase->id }}">
                                <td>{{ $counselorpastcase->id }}</td>   
                                <td>{{ $counselorpastcase->date }}</td>
                                <td> @if($counselorpastcase->getUser != " " ){{ $counselorpastcase->getUser->name }} @endif  </td>
                                <td> @if($counselorpastcase->getUser != " " ){{ $counselorpastcase->getUser->age }} @endif  </td>
                                <td> @if($counselorpastcase->getUser != " " ){{ $counselorpastcase->getUser->gender }} @endif  </td>
                                <td> @if($counselorpastcase->getCategory != " " ){{ $counselorpastcase->getCategory->category_name }} @endif  </td>
                                <td> @if($counselorpastcase->chat_type == 1) Live @else Async @endif</td>
                                <td>{{ $counselorpastcase->reason }}</td>
                                <td>{{ $counselorpastcase->remark }}</td>
                                <td> @if($counselorpastcase->getFeedback != " ")
                                    
                                    <?php  if($counselorpastcase->chat_type == 1) {
                                    if($counselorpastcase->getFeedback->star_reviews !='0'){
                                    for ($ii=1; $ii <= 6; $ii++) { 
                                        if($counselorpastcase->getFeedback->star_reviews >= $ii){
                                            echo '<span class="fa fa-star" style="color: orange;"></span>';
                                        } else {
                                            echo '<span class="fa fa-star" ></span>';
                                        }
                                    } 
                                    } } ?> @endif </td>

                                    <td>@if($counselorpastcase->getFeedback != " ") {{ $counselorpastcase->getFeedback->feedback }} @endif </td>
                                <td>
                                    @can('counselor_past_cases_show')
                                        <a class=" btn btn-gradient-primary btn-rounded btn-icon" href="{{ route('admin.past-chat-history.show', $counselorpastcase->id) }}">
                                           <i class="fa fa-eye" aria-hidden="true"></i>
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                </tbody>
            </table>