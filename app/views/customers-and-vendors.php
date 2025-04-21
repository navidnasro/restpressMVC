<?php

use sellerhub\app\models\User;
use sellerhub\core\storage\Export;

?>
<div class="wrap">
    <h1 class="wp-heading-inline">لیست کاربران (مشتری و فروشنده)</h1>

    <!-- Buttons to filter users -->
    <form method="GET">
        <input type="hidden" name="show" value="customers-and-vendors">
        <input type="hidden" name="page" value="seller_club_dashboard">
        <button type="submit" name="user_type" value="vendor" class="button button-primary">
            فروشنده ها
        </button>
        <button type="submit" name="user_type" value="customer" class="button button-secondary">
            مشتری ها
        </button>
        <!-- Export Buttons -->
        <input id="export_to_excel" data-format="excel" type="button" name="export" value="excel" class="button button-primary">
        <input id="export_to_pdf" data-format="pdf" type="button" name="export" value="pdf" class="button button-secondary">
    </form>

    <?php
    // Get the user type from the URL query
    $userType = isset($_GET['user_type']) ? sanitize_text_field(strtolower($_GET['user_type'])) : 'vendor';

    // Set up pagination
    $perPage = 20; // Number of users per page
    $paged = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
    $offset = ($paged - 1) * $perPage;

    $users = User::where('role',$userType)
        ->orderBy('name','DESC')
        ->paginate($perPage,$paged);

    if ($users['data'])
    {
        $headers = ['نام و نام خانوادگی', 'شماره تماس', 'شهر', 'کدملی'];

        if ($userType == 'vendor')
        {
            $headers[] = 'تصاویر احراز هویت';
            $headers[] = 'نام و نام خانوادگی مشتری';
            $headers[] = 'شماره تماس مشتری';
            $headers[] = 'مدل دستگاه';
        }

        elseif ($userType == 'customer')
        {
            $headers[] = 'مدل دستگاه';
        }
        ?>
        <table class="wp-list-table widefat fixed striped users">
            <thead>
                <tr>
                    <?php
                    foreach ($headers as $header)
                    {
                        ?>
                        <th class="manage-column"><?php echo $header ?></th>
                        <?php
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $data = [];
                foreach ($users['data'] as $user)
                {
                    $products = $user->getProducts();

                    if ($userType == 'customer')
                    {
                        if (!empty($products))
                        {
                            foreach ($products as $product)
                            {
                                $data[] = [
                                    $user->name,
                                    $user->phone_number,
                                    $user->city,
                                    $user->national_id,
                                    $product->name
                                ];

                                ?>
                                <tr>
                                    <td><?php echo esc_html($user->name); ?></td>
                                    <td><?php echo esc_html($user->phone_number); ?></td>
                                    <td><?php echo esc_html($user->city); ?></td>
                                    <td><?php echo esc_html($user->national_id); ?></td>
                                    <td><?php echo esc_html($product->name); ?></td>
                                </tr>
                                <?php
                            }
                        }

                        else
                        {
                            $data[] = [
                                $user->name,
                                $user->phone_number,
                                $user->city,
                                $user->national_id,
                                ''
                            ];

                            ?>
                            <tr>
                                <td><?php echo esc_html($user->name); ?></td>
                                <td><?php echo esc_html($user->phone_number); ?></td>
                                <td><?php echo esc_html($user->city); ?></td>
                                <td><?php echo esc_html($user->national_id); ?></td>
                                <td></td>
                            </tr>
                            <?php
                        }
                    }

                    else if ($userType == 'vendor')
                    {
                        $customers = $user->getCustomers();

                        if ($customers)
                        {
                            foreach ($customers as $customer)
                            {
                                $customerProducts = $user->getProductsSoldTo($customer->id);

                                if (!empty($customerProducts))
                                {
                                    foreach ($customerProducts as $product)
                                    {
                                        $data[] = [
                                            $user->name,
                                            $user->phone_number,
                                            $user->city,
                                            $user->national_id,
                                            $customer->name,
                                            $customer->phone_number,
                                            $product->name
                                        ];

                                        ?>
                                        <tr>
                                            <td><?php echo esc_html($user->name); ?></td>
                                            <td><?php echo esc_html($user->phone_number); ?></td>
                                            <td><?php echo esc_html($user->city); ?></td>
                                            <td><?php echo esc_html($user->national_id); ?></td>
                                            <td><span class="vendor-proof-pics" data-id="<?php echo $user->id ?>">مشاهده تصاویر</span></td>
                                            <td><?php echo esc_html($customer->name); ?></td>
                                            <td><?php echo esc_html($customer->phone_number); ?></td>
                                            <td><?php echo esc_html($product->name); ?></td>
                                        </tr>
                                        <?php
                                    }
                                }

                                else
                                {
                                    $data[] = [
                                        $user->name,
                                        $user->phone_number,
                                        $user->city,
                                        $user->national_id,
                                        $customer->name,
                                        $customer->phone_number,
                                        ''
                                    ];

                                    ?>
                                    <tr>
                                        <td><?php echo esc_html($user->name); ?></td>
                                        <td><?php echo esc_html($user->phone_number); ?></td>
                                        <td><?php echo esc_html($user->city); ?></td>
                                        <td><?php echo esc_html($user->national_id); ?></td>
                                        <td><span class="vendor-proof-pics" data-id="<?php echo $user->id ?>">مشاهده تصاویر</span></td>
                                        <td><?php echo esc_html($customer->name); ?></td>
                                        <td><?php echo esc_html($customer->phone_number); ?></td>
                                        <td></td>
                                    </tr>
                                    <?php
                                }
                            }
                        }

                        else
                        {
                            $data[] = [
                                $user->name,
                                $user->phone_number,
                                $user->city,
                                $user->national_id,
                                '',
                                '',
                                ''
                            ];

                            ?>
                            <tr>
                                <td><?php echo esc_html($user->name); ?></td>
                                <td><?php echo esc_html($user->phone_number); ?></td>
                                <td><?php echo esc_html($user->city); ?></td>
                                <td><?php echo esc_html($user->national_id); ?></td>
                                <td><span class="vendor-proof-pics" data-id="<?php echo $user->id ?>">مشاهده تصاویر</span></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <?php
                        }
                        ?>
                            <div class="vendor-proof-pics-popup-bg" data-id="<?php echo $user->id ?>">
                                <div class="vendor-proof-pics-popup-bg-wrap">
                                    <div class="vendor-proof-pics-popup">
                                        <span class="vendor-proof-pics-popup-close">&times;</span>
                                        <?php
                                        $vendorProofImages = $user->getProofs();

                                        foreach ($vendorProofImages as $image)
                                        {
                                            ?>
                                            <div><img src="<?php echo $image->url ?>" alt="<?php echo $image->name ?>"></div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        <?php
                    }
                }
                ?>
            </tbody>
        </table>
        <div class="tablenav">
            <div class="pagination">
                <?php
                $totalUsers = $users['pagination']['total'];

                echo paginate_links(
                    [
                        'base'      => add_query_arg('paged', '%#%'),
                        'format'    => '',
                        'prev_text' => __('&laquo; قبلی'),
                        'next_text' => __('بعدی &raquo;'),
                        'total'     => $users['pagination']['total_pages'],
                        'current'   => $users['pagination']['current_page'],
                    ]
                );
                ?>
            </div>
        </div>
        <?php
    }

    else
    {
        echo '<p>کاربری یافت نشد</p>';
    }
    ?>
</div>

<script>
    jQuery(document).ready(function ($)
    {
        $('#export_to_excel, #export_to_pdf').on('click', function ()
        {
            var button = $(this);
            var format = button.data('format');

            $('.export_to_excel').prop('disabled', true);
            $('.export_to_pdf').prop('disabled', true);

            $.ajax({
                url: '<?php echo SiteUrl ?>/wp-json/api/user/export',
                type: 'POST',
                data: {
                    nonce: '<?php echo wp_create_nonce('admin_export_file') ?>',
                    format: format,
                    role: '<?php echo $userType ?>'
                },
                success: function (response)
                {
                    $('.export_to_excel').prop('disabled', false);
                    $('.export_to_pdf').prop('disabled', false);

                    alert(response.message);
                },
                error: function ()
                {
                    $('.export_to_excel').prop('disabled', false);
                    $('.export_to_pdf').prop('disabled', false);

                    alert('خطایی رخ داده!');
                }
            });
        });

        $('.vendor-proof-pics').on('click',function ()
        {
            var id = $(this).data('id');

            $('.vendor-proof-pics-popup-bg[data-id="'+id+'"]').fadeIn();
        });

        $('.vendor-proof-pics-popup-close').on('click',function ()
        {
            $(this).closest('.vendor-proof-pics-popup-bg').fadeOut();
        });
    });
</script>

<style>
    /* Container styling */
    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        list-style: none;
        padding: 0;
        margin: 20px 0;
    }

    /* Default link styling */
    .pagination a,
    .pagination span {
        display: block;
        padding: 10px 15px;
        margin: 0 5px;
        text-decoration: none;
        font-size: 14px;
        color: #0073aa; /* Default WordPress link color */
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #f9f9f9;
        transition: all 0.3s ease;
    }

    /* Hover and active state */
    .pagination a:hover,
    .pagination span.current {
        color: #fff;
        background-color: #0073aa; /* WordPress blue */
        border-color: #0073aa;
    }

    /* Current page styling */
    .pagination span.current {
        font-weight: bold;
        pointer-events: none; /* Disable interaction */
    }

    /* Disabled links (if applicable) */
    .pagination .disabled {
        color: #ccc;
        pointer-events: none;
        background-color: #f9f9f9;
        border-color: #ddd;
    }

    /* Responsive design */
    @media (max-width: 600px) {
        .pagination a,
        .pagination span {
            padding: 8px 10px;
            font-size: 12px;
            margin: 0 3px;
        }
    }

    td
    {
        vertical-align: middle !important;
    }

    .vendor-proof-pics-popup-bg
    {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.6);
        display: none;
    }

    .vendor-proof-pics-popup-bg-wrap
    {
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
    }

    .vendor-proof-pics-popup
    {
        width: 80%;
        height: 70%;
        overflow: auto;
        display: flex;
        align-items: center;
        flex-direction: column;
        background-color: white;
        padding: 20px;
        border-radius: 20px;
    }

    .vendor-proof-pics-popup div
    {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 15px 0;
    }

    .vendor-proof-pics-popup div:not(:last-child)
    {
        border-bottom: 1px solid;
    }

    .vendor-proof-pics-popup-close
    {
        align-self: start;
        font-size: 25px;
        background-color: #ff7f7f;
        padding: 6px;
        border-radius: 4px;
        color: white;
        cursor: pointer;
    }

    .vendor-proof-pics
    {
        cursor: pointer;
        padding: 8px;
        background-color: #a0e7ff;
        border-radius: 6px;
    }
</style>