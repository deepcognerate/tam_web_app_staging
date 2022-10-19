  <?php  $i = 1;?>
                    @foreach($getcurrentcounselorbyuserslives as $key => $getcurrentcounselorbyuserslive)
                        <tr data-entry-id="{{ $getcurrentcounselorbyuserslive->id }}">
                            <td></td>
                            <td><?php echo $i; ?> </td>
                            <td>{{ $getcurrentcounselorbyuserslive->getUser->name }} </td>
                            <td>{{ $getcurrentcounselorbyuserslive->getUser->age }}</td>
                            <td>{{ $getcurrentcounselorbyuserslive->getUser->gender }} </td>
                            <td>{{ $getcurrentcounselorbyuserslive->getCategory->category_name }} </td>
                            <td>{{ $getcurrentcounselorbyuserslive->getUser->location }} </td>
                            <td> {{ $i }} </td>
                            <!-- <td> @if($getcurrentcounselorbyuserslive->chat_type == 1) Live @else Async @endif </td> -->
                            <td> {{ $getcurrentcounselorbyuserslive->report }} </td>
                            <td> {{ $getcurrentcounselorbyuserslive->counselor_name }} </td>
                            <td>
                                <a class="btn btn-gradient-primary btn-rounded btn-icon" href="{{ route('admin.counselor-live-chat.counselorLiveChat', $getcurrentcounselorbyuserslive->user_id) }}">
                                    Activate
                                </a>
                            </td>
                        </tr>
                        <?php $i++ ;?>
                    @endforeach