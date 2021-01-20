<?php

namespace App\Utils;

use Illuminate\Support\Carbon;
use App\Models\ReferenceCount;

class Utils
{
    /**
     * This function unformats a number and returns them in plain eng format
     *
     * @param int $input_number
     *
     * @return float
     */
    public function num_uf($input_number, $currency_details = null)
    {
        $thousand_separator = '';
        $decimal_separator = '';

        if (!empty($currency_details)) {
            $thousand_separator = $currency_details->thousand_separator;
            $decimal_separator = $currency_details->decimal_separator;
        }

        $num = str_replace($thousand_separator, '', $input_number);
        $num = str_replace($decimal_separator, '.', $num);

        return (float) $num;
    }

    /**
     * This function formats a number and returns them in specified format
     *
     * @param int $input_number
     * @param boolean $add_symbol = false
     * @param array $business_details = null
     * @param boolean $is_quantity = false; If number represents quantity
     *
     * @return string
     */
    public function num_f($input_number, $add_symbol = false, $business_details = null, $is_quantity = false)
    {
        $thousand_separator = !empty($business_details) ? $business_details->thousand_separator : ','; // TODO: Change the coma to model class function
        $decimal_separator = !empty($business_details) ? $business_details->decimal_separator : '.'; // TODO: Change the coma to model class function

        $currency_precision = config('constants.currency_precision', 2);

        if ($is_quantity) {
            $currency_precision = config('constants.quantity_precision', 2);
        }

        $formatted = number_format($input_number, $currency_precision, $decimal_separator, $thousand_separator);

        // if ($add_symbol) {
        //     $currency_symbol_placement = !empty($business_details) ? $business_details->currency_symbol_placement : session('business.currency_symbol_placement');
        //     $symbol = !empty($business_details) ? $business_details->currency_symbol : session('currency')['symbol'];

        //     if ($currency_symbol_placement == 'after') {
        //         $formatted = $formatted . ' ' . $symbol;
        //     } else {
        //         $formatted = $symbol . ' ' . $formatted;
        //     }
        // }

        return $formatted;
    }

    /**
     * Calculates percentage for a given number
     *
     * @param int $number
     * @param int $percent
     * @param int $addition default = 0
     *
     * @return float
     */
    public function calc_percentage($number, $percent, $addition = 0)
    {
        return ($addition + ($number * ($percent / 100)));
    }

    /**
     * Calculates base value on which percentage is calculated
     *
     * @param int $number
     * @param int $percent
     *
     * @return float
     */
    public function calc_percentage_base($number, $percent)
    {
        return ($number * 100) / (100 + $percent);
    }

    /**
     * Calculates percentage
     *
     * @param int $base
     * @param int $number
     *
     * @return float
     */
    public function get_percent($base, $number)
    {
        if ($base == 0) {
            return 0;
        }

        $diff = $number - $base;
        return ($diff / $base) * 100;
    }

    /**
     * Converts date in business format to mysql format
     *
     * @param string $date
     * @param bool $time (default = false)
     * @return string
     */
    public function uf_date($date, $time = false)
    {
        $date_format = 'm/d/Y';
        $mysql_format = 'Y-m-d H:i:s';
        // if ($time) {
        //     if ($business_id->time_format == 12) {
        //         $date_format = $date_format . ' h:i A';
        //     } else {
        //         $date_format = $date_format . 'H:i';
        //     }

        //     $mysql_format = 'Y-m-d H:i:s';
        // }

        return Carbon::createFromFormat($date_format, $date)->format($mysql_format);
    }

    /**
     * Convert time in business format to mysql format
     *
     * @param string $time
     * @return string
     */
    public function uf_time($time, $business_id)
    {
        $time_format = 'H:i';
        if ($business_id->time_format == 12) {
            $time_format = 'h:i A';
        }

        return Carbon::createFromFormat($time_format, $time)->format('H:i');
    }

    /**
     * Converts time in business format to mysql format
     *
     * @param string $time
     * @return string
     */
    public function format_time($time, $business_id)
    {
        $time_format = 'H:i';
        if ($business_id->time_format == 12) {
            $time_format = 'h:i A';
        }

        return Carbon::createFromFormat('H:i:s', $time)->format($time_format);
    }

    /**
     * Converts date in mysql format to business format
     *
     * @param string $date
     * @param bool $time (default = false)
     * @return string
     */
    public function format_date($date, $show_time = false, $business_details = null, $business_id)
    {
        $format = !empty($business_details) ? $business_details->date_format : $business_id->date_format;
        if (!empty($show_time)) {
            $time_format = !empty($business_details) ? $business_details->date_format : $business_id->date_format;
            if ($time_format == 12) {
                $format .= ' h:i A';
            } else {
                $format .= ' H:i';
            }
        }

        return Carbon::createFromTimestamp(strtotime($date))->format($format);
    }

    /**
     * Increments reference count for a given type and given business
     * and gives the updated reference count
     *
     * @param string $type
     * @param int $business_id
     *
     * @return int
     */
    public function setAndGetReferenceCount($type, $business_id)
    {
        $ref = ReferenceCount::where('ref_type', $type)
                             ->where('business_id', $business_id)
                             ->first();

        if (!empty($ref)) {
            $ref->ref_count += 1;
            $ref->save();

            return $ref->ref_count;
        } else {
            $new_ref = ReferenceCount::create([
                'ref_type' => $type,
                'business_id' => $business_id,
                'ref_count' => 1
            ]);

            return $new_ref->ref_count;
        }
    }

    /**
     * Returns the list of barcode types
     *
     * @return array
     */
    public function barcode_types()
    {
        $types = [ 'C128' => 'Code 128 (C128)', 'C39' => 'Code 39 (C39)', 'EAN13' => 'EAN-13', 'EAN8' => 'EAN-8', 'UPCA' => 'UPC-A', 'UPCE' => 'UPC-E'];

        return $types;
    }

    /**
     * Uploads document to the server if present in the request
     * @param obj $request, string $file_name, string dir_name
     * 
     * @return string
     */
    public function uploadFile($request, $file_name, $dir_name)
    {
        // If app environment is demo return null
        if (config('app.env') == 'demo') {
            return null;
        }

        $uploaded_file_name = null;
        if ($request->hasFile($file_name) && $request->file($file_name)->isValid()) {
            if ($request->file_name->getSize() <= config('constants.document_size_limit')) {
                $new_file_name = time() . '_' . $request->$file_name->getClientOriginalName();
                if ($request->$file_name->storeAs($dir_name, $new_file_name)) {
                    $uploaded_file_name = $new_file_name;
                }
            }
        }
        return $uploaded_file_name;
    }
}
