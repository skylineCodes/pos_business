<?php

namespace App\Utils;

use App\Models\Business;

class BusinessUtils extends Utils
{
    /**
     * Adds a default settings/resources for a new business
     *
     * @param int $business_id
     * @param int $user_id
     * 
     * @return boolean
     */
    public function newBusinessDefaultResources($business_id, $user_id)
    {
        $user = User::findOrFail($user_id);

        // Create Admin role and assign to user
        $role = Role::create([
            'name' => 'Admin#' . $business_id,
            'business_id' => $business_id,
            'guard_name' => 'web', 'is_default' => 1
        ]);

        $user->assignRole($role->name);

        //Create Cashier role for a new business
        $cashier_role = Role::create([
            'name' => 'Cashier#' . $business_id,
            'business_id' => $business_id,
            'guard_name' => 'web'
        ]);
        $cashier_role->syncPermissions(['sell.view', 'sell.create', 'sell.update', 'sell.delete', 'access_all_locations']);
    }

    /**
     * Creates new business with default settings
     * 
     * @return array
     */
    public function createNewBusiness($business_details)
    {
        $business_details['sell_price_tax'] = 'includes';

        $business_details['default_profit_percent'] = 25;

        // Add POS shortcuts
        $business_details['keyboard_shortcuts'] = '{"pos":{"express_checkout":"shift+e","pay_n_ckeckout":"shift+p","draft":"shift+d","cancel":"shift+c","edit_discount":"shift+i","edit_order_tax":"shift+t","add_payment_row":"shift+r","finalize_payment":"shift+f","recent_product_quantity":"f2","add_new_product":"f4"}}';

        // Add Prefixes
        $business_details['ref_no_prefixes'] = [
            'purchase' => 'PO',
            'stock_transfer' => 'ST',
            'stock_adjustment' => 'SA',
            'sell_return' => 'CN',
            'expense' => 'EP',
            'contacts' => 'CO',
            'purchase_payment' => 'PP',
            'sell_payment' => 'SP',
            'business_location' => 'BL'
        ];

        //Disable inline tax editing
        $business_details['enable_inline_tax'] = 0;

        $business = Business::create_business($business_details);

        return $business;
    }
}