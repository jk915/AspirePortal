  <tr class="intro">
                            <td colspan="3">Recent Reservations</td>
                        </tr>
                        <tr>
                            <th class="sortable" sort="p.address">Property</th>
                            <th class="sortable" sort="p.reserved_first_name">Investor</th>
                            <th class="sortable" sort="ts_reserved_date">Date of Reservation</th>
                        </tr>
                       
               
<?php if ($recent_reservations) : ?>
                    <?php foreach ($recent_reservations->result() AS $reservedProperty) : ?>
                        <tr>
                            <td>
                                <a href="<?php echo site_url("stocklist/detail/$reservedProperty->property_id")?>">
                                    Lot <?php echo trim("$reservedProperty->lot, $reservedProperty->address, $reservedProperty->suburb")?>
                                </a>
                            </td>
                            <td>
                                <a href="<?php echo site_url("investors/detail/$reservedProperty->investor_id")?>">
                                    <?php echo trim("$reservedProperty->reserved_first_name $reservedProperty->reserved_last_name")?>
                                </a>
                            </td>
                            <td>
                                <?php echo date('d/m/Y', $reservedProperty->ts_reserved_date);?>
                            </td>
                        </tr>
                    <?php endforeach;
                endif;  ?>