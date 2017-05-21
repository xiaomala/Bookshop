<?php


namespace App\Http\Controllers\service;

use Illuminate\Support\Facades\Log;
use App\Models\Category;
use App\Models\M3Result;
use Illuminate\http\Request;
use App\Models\PdtContent;
use App\Models\PdtImages;
use App\Models\Product;
use App\Models\Cart;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{


    /**
     * 分类
     * @return $this
     */
    public function Category()
    {

        $categorys = Category::where('parent_id','=','0')->get();

        //Log::info( $categorys );

        return view('category/category')->with('categorys', $categorys);
    }



    /**
     * 下级类型
     * @param $parent_id
     * @return string
     */
    public function getCategoryByParentId( $parent_id )
    {

        $categorys = Category::where('parent_id',$parent_id)->get();

        $m3_result = new M3Result();
        $m3_result->status = 0;
        $m3_result->message = '返回成功';
        $m3_result->categorys = $categorys;

        return $m3_result->toJson();
    }




    /**
     * 产吕列表
     */
    public function toProduct( $category_id )
    {
        $products = Product::where('category_id','=', $category_id)->get();
        return view('category/product')->with('products', $products);
    }



    /**
     * 产品详情
     */
    public function toPdtContent( Request $request, $product_id )
    {
        $product = Product::find( $product_id );
        $pdt_content = PdtContent::where('product_id','=',$product_id)->first();
        $pdt_images = PdtImages::where('product_id','=',$product_id)->get();

        $count = 0;

        $member = $request->session()->get('member', '');
        if($member != '') {

            $cart_items = Cart::where('member_id', $member->id)->get();

            foreach ($cart_items as $cart_item) {
                if($cart_item->product_id == $product_id) {
                    $count = $cart_item->count;
                    break;
                }
            }

        } else {

            $bk_cart = $request->cookie('bk_cart');
            $bk_cart_arr = ( $bk_cart != null ? explode( ',', $bk_cart ) : array());

            foreach ($bk_cart_arr as $value) {  //一定要传引用
                $index = strpos($value, ':');
                if (substr($value, 0, $index) == $product_id) {
                    $count = (int)substr($value, $index + 1);
                    break;
                }
            }
        }

        return view('category/pdt_content')->with('product', $product)
                                           ->with('pdt_content', $pdt_content)
                                           ->with('pdt_images', $pdt_images)
                                            ->with('count', $count);
    }

}