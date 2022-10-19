<?php  $i = 1;?>
                    @foreach($getcurrentcounselorbyusers as $key => $getcurrentcounselorbyusers)
                        <tr data-entry-id="{{ $getcurrentcounselorbyusers->id }}">
                            <td></td>
                            <td><?php echo $i; $i++ ;?> </td>
                            <td><?php if(isset($getcurrentcounselorbyusers->getUser->name)) { echo $getcurrentcounselorbyusers->getUser->name; } else { echo '<p style="color:red;">NA</p>'; }?> </td>
                            <td><?php if(isset($getcurrentcounselorbyusers->getUser->age)) { echo $getcurrentcounselorbyusers->getUser->age; } else { echo '<p style="color:red;">NA</p>'; }?></td>
                            <td><?php if(isset($getcurrentcounselorbyusers->getUser->gender)) { echo $getcurrentcounselorbyusers->getUser->gender; } else { echo '<p style="color:red;">NA</p>'; }?></td>
                            <td>{{ $getcurrentcounselorbyusers->getCategory->category_name }} </td>
                            <td><?php if(isset($getcurrentcounselorbyusers->getUser->location)) { echo $getcurrentcounselorbyusers->getUser->location; } else { echo '<p style="color:red;">NA</p>'; }?> </td>
                          
                            <!-- <td> @if($getcurrentcounselorbyusers->chat_type == 0) Async @else Live @endif  </td> -->
                            <td> {{ $getcurrentcounselorbyusers->report }}</td>
                            <td> {{ $getcurrentcounselorbyusers->counselor_name }}</td>

                            <td><?php if(isset($getcurrentcounselorbyusers->getUser->id)) { ?>
                                <a class="btn btn-gradient-primary btn-rounded btn-icon" href="{{ route('admin.counselor-assign-user-admin.counselorAssignUserAdmin', $getcurrentcounselorbyusers->getUser->id) }}">
                                    Activate
                                </a>
                                <input type="hidden" id="category_id" value="{{$getcurrentcounselorbyusers->getCategory->id}}">
                                <?php } else { echo '<p style="color:red;">Deleted</p>';} ?>
                            </td>
                        </tr>

                    @endforeach