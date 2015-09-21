<?php

class ITwebexperts_Payperrentals_Helper_Config extends Mage_Core_Helper_Abstract
{
    
    const XML_PATH_HOTEL_MODE = 'payperrentals/store/hotel_mode';
    
    const XML_PATH_SHOW_DAY_GRID = 'payperrentals/calendar_options/show_day_grid';
    
    const XML_PATH_APPEARANCE_TIMEINCREMENTS = 'payperrentals/calendar_options/time_increment';
    
    const XML_PATH_CALENDAR_PADDING_DAYS = 'payperrentals/calendar_options/product_calendar_padding_days';
    
    const XML_PATH_STORE_OPEN_TIME = 'payperrentals/store/store_open_time';
    
    const XML_PATH_STORE_CLOSE_TIME = 'payperrentals/store/store_close_time';

    const XML_PATH_STORE_OPEN_TIME_MONDAY = 'payperrentals/store/store_open_time_monday';

    const XML_PATH_STORE_CLOSE_TIME_MONDAY = 'payperrentals/store/store_close_time_monday';

    const XML_PATH_STORE_OPEN_TIME_TUESDAY = 'payperrentals/store/store_open_time_tuesday';

    const XML_PATH_STORE_CLOSE_TIME_TUESDAY = 'payperrentals/store/store_close_time_tuesday';

    const XML_PATH_STORE_OPEN_TIME_WEDNESDAY = 'payperrentals/store/store_open_time_wednesday';

    const XML_PATH_STORE_CLOSE_TIME_WEDNESDAY = 'payperrentals/store/store_close_time_wednesday';

    const XML_PATH_STORE_OPEN_TIME_THURSDAY = 'payperrentals/store/store_open_time_thursday';

    const XML_PATH_STORE_CLOSE_TIME_THURSDAY = 'payperrentals/store/store_close_time_thursday';

    const XML_PATH_STORE_OPEN_TIME_FRIDAY = 'payperrentals/store/store_open_time_friday';

    const XML_PATH_STORE_CLOSE_TIME_FRIDAY = 'payperrentals/store/store_close_time_friday';

    const XML_PATH_STORE_OPEN_TIME_SATURDAY = 'payperrentals/store/store_open_time_saturday';

    const XML_PATH_STORE_CLOSE_TIME_SATURDAY = 'payperrentals/store/store_close_time_saturday';

    const XML_PATH_STORE_OPEN_TIME_SUNDAY = 'payperrentals/store/store_open_time_sunday';

    const XML_PATH_STORE_CLOSE_TIME_SUNDAY = 'payperrentals/store/store_close_time_sunday';


    const XML_PATH_TURNOVER_SHOW = 'payperrentals/appearance_turnover/show_turnover';
    
    const XML_PATH_TURNOVER_AFTER_NUMBER = 'payperrentals/appearance_turnover/turnover_after_number';
    
    const XML_PATH_TURNOVER_AFTER_TYPE = 'payperrentals/appearance_turnover/turnover_after_type';
    
    const XML_PATH_TURNOVER_BEFORE_NUMBER = 'payperrentals/appearance_turnover/turnover_before_number';
    
    const XML_PATH_TURNOVER_BEFORE_TYPE = 'payperrentals/appearance_turnover/turnover_before_type';

    const XML_PATH_EXCLUDE_DISABLED_DAYS_FROM_TURNOVER = 'payperrentals/appearance_turnover/exclude_disabled_from_turnover';
    
    const XML_PATH_HIDE_BOOKED_IN_GRID = 'payperrentals/appearance_inventory/hide_booked_inventory_grid';
    
    const XML_PATH_SORT_PRICE_LIST_CONFIG = 'payperrentals/appearance_price/sort_price_list';
    
    const XML_PATH_SORT_PRICE_LIST_TYPE = 'payperrentals/appearance_price/sort_price_list_type';
    
    const XML_PATH_GLOBAL_DATES_EXCLUDE = 'payperrentals/store/global_dates_excluded';

    const XML_PATH_DISABLE_RENT_ON_GLOBAL_IF_DATES_NOT_SELECTED = 'payperrentals/store/disable_clicking_rent_if_dates_not_selected';
    
    const XML_PATH_APPEARANCE_CALENDAR_PAGES = 'payperrentals/listing/nr_calendars';

    const XML_PATH_SHOW_NEXT_AVAILABLE_DATE_ON_LISTING = 'payperrentals/listing/show_next_available_date_listing';

    const XML_PATH_SHOW_NEXT_AVAILABLE_DATE_ON_VIEW = 'payperrentals/listing/show_next_available_date_view';

    const XML_PATH_SHOW_BUYOUT_PRICE = 'payperrentals/listing/show_buyout_price';

    const XML_PATH_KEEP_LISTING_PRICE_AFTER_SELECTION = 'payperrentals/listing/keep_listing_price_after_selection';

    const XML_PATH_SHOW_CALENDAR_PRODUCT_INFO = 'payperrentals/global/show_calendar_product_info';

    const XML_PATH_USE_NONSEQUENTIAL = 'payperrentals/global/use_nonsequential_dates';

    const XML_PATH_HIDE_PRODUCTS_NOT_AVAILABLE = 'payperrentals/global/hide_products_not_available';
    const XML_PATH_SHOW_AVAILABILITY_ON_LISTING = 'payperrentals/global/show_availability_on_listing';

    
    const XML_PATH_CATEGORY_MEMBERSHIP = 'payperrentals/rental/category_membership';
    
