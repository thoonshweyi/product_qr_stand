<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OnlineProductExportDataService
{
    public function prepare(Collection $products): Collection
    {
        $productCodes = $products
            ->pluck('product_code')
            ->filter()
            ->values();

        $onlinedatas = $this->getOnlineDatas($productCodes);
        // dd($onlinedatas);

        return $products->map(function ($product) use ($onlinedatas) {
            $onlinedata = $onlinedatas->get(
                $product->product_code
            );
            // dd($onlinedata);

            return [
                'product' => $product,
                'onlinedata' => $onlinedata ?? null,
            ];
        });
    }

    public function getOnlineDatas($productCodes){
        $conn = DB::connection('master_product');

        $productCodesString = implode("','", $productCodes->toArray());

        $products = $conn->select("
            select  prod.product_code,coalesce(regexp_replace(prod.product_name1, E'[\\n\\r]+',' ', 'g' ),'')as product_name
                        ,coalesce(regexp_replace(prod.product_name1, E'[\\n\\r]+',' ', 'g' ),'')as product_name_mm,product_brand_name as brand_name
                        ,cat.location_catname as category_name,coalesce(cat.product_category_name,'-') as sub_category_name,coalesce(subcat.product_group_name,'-') as product_group_name
                        ,coalesce(class.product_pattern_name,'-') as product_pattern_name,product_type_name as product_type_name
                        ,product_unit_name as Unit,bar.barcode_code as item_code,barcode_bill_name as item_name,product_price1 as item_sell_price,product_price2 as item_member_price
                    from master_data.master_product prod 
                        left join master_data.master_product_category cat on prod.product_category_id = cat.product_category_id
                        left join master_data.master_product_group subcat on prod.product_group_id = subcat.product_group_id
                        left join master_data.master_product_pattern class on prod.product_pattern_id = class.product_pattern_id -- class
                        left join master_data.master_product_design subclass on prod.product_design_id = subclass.product_design_id -- sub-class
                        left join master_data.master_product_multiunit mulunit on prod.product_id= mulunit.product_id and prod.product_code= mulunit.product_code
                        left join master_data.master_product_unit unit on mulunit.product_unit_id= unit.product_unit_id
                        left join master_data.master_product_brand bd on prod.product_brand_id= bd.product_brand_id
                        left join  master_data.master_product_type gd on prod.product_type_id= gd.product_type_id
                        inner join master_data.master_product_barcode bar on prod.product_id= bar.product_id
                        and mulunit.product_unit_id= bar.product_unit_id
                left join  master_data.master_product_multiprice pr on pr.barcode_code=bar.barcode_code
                    where prod.inactive = 'A'
                    and bar.barcode_code in ('$productCodesString')
            and pr.branch_id='1'
        ");
        
        return collect($products)->keyBy('item_code');
    }
}