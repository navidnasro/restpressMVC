<div style="display: flex;
                    align-items: center;
                    flex-wrap: wrap;
                    justify-content: center;
                    background: white;
                    padding: 20px;
                    gap: 20px;">
    <form   action="<?php echo admin_url('admin.php'); ?>"
            style="display: flex;
                    align-items: center;
                    gap: 22px;">
        <input type="hidden" name="page" value="seller_club_dashboard">
<!--        <input type="hidden" name="show" value="withdraw-request">-->
        <div style="display: flex;
                    align-items: center;
                    background-color: #eeeeee;
                    border-radius: 10px;">
            <button type="submit"
                    name="show"
                    value="withdraw-request"
                    style="display: flex;
                            align-items: center;
                            background-color: #f0f0f1;
                            justify-content: center;
                            border: 1px dashed;
                            margin: 7px 7px;
                            cursor: pointer;
                            border-radius: 10px;">
                <h3 style="font-size: 16px">درخواست های برداشت</h3>
            </button>
        </div>
        <div style="display: flex;
                    align-items: center;
                    background-color: #eeeeee;
                    border-radius: 10px;">
            <button type="submit"
                    name="show"
                    value="guarantee-submission"
                    style="display: flex;
                            align-items: center;
                            background-color: #f0f0f1;
                            justify-content: center;
                            border: 1px dashed;
                            margin: 7px 7px;
                            cursor: pointer;
                            border-radius: 10px;">
                <h3 style="font-size: 16px">ثبت گارانتی</h3>
            </button>
        </div>
        <div style="display: flex;
                    align-items: center;
                    background-color: #eeeeee;
                    border-radius: 10px;">
            <button type="submit"
                    name="show"
                    value="invalid-guarantees"
                    style="display: flex;
                            align-items: center;
                            background-color: #f0f0f1;
                            justify-content: center;
                            border: 1px dashed;
                            margin: 7px 7px;
                            cursor: pointer;
                            border-radius: 10px;">
                <h3 style="font-size: 16px">گارانتی های فعال شده</h3>
            </button>
        </div>
        <div style="display: flex;
                    align-items: center;
                    background-color: #eeeeee;
                    border-radius: 10px;">
            <button type="submit"
                    name="show"
                    value="product-management"
                    style="display: flex;
                            align-items: center;
                            background-color: #f0f0f1;
                            justify-content: center;
                            border: 1px dashed;
                            margin: 7px 7px;
                            cursor: pointer;
                            border-radius: 10px;">
                <h3 style="font-size: 16px">مدیریت مشخصات محصولات</h3>
            </button>
        </div>
        <div style="display: flex;
                    align-items: center;
                    background-color: #eeeeee;
                    border-radius: 10px;">
            <button type="submit"
                    name="show"
                    value="customers-and-vendors"
                    style="display: flex;
                            align-items: center;
                            background-color: #f0f0f1;
                            justify-content: center;
                            border: 1px dashed;
                            margin: 7px 7px;
                            cursor: pointer;
                            border-radius: 10px;">
                <h3 style="font-size: 16px">لیست کاربران (مشتری و فروشنده)</h3>
            </button>
        </div>
    </form>
</div>