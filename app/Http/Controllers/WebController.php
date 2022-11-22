<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WebController extends Controller
{
    //
    protected $pageDefault = 40;
    public function index($code, $type)
    {

        if (!$code || !$type) return redirect()->to('/');

        $motoCategory = new \MotoCategory();
        $category = $motoCategory->getMotoCategory($code, $type);

        $totalCar = DB::table('mst_model_v2')
                    ->selectRaw('COUNT(model_code) as total_model ,SUM(model_count) as total_bike')
                    ->where($category['colmn'], 1)
                    ->first();

        $categoryMotoChar = get_all_const_in_class('MotoChar');
        $categoryMotoDisplacement = get_all_const_in_class('MotoDisplacement');

        $listMakerCode = DB::table('tbl_category_maker')
                            ->where('category_code', $code)
                            ->where('category_type', $type)
                            ->pluck('maker_code')
                            ->toArray();

        $listMaker = DB::table('mst_model_maker')
                        ->leftJoin('mst_model_v2', function($join) use ($category){
                            $join->on('mst_model_v2.model_maker_code', '=', 'mst_model_maker.model_maker_code');
                        })
                        ->selectRaw('mst_model_maker.model_maker_code, IFNULL(SUM(mst_model_v2.model_count),0) AS model_count, mst_model_maker.model_maker_hyouji')
                        ->where('mst_model_v2.' . $category['colmn'], '=', '1')
                        ->whereIn('mst_model_maker.model_maker_code', $listMakerCode)
                        ->groupBy('mst_model_maker.model_maker_code')
                        ->orderBy('mst_model_maker.model_maker_select_view_no')
                        ->get();

        return view('index', compact('category', 'categoryMotoChar', 'categoryMotoDisplacement', 'totalCar', 'listMaker'));

    }

    public function loadData(Request $request)
    {

        $data = $request->all();

        $queryData = DB::table('mst_model_v2')
            ->where('mst_model_v2.' . $data['categoryColumn'], '=', $data['page'])
            ->where('model_count', '>', 0);

        $products = $this->filter($queryData, $data);

        $jsonData = view('components.load-data',compact('products'))->render();

        return response()->json($jsonData);
    }

    public function filter($queryData, $data)
    {
        foreach (collect($data)->except('categoryColumn', 'page') as $key => $item) {
            $queryData->whereIn($key, $item);
        }

        return $queryData->paginate($this->pageDefault, ['*'], 'page', $data['page']);

    }
}
