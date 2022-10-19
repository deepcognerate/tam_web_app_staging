                <?php   $i = 1; ?>
                    @if(!empty($counselorLiveChats))                    
                    @foreach($counselorLiveChats as $key => $counselorLiveChat)
                        <tr data-entry-id="{{ $counselorLiveChat->id }}">
                            <td></td>
                            <td><?php echo $i;?> </td>
                            <td>{{ $counselorLiveChat->getUser->name }} </td>
                            <td>{{ $counselorLiveChat->getUser->age }}</td>
                            <td>{{ $counselorLiveChat->getUser->gender }} </td>
                            <td>{{ $counselorLiveChat->getCategory->category_name }} </td>

                            <td><?php if(isset($counselorLiveChat->getUserCounselor->name) AND !empty($counselorLiveChat->getUserCounselor->name))
                            { echo $counselorLiveChat->getUserCounselor->name; } ?>
                            </td>
                            <td>{{ $counselorLiveChat->getUser->location }} </td>
                            <td>Live</td>
                           <td>
                                <a class=" iconeye btn btn-gradient-primary btn-rounded btn-icon" href="{{ route('admin.live-chat-view-admin.chat_view_show', $counselorLiveChat->id) }}">
                                           <i class="fa fa-eye" aria-hidden="true"></i>
                                        </a>
                            </td>
                        </tr>
                        <?php  $i++ ;?>
                    @endforeach
                    @endif