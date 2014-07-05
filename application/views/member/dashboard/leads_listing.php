<tr class="intro">
                            <td colspan="3">Active Enquiry</td>
                        </tr>
                       <tr>
                            <th class="sortable" sort="u.first_name">Contact</th>
                            <th class="sortable" sort="u.mobile">Mobile</th>
                            <th class="sortable" sort="u.created_dtm">Days Old</th>
                            <th class="sortable" sort="notes_last_created">Date Last Note</th>
							<th class="sortable" sort="days_since_login">Last Login</th>
                        </tr>
                                                         <?php if($leads) : ?>
    <?php foreach($leads->result() as $user) : ?>
        <tr>
            <td><a href="<?php echo base_url() . "leads/detail/" . $user->user_id; ?>"><?php echo $user->first_name . " " . $user->last_name; ?></a></td>
            <td><?php echo $user->mobile; ?></td>
            <td style="text-align:center;"><?php echo get_days($user->created_dtm); ?></td>
            <td ><?php echo ($user->notes_last_created != '')? date('m/d/Y',strtotime($user->notes_last_created)):""; ?></td>
            <td><?php echo format_login_days($user->days_since_login); ?></td>
        </tr>
    <?php endforeach; ?>

                        
                        <?php endif; ?> 