    const XML_PATH_FORCE_USE_TIMES = 'payperrentals/create_order_admin/admin_time_dropdown';
    
    const XML_PATH_DEPOSIT_CHECKOUT = 'payperrentals/deposit/deposit_checkout';

    const XML_PATH_SHOW_MIN_MAX_DETAILS_PAGE = 'payperrentals/appearance_period/show_min_max_details_page';
    
    const XML_PATH_USE_MINIMUM_RENTAL_PERIOD = 'payperrentals/appearance_period/use_minimum_rental_period';
	 
    const XML_PATH_HIDE_TIME_PERIOD_NUMBERS = 'payperrentals/listing/hide_time_periods_numbers';
    const XML_PATH_RESERVE_INVENTORY_NOINVOICE = 'payperrentals/appearance_inventory/reserve_inventory_noinvoice';
    const XML_PATH_USE_RESERVE_INVENTORY_SEND_RETURN = 'payperrentals/appearance_inventory/use_reserve_inventory_send_return';
    const XML_PATH_USE_RESERVE_INVENTORY_DROPOFF_PICKUP = 'payperrentals/appearance_inventory/use_reserve_inventory_dropoff_pickup';
    const XML_PATH_USE_RESERVEBY_STATUS = 'payperrentals/appearance_inventory/use_reserveby_status';
    const XML_PATH_RESERVED_STATUSES = 'payperrentals/appearance_inventory/reserved_statuses';
    const XML_PATH_RESERVATION_STATUS = 'payperrentals/appearance_inventory/reservation_status';
    const XML_PATH_FIXED_SELECTION = 'payperrentals/listing/fixed_selection';
    const XML_PATH_HOUR_NEXT_DAY = 'payperrentals/listing/hour_next_day';
    const XML_PATH_ADD_START_DATE = 'payperrentals/listing/add_start_date';
    const XML_PATH_NO_CALENDAR_USE_TODAY_WITH_FIXED = 'payperrentals/listing/no_calendar_use_today_with_fixed_selection';
    const XML_PATH_PRICE_NO_DISABLED_DAYS = 'payperrentals/store/price_no_disabled_days';
    const XML_PATH_REMOVE_SHIPPING = 'payperrentals/checkout_options/remove_shipping';
    const XML_PATH_DEPOSIT_TYPE = 'payperrentals/checkout_options/deposit_type';
    const XML_PATH_HIDE_ADDRESSES = 'payperrentals/advanced_not_used/hide_addresses';
    const XML_PATH_USE_LIST_BUTTONS = 'payperrentals/global/use_list_buttons';
    const XML_PATH_GLOBAL_DATES_WITH_TIME = 'payperrentals/global/use_pprbox_shopping_cart_time';
    const XML_PATH_USE_GLOBAL_CALENDAR_TIME = 'payperrentals/global/use_global_calendar_time';
    const XML_PATH_MIN_NUMBER = 'payperrentals/appearance_period/min_period_number';
    const XML_PATH_MIN_TYPE = 'payperrentals/appearance_period/min_period_type';
    const XML_PATH_MAX_NUMBER = 'payperrentals/appearance_period/max_period_number';
    const XML_PATH_MAX_TYPE = 'payperrentals/appearance_period/max_period_type';
    const XML_PATH_MIN_QTY_ALLOWED = 'payperrentals/appearance_period/min_qty_allowed';
    const XML_PATH_MAX_QTY_ALLOWED = 'payperrentals/appearance_period/max_qty_allowed';
    const XML_PATH_TIME_INCREMENT = 'payperrentals/calendar_options/time_increment';
    const XML_PATH_ADDTIME_NUMBER = 'payperrentals/appearance_price/addtime_number';
    const XML_PATH_ADDTIME_TYPE = 'payperrentals/appearance_price/addtime_type';
    const XML_PATH_DISABLED_DAYS_WEEK = 'payperrentals/store/disabled_days_week';
    const XML_PATH_DISABLED_DAYS_WEEK_FROM = 'payperrentals/store/disabled_days_week_from';
    const XML_PATH_DISABLED_DAYS_WEEK_START = 'payperrentals/store/disabled_days_week_start';
    const XML_PATH_DISABLED_DAYS_WEEK_END = 'payperrentals/store/disabled_days_week_end';
    const XML_PATH_USE_GLOBAL_DAYS = 'payperrentals/calendar_options/use_global_days';

    const XML_PATH_MAX_EXTENSION_NUMBER = 'payperrentals/extend_order/max_extension_period_number';
    const XML_PATH_MAX_EXTENSION_TYPE = 'payperrentals/extend_order/max_extension_period_type';
    const XML_PATH_ENABLE_EXTEND = 'payperrentals/extend_order/enable_extend';

    const XML_PATH_STORE_TIME_FORMAT = 'payperrentals/store/time_format';


