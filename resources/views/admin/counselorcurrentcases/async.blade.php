<?php   $i = 1; ?>
                    @foreach($counselorCurrentChats as $key => $counselorCurrentChat)
                        <!-- <tr data-entry-id="{{ $counselorCurrentChat->id }}">
                            <td></td>
                            <td><?php echo $i; $i++ ;?> </td>
                            <td>{{ $counselorCurrentChat->getUser->name }} </td>
                            <td>{{ $counselorCurrentChat->getUser->age }}</td>
                            <td>{{ $counselorCurrentChat->getUser->gender }} </td>
                            <td>{{ $counselorCurrentChat->getCategory->category_name }} </td>
                            <td> -->
                                <<!-- ?php $date=date_create($counselorCurrentChat->time_left);
                                        echo date_format($date,"H:i");
                                        ?> -->
                            <!-- </td>
                            <td>{{ $counselorCurrentChat->getUser->location }} </td>
                            <td>Async</td>
                            <td> -->
                                <!-- <a class=" btn-gradient-primary btn-rounded " href="">
                                    <i class="fa fa-play fa-2x "></i>
                                </a> -->
                               <!--  <a href="{{ route('admin.counselor-assign-user.counselorAssignUser', $counselorCurrentChat->getUser->id) }}" class="round-button_active"><i class="fa fa-play fa-2x"></i></a>
                                <input type="hidden" id="category_id" value="{{$counselorCurrentChat->getCategory->id}}">
                            </td>   --> 
                        </tr>
                    @endforeach