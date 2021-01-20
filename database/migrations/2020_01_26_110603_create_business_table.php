<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 256);
            $table->bigInteger('currency_id')->unsigned();
            $table->date('start_date')->nullable();
            $table->string('tax_number_1', 100);
            $table->string('tax_label_1', 10);
            $table->string('tax_number_2', 100)->nullable();
            $table->string('tax_label_2', 10)->nullable();
            $table->bigInteger('default_sales_tax')->unsigned()->nullable();
            $table->float('default_profit_percent', 5, 2)->default();
            $table->bigInteger('owner_id')->unsigned();
            $table->string('time_zone')->default('Africa/Lagos');
            $table->tinyInteger('fy_start_month')->default(1);
            $table->enum('accounting_method', ['fifo', 'lifo', 'avco'])->default('fifo');
            $table->decimal('default_sales_discount', 5, 2)->nullable();
            $table->enum('sell_price_tax', ['includes', 'excludes'])->default('includes');
            $table->string('logo')->nullable();
            $table->string('sku_prefix')->nullable();
            $table->boolean('enable_product_expiry')->default(0);
            $table->enum('expiry_type', ['add_expiry', 'add_manufacturing'])->default('add_expiry');
            $table->enum('on_product_expiry', ['keep_selling', 'stop_selling', 'auto_delete'])->default('stop_selling');
            $table->bigInteger('stop_selling_before')->comment('Stop selling expired item n days before expiry');
            $table->boolean('enable_tooltip')->default(1);
            $table->boolean('purchase_in_diff_currency')->default(0)->comment("Allow purchase to be in different currency then the business currency");
            $table->bigInteger('purchase_currency_id')->unsigned()->nullable();
            $table->decimal('p_exchange_rate', 5, 3)->default(1)->comment("1 Purchase currency = ? Base Currency");
            $table->bigInteger('transaction_edit_days')->unsigned()->default(30);
            $table->bigInteger('stock_expiry_alert_days')->unsigned()->default(30);
            $table->text('keyboard_shortcuts')->nullable();
            $table->text('pos_settings')->nullable();
            $table->boolean('enable_brand')->default(true);
            $table->boolean('enable_category')->default(true);
            $table->boolean('enable_sub_category')->default(true);
            $table->boolean('enable_price_tax')->default(true);
            $table->boolean('enable_purchase_status')->nullable()->default(true);
            $table->boolean('enable_lot_number')->default(false);
            $table->bigInteger('default_unit')->nullable();
            $table->boolean('enable_sub_units')->default(false);
            $table->boolean('enable_racks')->default(false);
            $table->boolean('enable_row')->default(false);
            $table->boolean('enable_position')->default(false);
            $table->boolean('enable_editing_product_from_purchase')->default(1);
            $table->enum('sales_cmsn_agnt', ['logged_in_user', 'user', 'cmsn_agnt'])->nullable();
            $table->boolean('item_addition_method')->default(1);
            $table->boolean('enable_inline_tax')->default(1);
            $table->enum('currency_symbol_placement', ['before', 'after'])->default('before');
            $table->text('enabled_modules')->nullable();
            $table->string('date_format')->default('m/d/Y');
            $table->enum('time_format', [12, 24])->default(24);
            $table->text('ref_no_prefixes')->nullable();
            $table->char('theme_color', 20)->nullable();
            $table->string('created_by')->nullable();
            $table->boolean('enable_rp')->default(0)->comment('rp is the short form of reward points');
            $table->string('rp_name')->nullable()->comment('rp is the short form of reward points');
            $table->decimal('amount_for_unit_rp', 22, 4)->default(1)->comment('rp is the short form of reward points');
            $table->decimal('min_order_total_for_rp', 22, 4)->default(1)->comment('rp is the short form of reward points');
            $table->integer('max_rp_per_order')->nullable()->comment('rp is the short form of reward points');
            $table->decimal('redeem_amount_per_unit_rp', 22, 4)->default(1)->comment('rp is the short form of reward points');
            $table->decimal('min_order_total_for_redeem', 22, 4)->default(1)->comment('rp is the short form of reward points');
            $table->integer('min_redeem_point')->nullable()->comment('rp is the short form of reward points');
            $table->integer('max_redeem_point')->nullable()->comment('rp is the short form of reward points');
            $table->integer('rp_expiry_period')->nullable()->comment('rp is the short form of reward points');
            $table->enum('rp_expiry_type', ['month', 'year'])->default('year')->comment('rp is the short form of reward points');
            $table->text('email_settings')->nullable();
            $table->text('sms_settings')->nullable();
            $table->timestamps();

            $table->foreign('currency_id')->references('id')->on('currencies');
            $table->foreign('purchase_currency_id')->references('id')->on('currencies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('business');
    }
}