    /**
     *deprecated to be removed. Only site that should use it is ronshire
     */
    const XML_PATH_GLOBAL_DATES_SHOW_MIDNIGHT = 'payperrentals/global/show_midnight_global_calendar';
    const XML_PATH_USE_GLOBAL_CALENDAR_PADDING_DAYS = 'payperrentals/global/global_calendar_padding_days';
    const XML_PATH_GLOBAL_DATES_CART_ENABLED = 'payperrentals/global/use_pprbox_shopping_cart';
    const XML_PATH_USE_CATEGORIES_BOX = 'payperrentals/global/use_categories_box';
    const XML_PATH_THEME_CALENDAR_STYLE = 'payperrentals/calendar_options/theme_calendar_style';
    const XML_PATH_USE_MODIFIED_ORDER_CREATOR = 'payperrentals/advanced_not_used/use_modified_order_creator';
    const XML_PATH_STARTEND_SHOW_AS_TEXT = 'payperrentals/advanced_not_used/show_start_end_text_on_editor';
    const XML_PATH_STARTEND_SHOW_AS_COLUMNS = 'payperrentals/advanced_not_used/show_start_end_columns_on_editor';
    const XML_PATH_PRICING_ON_LISTING = 'payperrentals/listing/prices_on_listing';
    const XML_PATH_ALLOW_LISTING_QTY = 'payperrentals/listing/allow_listing_qty';

    const XML_PATH_SHOW_ADMIN_OVERBOOK_WARNING = 'payperrentals/overbooking_admin/show_admin_overbook_warning';
    const XML_PATH_ALLOW_ADMIN_OVERBOOK = 'payperrentals/overbooking_admin/allow_admin_overbook';

    const XML_PATH_ALL_PRODUCTS_SAME_DATES = 'payperrentals/calendar_options/enforce_same_dates';
    const XML_PATH_TURNOVER = 'payperrentals/admin_calendarreport/enddate';
    const XML_PATH_LISTING_SHOWPRICE = 'payperrentals/listing/show_price';
    const XML_PATH_ADDRESSFIRST = 'payperrentals/rental/addressfirst';
    const XML_PATH_INCLUDEPRODUCT = 'payperrentals/rental/includeproduct';


    /**
     * Include product name on rental queue ship labels
     *
     * @return bool
     */
    public function includeProduct()
    {
        if (Mage::getStoreConfig(self::XML_PATH_INCLUDEPRODUCT) != 0) {
            return true;
        }
        return false;
    }


    /**
     * Show address before product name on rental queue ship labels
     *
     * @return bool
     */
    public function addressFirst()
    {
        if (Mage::getStoreConfig(self::XML_PATH_ADDRESSFIRST) != 0) {
            return true;
        }
        return false;
    }

    /**
     * Returns turnover if end date should use turnover returns withoutturnover for without
     *
     * @return mixed
     */
    public function useTurnover()
    {
        return Mage::getStoreConfig(self::XML_PATH_TURNOVER);
    }

    /**
     * How many price points to show on product page
     * */

    public function showPrice()
    {
        return intval(Mage::getStoreConfig(self::XML_PATH_LISTING_SHOWPRICE));
    }

    /**
     * @return bool
     */
    public static function reserveInventoryNoInvoice()
    {
        if (Mage::getStoreConfig(self::XML_PATH_RESERVE_INVENTORY_NOINVOICE) != 0) {
            return true;
        }
        return false;
    }

