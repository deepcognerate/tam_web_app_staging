                    <?php   $i = 1; ?>
                    @foreach($counselorLiveChats as $key => $counselorLiveChat)
                        <tr data-entry-id="{{ $counselorLiveChat->id }}">
                            <td></td>
                            <td><?php echo $i; ?> </td>
                            <td>{{ $counselorLiveChat->getUser->name }} </td>
                            <td>{{ $counselorLiveChat->getUser->age }}</td>
                            <td>{{ $counselorLiveChat->getUser->gender }} </td>
                            <td>{{ $counselorLiveChat->getCategory->category_name }} </td>
                            <td>{{ $i }}</td>
                            <td>{{ $counselorLiveChat->getUser->location }} </td>
                            <td>Live</td>
                            <td>
                                <!-- <a class=" btn-gradient-primary btn-rounded round-button_active" href="{{ route('admin.counselor-live-chat.counselorLiveChat', $counselorLiveChat->user_id) }}">
                                    <i class="fa fa-play fa-2x"></i>
                                </a> -->

                                <a href="{{ route('admin.counselor-live-chat.counselorLiveChat', $counselorLiveChat->user_id) }}" class="round-button_active"><i class="fa fa-play fa-2x"></i></a>
                            </td>
                        </tr>
                        <?php $i++ ;?>
                    @endforeach