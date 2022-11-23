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

        $queryProducts = DB::table('mst_model_v2')
            ->where('mst_model_v2.' . $category['colmn'], '=', 1)
            ->where('model_count', '>', 0);


       $enableKanaPrefix = $this->getEnable($queryProducts, 'model_kana_prefix');
       $enableNamePrefix = $this->getEnable($queryProducts, 'model_name_prefix');
       $enableDisplacement = $this->getEnable($queryProducts, 'model_displacement');
       $enableMakerCode = $this->getEnable($queryProducts, 'model_maker_code');

        return view('index', compact(
            'category', 'categoryMotoChar', 'categoryMotoDisplacement', 'totalCar', 'listMaker', 'enableKanaPrefix',
            'enableKanaPrefix', 'enableNamePrefix', 'enableDisplacement', 'enableMakerCode'
        ));

    }

    public function loadData(Request $request)
    {

        $data = $request->all();

        $queryData = DB::table('mst_model_v2')
            ->where('mst_model_v2.' . $data['categoryColumn'], '=', 1)
            ->where('model_count', '>', 0);

        $products = $this->filter($queryData, $data);


        $jsonData = view('components.load-data',compact('products'))->render();

        return response()->json($jsonData);
    }

    public function filter($queryData, $data)
    {
        $filterBetWeen = ['model_displacement'];
        foreach (collect($data)->except('categoryColumn', 'page') as $key => $item) {
            if (in_array($key, $filterBetWeen)) {
                foreach ($item as $index => $val) {
                    $arrVal = explode('_', $val);
                    if($index === 0){
                        $queryData->whereBetween($key, [ $arrVal[0], $arrVal[1] ]);
                    }else{
                        $queryData->orWhereBetween($key, [ $arrVal[0], $arrVal[1] ]);
                    }
                }
            } else {
                $queryData->whereIn($key, $item);
            }
        }

        return $queryData->paginate($this->pageDefault, ['*'], 'page', $data['page']);

    }

    public function getEnable($queryData, $model)
    {
        return $queryData->where($model, '<>', '')
                            ->groupBy($model)
                            ->pluck($model)
                            ->toArray();
    }
}