    /**
     * Reserve Inventory By Order Status True or False
     *
     * @return bool
     */
    public static function reserveByStatus()
    {
        if (Mage::getStoreConfig(self::XML_PATH_USE_RESERVEBY_STATUS) != 0) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public static function useReserveInventorySendReturn()
    {
        if (Mage::getStoreConfig(self::XML_PATH_USE_RESERVE_INVENTORY_SEND_RETURN)
            != 0
        ) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public static function useReserveInventoryDropoffPickup()
    {
        if (Mage::getStoreConfig(self::XML_PATH_USE_RESERVE_INVENTORY_DROPOFF_PICKUP)
            != 0
        ) {
            return true;
        }
        return false;
    }

    /**
     * Function used for sso
     *
*@return array|bool
     */
    public static function getFixedSelection()
    {
        if (Mage::getStoreConfig(self::XML_PATH_FIXED_SELECTION) != '') {
            $arr = explode(',', Mage::getStoreConfig(self::XML_PATH_FIXED_SELECTION));
            $fixedArr = array();
            foreach ($arr as $fixed) {
                if (is_numeric($fixed)) {
                    $fixedArr[] = $fixed;
                }
            }
            return $fixedArr;
        }
        return false;
    }/**
     * Function to check if next day will be used if passed a specific hour
     *
*@return bool
 */
    public static function isNextHourSelection()
    {
        if (Mage::getStoreConfig(self::XML_PATH_HOUR_NEXT_DAY) != '') {
            $nextHour = strtotime(
                date('Y-m-d', Mage::getModel('core/date')->timestamp(time())) . ' ' . Mage::getStoreConfig(
                    self::XML_PATH_HOUR_NEXT_DAY
                )
            );
            $curTime = Mage::getModel('core/date')->timestamp(time());
            if ($curTime > $nextHour) {
                return true;
            }
        }
        return false;
    }
    /**
     * Function to check if add start date is enabled
     *
*@return bool
 */
    public static function isAddStartDate()
    {
        if (Mage::getStoreConfig(self::XML_PATH_ADD_START_DATE) != 0) {
            return true;
        }
        return false;
    }
    /**
     * Function to check if sso is enabled
     *
        *@return bool
    */
    public static function NoCalendarUseTodayAsStartDate()
    {
        if (Mage::getStoreConfig(self::XML_PATH_NO_CALENDAR_USE_TODAY_WITH_FIXED) != 0) {
            return true;
        }
        return false;
    }
    /**
     * Function to check if disabled days are included in price calculatio\n
     *
     *@return bool
     */
    public static function countPriceForDisabledDays()
    {
        if (Mage::getStoreConfig(self::XML_PATH_PRICE_NO_DISABLED_DAYS) != 0) {
            return false;
        }
        return true;
    }/**
     * Function to check if removing shipping address from checkout is enabled
     *
*@return bool
 */
    public static function removeShipping()
    {
        if (Mage::getStoreConfig(self::XML_PATH_REMOVE_SHIPPING) != 0) {
            return true;
        }
        return false;
    }/**
     * Function to check if hide addresses from create order in admin
     *
*@return bool
 */
    public static function hideAdminAddresses()
    {
        if (Mage::getStoreConfig(self::XML_PATH_HIDE_ADDRESSES) != 0) {
            return true;
        }
        return false;
    }
    /**
     * Function to check if nonsequential dates is enabled
     * This feature is disabled for the moment because is not tested.
     *@return bool
    */
    public static function useNonSequential()
    {
        /*if (Mage::getStoreConfig(self::XML_PATH_USE_NONSEQUENTIAL) != 0) {
            return true;
        }*/
        return false;
    }

    public static function useListButtons()
    {
        if (Mage::getStoreConfig(self::XML_PATH_USE_LIST_BUTTONS) != 0) {
            return true;
        }
        return false;
    }

    public static function useGlobalShoppingCartWithTime()
    {
        return Mage::getStoreConfig(self::XML_PATH_GLOBAL_DATES_WITH_TIME);
    }

    public static function useGlobalDatesWithTime()
    {
        return Mage::getStoreConfig(self::XML_PATH_USE_GLOBAL_CALENDAR_TIME);
    }

    /**
     * Check store hotel mode
     * @param int $storeId
     * @return bool
     * */
    public function isHotelMode($storeId = null)
    {
        if ($storeId) {
            return (bool)(int)Mage::getStoreConfig(self::XML_PATH_HOTEL_MODE, $storeId);
        } else {
            return (bool)(int)Mage::getStoreConfig(self::XML_PATH_HOTEL_MODE);
        }
    }

    /**
     * Is show time grid
     * @param int $storeId
     * @return bool
     * */
    public function isShowTimeGrid($storeId = null)
    {
        if ($storeId) {
            return (bool)(int)Mage::getStoreConfig(self::XML_PATH_SHOW_DAY_GRID, $storeId);
        } else {
            return (bool)(int)Mage::getStoreConfig(self::XML_PATH_SHOW_DAY_GRID);
        }
    }

    /**
     * Hide not available products on listing
     * @param int $storeId
     * @return bool
     * */
    public function hideProductsNotAvailable($storeId = null)
    {
        if ($storeId) {
            return (bool)(int)Mage::getStoreConfig(self::XML_PATH_HIDE_PRODUCTS_NOT_AVAILABLE, $storeId);
        } else {
            return (bool)(int)Mage::getStoreConfig(self::XML_PATH_HIDE_PRODUCTS_NOT_AVAILABLE);
        }
    }

    /**
     * Show availability on listing
     * @param int $storeId
     * @return bool
     * */
    public function showAvailabilityOnProductListing($storeId = null)
    {
        if ($storeId) {
            return (bool)(int)Mage::getStoreConfig(self::XML_PATH_SHOW_AVAILABILITY_ON_LISTING, $storeId);
        } else {
            return (bool)(int)Mage::getStoreConfig(self::XML_PATH_SHOW_AVAILABILITY_ON_LISTING);
        }
    }

    /**
     * Show availability on listing
     * @param int $storeId
     * @return bool
     * */
    public function allowAdminOverbook($storeId = null)
    {
        if ($storeId) {
            return (bool)(int)Mage::getStoreConfig(self::XML_PATH_ALLOW_ADMIN_OVERBOOK, $storeId);
        } else {
            return (bool)(int)Mage::getStoreConfig(self::XML_PATH_ALLOW_ADMIN_OVERBOOK);
        }
    }

    /**
     * Show availability on listing
     * @param int $storeId
     * @return bool
     * */
    public function showAdminOverbookWarning($storeId = null)
    {
        if ($storeId) {
            return (bool)(int)Mage::getStoreConfig(self::XML_PATH_SHOW_ADMIN_OVERBOOK_WARNING, $storeId);
        } else {
            return (bool)(int)Mage::getStoreConfig(self::XML_PATH_SHOW_ADMIN_OVERBOOK_WARNING);
        }
    }

    /**
     * Get time increment value
     * @param int $storeId
     * @return int
     * */
    public function getTimeIncrement($storeId = null)
    {
        if ($storeId) {
            return (int)Mage::getStoreConfig(self::XML_PATH_APPEARANCE_TIMEINCREMENTS, $storeId);
        } else {
            return (int)Mage::getStoreConfig(self::XML_PATH_APPEARANCE_TIMEINCREMENTS);
        }
    }

    /**
     * Get Store Time
     * @param int $storeId
     * @return array
     * */
    public function getStoreTime($storeId = null)
    {
        if ($storeId) {
            return array(
                Mage::getStoreConfig(self::XML_PATH_STORE_OPEN_TIME, $storeId),
                Mage::getStoreConfig(self::XML_PATH_STORE_CLOSE_TIME, $storeId)
            );
        } else {
            return array(
                Mage::getStoreConfig(self::XML_PATH_STORE_OPEN_TIME),
                Mage::getStoreConfig(self::XML_PATH_STORE_CLOSE_TIME)
            );
        }
    }

    /**
     * Get Store Time
     * @param int $storeId
     * @return array
     * */
    public function getStoreTimeMonday($storeId = null)
    {
        if ($storeId) {
            return array(
                Mage::getStoreConfig(self::XML_PATH_STORE_OPEN_TIME_MONDAY, $storeId),
                Mage::getStoreConfig(self::XML_PATH_STORE_CLOSE_TIME_MONDAY, $storeId)
            );
        } else {
            return array(
                Mage::getStoreConfig(self::XML_PATH_STORE_OPEN_TIME_MONDAY),
                Mage::getStoreConfig(self::XML_PATH_STORE_CLOSE_TIME_MONDAY)
            );
        }
    }

    /**
     * Get Store Time
     * @param int $storeId
     * @return array
     * */
    public function getStoreTimeTuesday($storeId = null)
    {
        if ($storeId) {
            return array(
                Mage::getStoreConfig(self::XML_PATH_STORE_OPEN_TIME_TUESDAY, $storeId),
                Mage::getStoreConfig(self::XML_PATH_STORE_CLOSE_TIME_TUESDAY, $storeId)
            );
        } else {
            return array(
                Mage::getStoreConfig(self::XML_PATH_STORE_OPEN_TIME_TUESDAY),
                Mage::getStoreConfig(self::XML_PATH_STORE_CLOSE_TIME_TUESDAY)
            );
        }
    }

    /**
     * Get Store Time
     * @param int $storeId
     * @return array
     * */
    public function getStoreTimeWednesday($storeId = null)
    {
        if ($storeId) {
            return array(
                Mage::getStoreConfig(self::XML_PATH_STORE_OPEN_TIME_WEDNESDAY, $storeId),
                Mage::getStoreConfig(self::XML_PATH_STORE_CLOSE_TIME_WEDNESDAY, $storeId)
            );
        } else {
            return array(
                Mage::getStoreConfig(self::XML_PATH_STORE_OPEN_TIME_WEDNESDAY),
                Mage::getStoreConfig(self::XML_PATH_STORE_CLOSE_TIME_WEDNESDAY)
            );
        }
    }

    /**
     * Get Store Time
     * @param int $storeId
     * @return array
     * */
    public function getStoreTimeThursday($storeId = null)
    {
        if ($storeId) {
            return array(
                Mage::getStoreConfig(self::XML_PATH_STORE_OPEN_TIME_THURSDAY, $storeId),
                Mage::getStoreConfig(self::XML_PATH_STORE_CLOSE_TIME_THURSDAY, $storeId)
            );
        } else {
            return array(
                Mage::getStoreConfig(self::XML_PATH_STORE_OPEN_TIME_THURSDAY),
                Mage::getStoreConfig(self::XML_PATH_STORE_CLOSE_TIME_THURSDAY)
            );
        }
    }

    /**
     * Get Store Time
     * @param int $storeId
     * @return array
     * */
    public function getStoreTimeFriday($storeId = null)
    {
        if ($storeId) {
            return array(
                Mage::getStoreConfig(self::XML_PATH_STORE_OPEN_TIME_FRIDAY, $storeId),
                Mage::getStoreConfig(self::XML_PATH_STORE_CLOSE_TIME_FRIDAY, $storeId)
            );
        } else {
            return array(
                Mage::getStoreConfig(self::XML_PATH_STORE_OPEN_TIME_FRIDAY),
                Mage::getStoreConfig(self::XML_PATH_STORE_CLOSE_TIME_FRIDAY)
            );
        }
    }

    /**
     * Get Store Time
     * @param int $storeId
     * @return array
     * */
    public function getStoreTimeSaturday($storeId = null)
    {
        if ($storeId) {
            return array(
                Mage::getStoreConfig(self::XML_PATH_STORE_OPEN_TIME_SATURDAY, $storeId),
                Mage::getStoreConfig(self::XML_PATH_STORE_CLOSE_TIME_SATURDAY, $storeId)
            );
        } else {
            return array(
                Mage::getStoreConfig(self::XML_PATH_STORE_OPEN_TIME_SATURDAY),
                Mage::getStoreConfig(self::XML_PATH_STORE_CLOSE_TIME_SATURDAY)
            );
        }
    }

    /**
     * Get Store Time
     * @param int $storeId
     * @return array
     * */
    public function getStoreTimeSunday($storeId = null)
    {
        if ($storeId) {
            return array(
                Mage::getStoreConfig(self::XML_PATH_STORE_OPEN_TIME_SUNDAY, $storeId),
                Mage::getStoreConfig(self::XML_PATH_STORE_CLOSE_TIME_SUNDAY, $storeId)
            );
        } else {
            return array(
                Mage::getStoreConfig(self::XML_PATH_STORE_OPEN_TIME_SUNDAY),
                Mage::getStoreConfig(self::XML_PATH_STORE_CLOSE_TIME_SUNDAY)
            );
        }
    }

    /**
     * @param      $productId
     * @param null $storeId
     *
     * @return string
     */

    public static function getMaximumMessageText($productId, $storeId = null){
        if ($productId > 0 && ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($productId, 'global_max_period') == 0) {
            $payperrentalsMaxNumber = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($productId, 'payperrentals_max_number');
            $payperrentalsMaxType = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($productId, 'payperrentals_max_type');
            $textMax = ITwebexperts_Payperrentals_Helper_Html::getTextForType(
                $payperrentalsMaxNumber,
                $payperrentalsMaxType
            );
        } else {
            $textMax = ITwebexperts_Payperrentals_Helper_Html::getTextForType(
                Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_MAX_NUMBER, $storeId),
                Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_MAX_TYPE, $storeId)
            );
        }

        return $textMax;
    }

