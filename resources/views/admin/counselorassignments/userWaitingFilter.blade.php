                <?php  $i = 1;?>
                @if(!empty($waitingUsers))                   
                    @foreach($waitingUsers as $key => $waitingUser)
                        <tr data-entry-id="{{ $waitingUser->id }}">
                            <td></td>
                            <td> <?php echo $i; ?></td>
                            <td>{{ $waitingUser->getUser->name }} </td>
                            <td>{{ $waitingUser->getUser->age }} </td>
                            <td>{{ $waitingUser->getUser->gender }} </td>
                            <td>{{ $waitingUser->getCategory->category_name }} </td>
                            <td>{{ $waitingUser->getUser->location }} </td>
                            <td>{{ $i }} </td>
                            <td>     
                                <select class="form-control select2 {{ $errors->has('counselor') ? 'is-invalid' : '' }}" name="counselor" id="counselor{{ $waitingUser->id }}">
                                   <option> Select Counsellor </option>
                                   @foreach($counsellors as $key => $counsellor)  
                                   <option value="{{ $counsellor->id }}">{{ $counsellor->name }} </option>
                                   @endforeach
                                </select>
                                <input type="hidden" name="watting_id" id="watting_id{{ $waitingUser->id }}" value="{{ $waitingUser->id }}">
                                <input type="hidden" name="user_id" id="user_id{{ $waitingUser->id }}" value="{{ $waitingUser->user_id }}">
                                <input type="hidden" name="category_id" id="category_id{{ $waitingUser->id }}" value="{{ $waitingUser->getCategory->id }}">
                                <input type="hidden" name="counselor_category_id" id="counselor_category_id{{ $waitingUser->id }}" value="{{ $waitingUser->category_id }}">
                            </td>
                            <td>
                                @can('user_counselor_assignment')
                                    <a class="btn btn-gradient-primary btn-rounded btn-icon submit" style="width: 95px;" onclick="userAssignToCounselor({{ $waitingUser->id }});">
                                         Counsellor
                                    </a>
                                @endcan
                                @can('my_chat_accses')
                                    <a class="btn btn-gradient-primary btn-rounded btn-icon submit" onclick="userAssignToAdmin({{ $waitingUser->id }});">
                                    Myself
                                    </a>
                                @endcan
                            </td>
                        </tr>
                        <?php $i++ ;?>
                    @endforeach
                @endif