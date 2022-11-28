<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebController extends Controller
{
    //
    protected $pageDefault = 40;
    public function index()
    {
        $type = request()->btype ? B_TYPE : C_TYPE;
        $code = request()->btype ? request()->btype : request()->ctype;
        if (!$code || !$type) return redirect()->to('/');

        $dataFilter = collect(request()->all())->except('btype', 'ctype')->toArray();
        $dataFilter['page'] = 1;

        $motoCategory = new \MotoCategory();
        $category = $motoCategory->getMotoCategory($code, $type);

        $categoryMotoChar = get_all_const_in_class('MotoChar');
        $categoryMotoDisplacement = get_all_const_in_class('MotoDisplacement');

        $listMakerCode = DB::table('tbl_category_maker')
            ->where('category_code', $code)
            ->where('category_type', $type)
            ->pluck('maker_code')
            ->toArray();

        $listMaker = DB::table('mst_model_maker')
            ->leftJoin('mst_model_v2', function($join) use ($category){
                $join->on('mst_model_v2.model_maker_code', '=', 'mst_model_maker.model_maker_code')
                    ->where('mst_model_v2.' . $category['colmn'], '=', '1');
            })
            ->selectRaw('mst_model_maker.model_maker_code, IFNULL(SUM(mst_model_v2.model_count),0) AS model_count, mst_model_maker.model_maker_hyouji')
            ->whereIn('mst_model_maker.model_maker_code', $listMakerCode)
            ->groupBy('mst_model_maker.model_maker_code')
            ->orderBy('mst_model_maker.model_maker_select_view_no')
            ->get();

//        dd($listMakerCode);

        $queryProducts = DB::table('mst_model_v2')
            ->where( $category['colmn'], 1)
            ->where('model_count', '>', 0);


        $queryProducts = $this->filter($queryProducts, $dataFilter);
        $products = $queryProducts->paginate($this->pageDefault, ['*'], 'page', 1);

        $totalCar = [
            'total_model' => $queryProducts->count('model_code'),
            'total_bike' => $queryProducts->sum('model_count')
        ];

        $enable = [
            'enableKanaPrefix' => $this->getEnable($queryProducts, 'model_kana_prefix'),
            'enableNamePrefix' => $this->getEnable($queryProducts, 'model_name_prefix'),
            'enableDisplacement' => $this->getEnable($queryProducts, 'model_displacement'),
            'enableMakerCode' => $this->getEnable($queryProducts, 'model_maker_code')
        ];

        $active = [
            'activeKanaPrefix' => explode('-', request()->model_kana_prefix) ?? [],
            'activeNamePrefix' => explode('-', request()->model_name_prefix) ?? [],
            'activeDisplacement' => explode('-', request()->model_displacement) ?? [],
            'activeMakerCode' => explode('-', request()->model_maker_code) ?? []
        ];

        return view('index', compact(
            'category', 'categoryMotoChar', 'categoryMotoDisplacement', 'totalCar', 'listMaker', 'enable', 'active', 'products'
        ));

    }

    public function loadData(Request $request)
    {

        $data = $request->all();

        $queryData = DB::table('mst_model_v2')
            ->where('mst_model_v2.' . $data['categoryColumn'], '=', 1)
            ->where('model_count', '>', 0);

        $queryProducts = $this->filter($queryData, $data);
        $products = $queryProducts->paginate($this->pageDefault, ['*'], 'page', $data['page']);

        $jsonData = view('components.load-data',compact('products'))->render();

        return response()->json($jsonData);
    }

    public function filter($queryData, $data)
    {
        $filterBetWeen = ['model_displacement'];
        foreach (collect($data)->only('model_kana_prefix', 'model_name_prefix', 'model_displacement', 'model_maker_code') as $key => $item) {
            $item = explode('-', $item);
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

        return $queryData;

    }

    public function getEnable($queryData, $model)
    {
        return $queryData->where($model, '<>', '')
            ->groupBy($model)
            ->pluck($model)
            ->toArray();
    }

    public function handleUrl()
    {
        dd('done');
    }
}