    /**
     * @param      $productId
     * @param null $storeId
     *
     * @return string
     */

    public static function getMinimumMessageText($productId, $storeId = null){
        if ($productId > 0 && ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($productId, 'global_min_period') == 0) {
            $payperrentalsMinNumber = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($productId, 'payperrentals_min_number');
            $payperrentalsMinType = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($productId, 'payperrentals_min_type');
            $textMin = ITwebexperts_Payperrentals_Helper_Html::getTextForType(
                $payperrentalsMinNumber,
                $payperrentalsMinType
            );
        } else {
            $textMin = ITwebexperts_Payperrentals_Helper_Html::getTextForType(
                Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_MIN_NUMBER, $storeId),
                Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_MIN_TYPE, $storeId)
            );
        }

        return $textMin;
    }



    /**
     * @param      $productId
     * @param null $storeId
     * @param bool $forcePeriod
     *
     * @return int
     */

    public static function getMinimumPeriod($productId, $storeId = null, $forcePeriod = false){
        if ($productId > 0 && ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($productId, 'global_min_period') == 0) {
            $payperrentalsMinNumber = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($productId, 'payperrentals_min_number');
            $payperrentalsMinType = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($productId, 'payperrentals_min_type');
            $periodInSecond = ITwebexperts_Payperrentals_Helper_Data::getPeriodInSeconds(
                $payperrentalsMinNumber, $payperrentalsMinType
            );
        } else {
            $periodInSecond = ITwebexperts_Payperrentals_Helper_Data::getPeriodInSeconds(
                Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_MIN_NUMBER, $storeId),
                Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_MIN_TYPE, $storeId)
            );
        }
        if (!$periodInSecond && $forcePeriod) {
            $minPeriodValue = Mage::getStoreConfig(
                ITwebexperts_Payperrentals_Helper_Config::XML_PATH_TIME_INCREMENT
            );
            $minPeriodType = ITwebexperts_Payperrentals_Model_Product_Periodtype::MINUTES;
            $periodInSecond = ITwebexperts_Payperrentals_Helper_Data::getPeriodInSeconds(
                $minPeriodValue, $minPeriodType
            );
        }
        return $periodInSecond;
    }

    /**
     * @param      $productId
     * @param null $storeId
     *
     * @return int
     */

    public static function getMaximumPeriod($productId, $storeId = null){
        if ($productId > 0 && ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($productId, 'global_max_period') == 0) {
            $payperrentalsMaxNumber = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($productId, 'payperrentals_max_number');
            $payperrentalsMaxType = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($productId, 'payperrentals_max_type');
            $periodInSecond = ITwebexperts_Payperrentals_Helper_Data::getPeriodInSeconds(
                $payperrentalsMaxNumber, $payperrentalsMaxType
            );
        } else {
            $periodInSecond = ITwebexperts_Payperrentals_Helper_Data::getPeriodInSeconds(
                Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_MAX_NUMBER, $storeId),
                Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_MAX_TYPE, $storeId)
            );
        }

        return $periodInSecond;
    }

    /**
     * Return in seconds
     * @param      $productId
     * @param null $storeId
     *
     * @return float
     */

    public static function getTurnoverTimeBefore($productId, $storeId = null){
        if (isset($productId) && ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($productId, 'global_turnover_before') == 0) {
            $payperrentalsAvailNumberb = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($productId, 'payperrentals_avail_numberb');
            $payperrentalsAvailTypeb = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($productId, 'payperrentals_avail_typeb');
            $turnoverTimeBefore = ITwebexperts_Payperrentals_Helper_Data::getPeriodInSeconds(
                    $payperrentalsAvailNumberb, $payperrentalsAvailTypeb
                );
        } else {
            $turnoverTimeBefore = ITwebexperts_Payperrentals_Helper_Data::getPeriodInSeconds(
                    Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_TURNOVER_BEFORE_NUMBER, $storeId),
                    Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_TURNOVER_BEFORE_TYPE, $storeId)
                );
        }
        return $turnoverTimeBefore;
    }

    /**
     * Return in seconds
     * @param      $productId
     * @param null $storeId
     *
     * @return float
     */
    public static function getTurnoverTimeAfter($productId, $storeId = null){
        if (isset($productId) && ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($productId, 'global_turnover_after') == 0) {
            $payperrentalsAvailNumberb = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($productId, 'payperrentals_avail_number');
            $payperrentalsAvailTypeb = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($productId, 'payperrentals_avail_type');
            $turnoverTimeBefore = ITwebexperts_Payperrentals_Helper_Data::getPeriodInSeconds(
                    $payperrentalsAvailNumberb, $payperrentalsAvailTypeb
                ) ;
        } else {
            $turnoverTimeBefore = ITwebexperts_Payperrentals_Helper_Data::getPeriodInSeconds(
                    Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_TURNOVER_AFTER_NUMBER, $storeId),
                    Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_TURNOVER_AFTER_TYPE, $storeId)
                );
        }
        return $turnoverTimeBefore;
    }

    /**
     * Get calendar padding days
     * @param int $storeId
     * @return int
     * */
    public function getCalendarPaddingDays($storeId = null)
    {
        if ($storeId) {
            return (int)Mage::getStoreConfig(self::XML_PATH_CALENDAR_PADDING_DAYS, $storeId);
        } else {
            return (int)Mage::getStoreConfig(self::XML_PATH_CALENDAR_PADDING_DAYS);
        }
    }

    /**
     * Get global exclude dates
     * @param int $storeId
     * @return int
     * */
    public function getGlobalExcludeDates($storeId = null)
    {
        if ($storeId) {
            return Mage::getStoreConfig(self::XML_PATH_GLOBAL_DATES_EXCLUDE, $storeId);
        } else {
            return Mage::getStoreConfig(self::XML_PATH_GLOBAL_DATES_EXCLUDE);
        }
    }

    /**
     * Get number of calendars to show
     * @param int $storeId
     * @return int
     * */
    public function getMonthNumberConfig($storeId = null)
    {
        if ($storeId) {
            return Mage::getStoreConfig(self::XML_PATH_APPEARANCE_CALENDAR_PAGES, $storeId);
        } else {
            return Mage::getStoreConfig(self::XML_PATH_APPEARANCE_CALENDAR_PAGES);
        }
    }

    /**
     * returns theme calendar style
     * @param int $storeId
     * @return int
     * */
    public function getThemeStyle($storeId = null)
    {
        if (!Mage::app()->getStore()->isAdmin()) {
            if ($storeId) {
                return Mage::getStoreConfig(self::XML_PATH_THEME_CALENDAR_STYLE, $storeId);
            } else {
                return Mage::getStoreConfig(self::XML_PATH_THEME_CALENDAR_STYLE);
            }
        }else{
            return ITwebexperts_Payperrentals_Model_Source_Themestyle::DEFAULT_STYLE;
        }
    }

    /**
     * This feature is disabled for the moment
     * Check using non sequential calendar select
     * @param int $storeId
     * @return bool
     * */
    public function isNonSequentialSelect($storeId = null)
    {
        /*if ($storeId) {
            return (bool)Mage::getStoreConfig(self::XML_PATH_USE_NONSEQUENTIAL, $storeId);
        } else {
            return (bool)Mage::getStoreConfig(self::XML_PATH_USE_NONSEQUENTIAL);
        }*/
        return false;

    }

    public function forceUseTimes($storeId = null)
    {
        if ($storeId) {
            return (bool)Mage::getStoreConfig(self::XML_PATH_FORCE_USE_TIMES, $storeId);
        } else {
            return (bool)Mage::getStoreConfig(self::XML_PATH_FORCE_USE_TIMES);
        }
    }

    /**
     * Check hiding booked/available inventory in grid
     * @param int $storeId
     * @return int
     * */
    public function hideBookedInventoryInGrid($storeId = null)
    {
        if ($storeId) {
            return (bool)Mage::getStoreConfig(self::XML_PATH_HIDE_BOOKED_IN_GRID, $storeId);
        } else {
            return (bool)Mage::getStoreConfig(self::XML_PATH_HIDE_BOOKED_IN_GRID);
        }
    }

    /**
     * Get Membership category id
     * @param int $storeId
     * @return int
     * */
    public function getMembershipCategoryId($storeId = null)
    {
        if ($storeId) {
            return Mage::getStoreConfig(self::XML_PATH_CATEGORY_MEMBERSHIP, $storeId);
        } else {
            return Mage::getStoreConfig(self::XML_PATH_CATEGORY_MEMBERSHIP);
        }
    }

    /**
     * Get sort price list config
     * @param int $storeId
     * @return int
     * */
    public function getSortPriceListConfig($storeId = null)
    {
        if ($storeId) {
            return (bool)Mage::getStoreConfig(self::XML_PATH_SORT_PRICE_LIST_CONFIG, $storeId);
        } else {
            return (bool)Mage::getStoreConfig(self::XML_PATH_SORT_PRICE_LIST_CONFIG);
        }
    }

    /**
     * Get sort price list type
     * @param int $storeId
     * @return int
     * */
    public function getSortPriceListType($storeId = null)
    {
        if ($storeId) {
            return Mage::getStoreConfig(self::XML_PATH_SORT_PRICE_LIST_TYPE, $storeId);
        } else {
            return Mage::getStoreConfig(self::XML_PATH_SORT_PRICE_LIST_TYPE);
        }
    }
    /**
     * Show min max on product details page
     * @param int $_storeId
     * @return bool
     * */
    public function showMinMaxProductDetailsPage($_storeId = null)
    {
        if ($_storeId) {
            return (bool)(int)Mage::getStoreConfig(self::XML_PATH_SHOW_MIN_MAX_DETAILS_PAGE, $_storeId);
        } else {
            return (bool)(int)Mage::getStoreConfig(self::XML_PATH_SHOW_MIN_MAX_DETAILS_PAGE);
        }
    }
    /**
     * Use minimum rental period on listing page
     * @param int $_storeId
     * @return bool
     * */
    public function useMinimumRentalPeriod($_storeId = null)
    {
        if ($_storeId) {
            return (bool)(int)Mage::getStoreConfig(self::XML_PATH_USE_MINIMUM_RENTAL_PERIOD, $_storeId);
        } else {
            return (bool)(int)Mage::getStoreConfig(self::XML_PATH_USE_MINIMUM_RENTAL_PERIOD);
        }
    }
	 /**
     * Hide time period numbers on product info page
     * @param int $_storeId
     * @return bool
     * */
	public function hideTimePeriodNumbers($_storeId = null)
    {
        if ($_storeId) {
            return (bool)Mage::getStoreConfig(self::XML_PATH_HIDE_TIME_PERIOD_NUMBERS, $_storeId);
        } else {
            return (bool)Mage::getStoreConfig(self::XML_PATH_HIDE_TIME_PERIOD_NUMBERS);
        }
    }

    public function isBuyout($product)
    {
        if(is_object($product)){
            $productId = $product->getId();
        }else{
            $productId = $product;
        }

        $enableBuyout = ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($productId, 'payperrentals_enable_buyout');

        return $enableBuyout;
    }

    /**
     * Returns true if store has short time format

     * @param null $storeId
     *
     * @return bool|mixed
     */

    public static function isShortTimeFormat($storeId = null){
            if (Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_STORE_TIME_FORMAT, $storeId) == '12h'){
                return true;
            }
        return false;
    }


    /**
     * Returns if item has extend enable.
     * If product is bundle or configurable it will use product config not the associated products configs.
     * @param      $productId
     * @param null $storeId
     *
     * @return bool|mixed
     */

    public static function hasExtendEnabled($productId, $storeId = null){
        if (isset($productId) && ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($productId, 'enable_extend_order') == 2) {
            return false;
        } else if (isset($productId) && ITwebexperts_Payperrentals_Helper_Data::getAttributeCodeForId($productId, 'enable_extend_order') == 1) {
            return true;
        }else{
              return Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_ENABLE_EXTEND, $storeId);
        }
    }

    /**
     * Returns maximum extension length in second
     * @param null $storeId
     *
     * @return int
     */

    public static function getMaximumExtensionLength($storeId = null){
        if(Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_MAX_EXTENSION_NUMBER, $storeId) != '') {
            ITwebexperts_Payperrentals_Helper_Data::getPeriodInSeconds(
                Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_MAX_EXTENSION_NUMBER, $storeId),
                Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_MAX_EXTENSION_TYPE, $storeId)
            );
        }else{
            return 10000000000;
        }
    }

    public static function showTurnovers($storeId = null){
        if ($storeId) {
            return (bool)(int)Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_TURNOVER_SHOW, $storeId);
        } else {
            return (bool)(int)Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_TURNOVER_SHOW);
        }
    }

    public static function isChargedDeposit($storeId = null)
    {
        if ($storeId) {
            return (bool)(int)Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_DEPOSIT_TYPE, $storeId);
        } else {
            return (bool)(int)Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_DEPOSIT_TYPE);
        }
    }

    public static function showBuyoutPrice($storeId = null)
    {
        if ($storeId) {
            return (bool)(int)Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_SHOW_BUYOUT_PRICE, $storeId);
        } else {
            return (bool)(int)Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_SHOW_BUYOUT_PRICE);
        }
    }

    public static function keepListingPriceAfterDatesSelection($storeId = null)
    {
        if ($storeId) {
            return (bool)(int)Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_KEEP_LISTING_PRICE_AFTER_SELECTION, $storeId);
        } else {
            return (bool)(int)Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_KEEP_LISTING_PRICE_AFTER_SELECTION);
        }
    }

    public static function disableClickingRentIfDatesNotSelected($storeId = null)
    {
        if ($storeId) {
            return (bool)(int)Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_DISABLE_RENT_ON_GLOBAL_IF_DATES_NOT_SELECTED, $storeId);
        } else {
            return (bool)(int)Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_DISABLE_RENT_ON_GLOBAL_IF_DATES_NOT_SELECTED);
        }
    }

    public static function keepSelectedDays($storeId = null)
    {
        if ($storeId) {
            return (bool)(int)Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_USE_GLOBAL_DAYS, $storeId);
        } else {
            return (bool)(int)Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_USE_GLOBAL_DAYS);
        }
    }



    public static function showCalendarOnProductInfo($storeId = null){
        if ($storeId) {
            return (bool)(int)Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_SHOW_CALENDAR_PRODUCT_INFO, $storeId);
        } else {
            return (bool)(int)Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_SHOW_CALENDAR_PRODUCT_INFO);
        }
        //return true;
    }

    public static function showNextAvailableDateOnListing($storeId = null){
        if ($storeId) {
            return (bool)(int)Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_SHOW_NEXT_AVAILABLE_DATE_ON_LISTING, $storeId);
        } else {
            return (bool)(int)Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_SHOW_NEXT_AVAILABLE_DATE_ON_LISTING);
        }
    }

    public static function showNextAvailableDateOnView($storeId = null){
        if ($storeId) {
            return (bool)(int)Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_SHOW_NEXT_AVAILABLE_DATE_ON_VIEW, $storeId);
        } else {
            return (bool)(int)Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_SHOW_NEXT_AVAILABLE_DATE_ON_VIEW);
        }
    }

    public static function excludeDisabledDaysOfWeekFromTurnover($storeId = null){
        if ($storeId) {
            return (bool)(int)Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_EXCLUDE_DISABLED_DAYS_FROM_TURNOVER, $storeId);
        } else {
            return (bool)(int)Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_EXCLUDE_DISABLED_DAYS_FROM_TURNOVER);
        }
    }

    public static function getMinQtyAllowed($storeId = null){
        if ($storeId) {
            return (bool)(int)Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_MIN_QTY_ALLOWED, $storeId);
        } else {
            return (bool)(int)Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_MIN_QTY_ALLOWED);
        }
    }

    public static function getMaxQtyAllowed($storeId = null){
        if ($storeId) {
            return (bool)(int)Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_MAX_QTY_ALLOWED, $storeId);
        } else {
            return (bool)(int)Mage::getStoreConfig(ITwebexperts_Payperrentals_Helper_Config::XML_PATH_MAX_QTY_ALLOWED);
        }
    }


}